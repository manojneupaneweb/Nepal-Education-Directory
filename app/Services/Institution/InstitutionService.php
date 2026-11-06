<?php

namespace App\Services\Institution;

use App\Enums\InstitutionStatus;
use App\Enums\VerificationStatus;
use App\Models\Institution;
use App\Models\InstitutionCategory;
use App\Models\User;
use App\Services\Location\LocationConsistency;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InstitutionService
{
    public function __construct(
        private readonly InstitutionSlugger $slugger,
        private readonly LocationConsistency $locations,
    ) {}

    /**
     * @param  array<string, mixed>  $attributes
     * @param  list<int>  $categoryIds
     */
    public function create(array $attributes, array $categoryIds): Institution
    {
        $attributes['slug'] = $this->slugger->resolve($attributes['slug'] ?? null, $attributes['name']);
        $attributes['status'] = InstitutionStatus::Draft;
        $attributes['verification_status'] = VerificationStatus::Unverified;
        $attributes['published_at'] = null;

        return DB::transaction(function () use ($attributes, $categoryIds) {
            $institution = Institution::query()->create($attributes);
            $this->assertValid($institution, $categoryIds);
            $institution->categories()->sync($categoryIds);

            return $institution->refresh();
        });
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @param  list<int>  $categoryIds
     */
    public function update(Institution $institution, array $attributes, array $categoryIds): Institution
    {
        $attributes['slug'] = $this->slugger->resolve($attributes['slug'] ?? null, $attributes['name'], $institution);
        unset($attributes['status'], $attributes['verification_status'], $attributes['published_at']);

        return DB::transaction(function () use ($institution, $attributes, $categoryIds) {
            $institution->fill($attributes);
            $this->assertValid($institution, $categoryIds);
            $institution->save();
            $institution->categories()->sync($categoryIds);

            return $institution->refresh();
        });
    }

    public function transition(Institution $institution, InstitutionStatus $status, ?User $actor, ?string $reason = null): Institution
    {
        $allowed = match ($status) {
            InstitutionStatus::Pending => in_array($institution->status, [InstitutionStatus::Draft, InstitutionStatus::Rejected], true),
            InstitutionStatus::Published => $institution->status === InstitutionStatus::Pending,
            InstitutionStatus::Rejected => $institution->status === InstitutionStatus::Pending,
            InstitutionStatus::Archived => $institution->status !== InstitutionStatus::Archived,
            InstitutionStatus::Draft => in_array($institution->status, [InstitutionStatus::Draft, InstitutionStatus::Rejected, InstitutionStatus::Pending], true),
        };

        if (! $allowed) {
            throw ValidationException::withMessages([
                'status' => 'That status change is not available from the current publication status.',
            ]);
        }

        $previous = $institution->status;
        $institution->status = $status;
        $institution->published_at = $status === InstitutionStatus::Published ? now() : $institution->published_at;
        $institution->save();

        $institution->changeLogs()->create([
            'changed_by' => $actor?->id,
            'field_name' => 'status',
            'old_value' => ['status' => $previous->value],
            'new_value' => ['status' => $status->value],
            'change_reason' => $reason,
        ]);

        return $institution->refresh();
    }

    /**
     * @param  list<int>  $categoryIds
     */
    private function assertValid(Institution $institution, array $categoryIds): void
    {
        $institution->loadMissing(['type', 'province']);
        $errors = [];

        foreach ($this->locations->violations($institution) as $message) {
            $errors['municipality_id'] = $message;
        }

        if ($institution->province !== null && ! $institution->province->is_active) {
            $errors['province_id'] = 'The selected province is inactive.';
        }

        if ($institution->type !== null && ! $institution->type->is_active) {
            $errors['institution_type_id'] = 'The selected institution type is inactive.';
        }

        if ($this->parentCreatesCycle($institution)) {
            $errors['parent_institution_id'] = 'The parent institution would create a circular relationship.';
        }

        $activeCategories = InstitutionCategory::query()
            ->whereIn('id', $categoryIds)
            ->where('is_active', true)
            ->count();

        if ($activeCategories !== count(array_unique($categoryIds))) {
            $errors['category_ids'] = 'Each category must be an active category.';
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }

    private function parentCreatesCycle(Institution $institution): bool
    {
        $parentId = $institution->parent_institution_id;

        if ($parentId === null) {
            return false;
        }

        if ($institution->exists && (int) $parentId === (int) $institution->id) {
            return true;
        }

        $seen = [];
        $currentId = (int) $parentId;

        while ($currentId !== 0 && ! isset($seen[$currentId])) {
            if ($institution->exists && $currentId === (int) $institution->id) {
                return true;
            }

            $seen[$currentId] = true;
            $currentId = (int) Institution::query()->whereKey($currentId)->value('parent_institution_id');
        }

        return isset($seen[$currentId]) && $currentId !== 0;
    }
}

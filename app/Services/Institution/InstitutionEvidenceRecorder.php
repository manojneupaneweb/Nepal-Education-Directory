<?php

namespace App\Services\Institution;

use App\Enums\AccessMethod;
use App\Enums\ContactType;
use App\Enums\FieldReviewStatus;
use App\Enums\SourceType;
use App\Enums\VerificationStatus;
use App\Models\DataSource;
use App\Models\Institution;
use App\Models\InstitutionContact;
use App\Models\InstitutionFieldEvidence;
use App\Models\InstitutionSource;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InstitutionEvidenceRecorder
{
    /**
     * Store a sourced proposal. The institution record is not changed.
     */
    public function propose(Institution $institution, string $field, string $value, string $sourceUrl): InstitutionFieldEvidence
    {
        $field = $this->field($field);
        $value = $this->value($field, $value);
        $sourceUrl = $this->sourceUrl($sourceUrl);
        $this->assertSourceMatches($institution, $field, $value, $sourceUrl);

        $duplicate = $institution->fieldEvidence()
            ->where('field_name', $field)
            ->where('normalized_value', $value)
            ->where('review_status', FieldReviewStatus::Pending)
            ->exists();
        if ($duplicate) {
            throw ValidationException::withMessages([
                $field => 'A pending proposal for this value already exists.',
            ]);
        }

        $source = InstitutionSource::query()->firstOrCreate(
            [
                'institution_id' => $institution->id,
                'data_source_id' => $this->dataSource()->id,
                'source_url' => $sourceUrl,
            ],
            [
                'collected_at' => now(),
                'last_checked_at' => now(),
            ],
        );
        $source->forceFill(['last_checked_at' => now()])->save();

        return $institution->fieldEvidence()->create([
            'institution_source_id' => $source->id,
            'field_name' => $field,
            'extracted_value' => $value,
            'normalized_value' => $value,
            'review_status' => FieldReviewStatus::Pending,
        ]);
    }

    /**
     * Apply one reviewed proposal when the destination is empty.
     * A different stored value is left unchanged.
     */
    public function accept(InstitutionFieldEvidence $evidence, ?User $reviewer): InstitutionFieldEvidence
    {
        if ($evidence->review_status !== FieldReviewStatus::Pending) {
            throw ValidationException::withMessages([
                'review_status' => 'Only a pending proposal can be accepted.',
            ]);
        }

        $evidence->loadMissing(['institution', 'institutionSource']);
        $field = $this->field($evidence->field_name);
        $value = $this->value($field, (string) $evidence->normalized_value);

        DB::transaction(function () use ($evidence, $reviewer, $field, $value): void {
            $institution = Institution::query()->whereKey($evidence->institution_id)->lockForUpdate()->firstOrFail();
            $current = $this->current($institution, $field);

            if ($current !== null && $current !== $value) {
                throw ValidationException::withMessages([
                    $field => 'The institution already has a different value. The proposal was not applied.',
                ]);
            }

            if ($current === null) {
                $this->apply($institution, $field, $value, $evidence->institutionSource?->data_source_id);
                $institution->changeLogs()->create([
                    'changed_by' => $reviewer?->id,
                    'field_name' => $field,
                    'old_value' => [$field => null],
                    'new_value' => [$field => $value],
                    'change_reason' => 'Accepted sourced proposal '.$evidence->institutionSource?->source_url,
                ]);
                if ($institution->verification_status === VerificationStatus::Unverified) {
                    $institution->verification_status = VerificationStatus::SourceMatched;
                }
                $institution->save();
            }

            $evidence->forceFill([
                'review_status' => FieldReviewStatus::Accepted,
                'reviewed_by' => $reviewer?->id,
                'reviewed_at' => now(),
            ])->save();
        });

        return $evidence->refresh();
    }

    public function reject(InstitutionFieldEvidence $evidence, ?User $reviewer): InstitutionFieldEvidence
    {
        if ($evidence->review_status !== FieldReviewStatus::Pending) {
            throw ValidationException::withMessages([
                'review_status' => 'Only a pending proposal can be rejected.',
            ]);
        }

        $evidence->forceFill([
            'review_status' => FieldReviewStatus::Rejected,
            'reviewed_by' => $reviewer?->id,
            'reviewed_at' => now(),
        ])->save();

        return $evidence->refresh();
    }

    /**
     * @return list<string>
     */
    public function fields(): array
    {
        return ['official_website', 'address', 'description', 'phone', 'email', 'principal', 'education_levels', 'programs', 'facilities'];
    }

    private function field(string $field): string
    {
        $allowed = $this->fields();
        if (! in_array($field, $allowed, true)) {
            throw ValidationException::withMessages([
                'field' => 'That field cannot be stored from a proposal.',
            ]);
        }

        return $field;
    }

    private function value(string $field, string $value): string
    {
        $value = trim($value);
        $valid = match ($field) {
            'official_website' => filter_var($value, FILTER_VALIDATE_URL) !== false && preg_match('/^https?:\/\//i', $value) === 1,
            'phone' => preg_match('/\d(?:\D*\d){6,}/', $value) === 1,
            'email' => filter_var($value, FILTER_VALIDATE_EMAIL) !== false,
            'address' => mb_strlen($value) >= 8,
            'description' => mb_strlen($value) >= 40,
            'principal' => mb_strlen($value) >= 3,
            'education_levels', 'programs', 'facilities' => $this->lines($value) !== [],
            default => false,
        };

        if (! $valid) {
            throw ValidationException::withMessages([
                $field => 'The proposal has no usable value for this field.',
            ]);
        }

        return $value;
    }

    private function sourceUrl(string $sourceUrl): string
    {
        $sourceUrl = trim($sourceUrl);
        if (filter_var($sourceUrl, FILTER_VALIDATE_URL) === false || preg_match('/^https?:\/\//i', $sourceUrl) !== 1) {
            throw ValidationException::withMessages([
                'source_url' => 'A proposal needs an http or https source URL.',
            ]);
        }

        return $sourceUrl;
    }

    private function dataSource(): DataSource
    {
        return DataSource::query()->firstOrCreate(
            ['name' => 'Cited public page'],
            [
                'source_type' => SourceType::InstitutionWebsite,
                'base_url' => 'https://localhost',
                'authority_level' => 1,
                'access_method' => AccessMethod::Manual,
                'is_active' => true,
            ],
        );
    }

    private function current(Institution $institution, string $field): ?string
    {
        if (in_array($field, ['official_website', 'address', 'description'], true)) {
            $value = trim((string) $institution->{$field});

            return $value === '' ? null : $value;
        }

        if (in_array($field, ['education_levels', 'programs', 'facilities'], true)) {
            $lines = collect($institution->website_sections[$field] ?? [])->filter(fn ($line) => filled($line));

            return $lines->isEmpty() ? null : $lines->implode("\n");
        }

        $contact = $this->contact($institution, $field);

        return $contact === null ? null : trim($contact->contact_value);
    }

    private function apply(Institution $institution, string $field, string $value, ?int $dataSourceId): void
    {
        if (in_array($field, ['official_website', 'address', 'description'], true)) {
            $institution->{$field} = $value;

            return;
        }

        if (in_array($field, ['education_levels', 'programs', 'facilities'], true)) {
            $sections = $institution->website_sections ?? [];
            $sections[$field] = $this->lines($value);
            $institution->website_sections = $sections;

            return;
        }

        $type = $field === 'email' ? ContactType::Email : ContactType::Phone;
        $label = $field === 'principal' ? 'Named principal' : $field;
        $stored = $field === 'principal' ? 'Principal: '.$value : $value;

        $institution->contacts()->create([
            'contact_type' => $type,
            'label' => $label,
            'contact_value' => $stored,
            'is_primary' => false,
            'is_public' => true,
            'source_id' => $dataSourceId,
        ]);
    }

    private function assertSourceMatches(Institution $institution, string $field, string $value, string $sourceUrl): void
    {
        $sourceHost = $this->host($sourceUrl);
        if ($field === 'official_website' && $sourceHost !== $this->host($value)) {
            throw ValidationException::withMessages([
                'source_url' => 'The source page is not on the same site as the proposed website.',
            ]);
        }

        $stored = trim((string) $institution->official_website);
        if ($field !== 'official_website' && $stored !== '' && $this->host($stored) !== $sourceHost) {
            throw ValidationException::withMessages([
                'source_url' => 'The source page is not on this institution’s official website.',
            ]);
        }
    }

    private function host(string $url): string
    {
        $parts = parse_url($url);
        if (! is_array($parts) || isset($parts['user']) || isset($parts['pass'])) {
            throw ValidationException::withMessages([
                'source_url' => 'The source URL cannot include a username or password.',
            ]);
        }

        return preg_replace('/^www\./', '', strtolower((string) ($parts['host'] ?? ''))) ?? '';
    }

    /**
     * @return list<string>
     */
    private function lines(string $value): array
    {
        return collect(preg_split('/\r\n|\n/', $value) ?: [])
            ->map(fn ($line) => trim($line))
            ->filter(fn ($line) => mb_strlen($line) >= 2)
            ->unique()
            ->take(20)
            ->values()
            ->all();
    }

    private function contact(Institution $institution, string $field): ?InstitutionContact
    {
        $type = $field === 'email' ? ContactType::Email : ContactType::Phone;
        $query = $institution->contacts()->where('contact_type', $type);
        if ($field === 'principal') {
            $query->where('label', 'Named principal');
        }
        if ($field === 'phone') {
            $query->where(function ($contacts): void {
                $contacts->whereNull('label')->orWhere('label', '!=', 'Named principal');
            });
        }

        return $query->first();
    }
}

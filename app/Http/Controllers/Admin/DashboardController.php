<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\District;
use App\Models\Institution;
use App\Models\InstitutionSuggestion;
use App\Models\InstitutionType;
use App\Models\Municipality;
use App\Models\PageView;
use App\Models\ProjectError;
use App\Models\Province;
use App\Models\SearchKeyword;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $status = $this->countsFor(Institution::query(), 'status');
        $verification = $this->countsFor(Institution::query(), 'verification_status');
        $ownership = $this->countsFor(Institution::query(), 'ownership_type');

        return view('admin.dashboard', [
            'stats' => [
                'institutions' => Institution::query()->count(),
                'published' => $status['published'] ?? 0,
                'pending' => $status['pending'] ?? 0,
                'keywords' => SearchKeyword::query()->count(),
                'messages' => ContactMessage::query()->count(),
                'suggestions' => InstitutionSuggestion::query()->count(),
                'errors' => ProjectError::query()->open()->count(),
                'provinces' => Province::query()->count(),
                'districts' => District::query()->count(),
                'municipalities' => Municipality::query()->count(),
                'school_views' => PageView::query()->where('kind', 'school')->count(),
                'college_views' => PageView::query()->where('kind', 'college')->count(),
                'university_views' => PageView::query()->where('kind', 'university')->count(),
            ],
            'status' => $this->slices($status, [
                'published' => ['Published', '#2563EB'],
                'draft' => ['Draft', '#123B70'],
                'pending' => ['Pending', '#D97706'],
                'rejected' => ['Rejected', '#E63946'],
                'archived' => ['Archived', '#64748B'],
            ]),
            'verification' => $this->slices($verification, [
                'unverified' => ['Unverified', '#E2E8F0'],
                'source_matched' => ['Source matched', '#93C5FD'],
                'admin_reviewed' => ['Admin reviewed', '#2563EB'],
                'institution_verified' => ['Verified', '#16A34A'],
            ]),
            'ownership' => $this->slices($ownership, [
                'government' => ['Government', '#123B70'],
                'community' => ['Community', '#2563EB'],
                'private' => ['Private', '#E63946'],
                'public' => ['Public', '#16A34A'],
                '' => ['Not set', '#CBD5E1'],
            ]),
            'types' => InstitutionType::query()->withCount('institutions')->orderByDesc('institutions_count')->limit(6)->get(),
            'provinces' => Province::query()->withCount('institutions')->orderBy('name')->get(),
            'searches' => $this->dailyCounts(SearchKeyword::class),
            'monthly' => $this->monthlyInstitutions(),
            'inbox' => [
                ['Messages', ContactMessage::query()->count(), '#2563EB'],
                ['Suggestions', InstitutionSuggestion::query()->count(), '#E63946'],
                ['Keywords', SearchKeyword::query()->count(), '#123B70'],
                ['Open errors', ProjectError::query()->open()->count(), '#D97706'],
            ],
            'recentKeywords' => SearchKeyword::query()->latest()->limit(5)->get(),
            'recentMessages' => ContactMessage::query()->latest()->limit(5)->get(),
            'openErrors' => ProjectError::query()->open()->latest()->limit(5)->get(),
            'recentInstitutions' => Institution::query()->with('type')->latest()->limit(6)->get(),
            'suggestions' => InstitutionSuggestion::query()->latest()->limit(5)->get(),
        ]);
    }

    /**
     * @return array<string, int>
     */
    private function countsFor($query, string $column): array
    {
        return $query
            ->select($column, DB::raw('COUNT(*) as total'))
            ->groupBy($column)
            ->pluck('total', $column)
            ->mapWithKeys(fn ($total, $key) => [(string) $key => (int) $total])
            ->all();
    }

    /**
     * @param  array<string, int>  $counts
     * @param  array<string, array{0: string, 1: string}>  $labels
     * @return list<array{label: string, value: int, color: string}>
     */
    private function slices(array $counts, array $labels): array
    {
        $slices = [];

        foreach ($labels as $key => [$label, $color]) {
            $slices[] = [
                'label' => $label,
                'value' => $counts[$key] ?? 0,
                'color' => $color,
            ];
        }

        foreach ($counts as $key => $total) {
            if (! array_key_exists($key, $labels) && $key !== '') {
                $slices[] = ['label' => str_replace('_', ' ', $key), 'value' => $total, 'color' => '#64748B'];
            }
        }

        return $slices;
    }

    /**
     * @param  class-string  $model
     * @return list<array{label: string, value: int}>
     */
    private function dailyCounts(string $model): array
    {
        $rows = $model::query()
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->get(['created_at'])
            ->countBy(fn ($row) => $row->created_at->toDateString());

        return collect(range(6, 0))->map(function (int $daysAgo) use ($rows) {
            $date = now()->subDays($daysAgo);

            return [
                'label' => $date->format('D'),
                'value' => (int) ($rows[$date->toDateString()] ?? 0),
            ];
        })->all();
    }

    /**
     * @return list<array{label: string, value: int}>
     */
    private function monthlyInstitutions(): array
    {
        $expression = DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', created_at)"
            : "date_format(created_at, '%Y-%m')";

        $rows = Institution::query()
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->selectRaw($expression.' as month, COUNT(*) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        return collect(range(5, 0))->map(function (int $monthsAgo) use ($rows) {
            $month = now()->subMonths($monthsAgo);

            return [
                'label' => $month->format('M'),
                'value' => (int) ($rows[$month->format('Y-m')] ?? 0),
            ];
        })->all();
    }
}

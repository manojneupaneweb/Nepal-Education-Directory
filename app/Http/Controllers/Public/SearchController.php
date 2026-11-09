<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\CollegeStream;
use App\Models\Institution;
use App\Models\SearchKeyword;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $term = trim((string) $request->string('q'));
        $stream = $term === ''
            ? null
            : CollegeStream::query()->active()->where('name', $term)->first();

        $institutions = ($stream?->matchingInstitutions() ?? Institution::query()->published())
            ->with(['type', 'municipality', 'district'])
            ->when($stream === null && $term !== '', function ($query) use ($term) {
                $like = '%'.addcslashes($term, '%_\\').'%';
                $query->where(function ($query) use ($like) {
                    $query->where('name', 'like', $like)
                        ->orWhere('name_ne', 'like', $like)
                        ->orWhere('short_name', 'like', $like);
                });
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        if ($term !== '') {
            SearchKeyword::query()->create([
                'term' => mb_substr($term, 0, 255),
                'results_count' => $institutions->total(),
            ]);
        }

        return view('public.search', [
            'term' => $term,
            'institutions' => $institutions,
        ]);
    }
}

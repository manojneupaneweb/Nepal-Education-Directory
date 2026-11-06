<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $term = trim((string) $request->string('q'));
        $institutions = Institution::query()
            ->published()
            ->with(['type', 'municipality', 'district'])
            ->when($term !== '', function ($query) use ($term) {
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

        return view('public.search', [
            'term' => $term,
            'institutions' => $institutions,
        ]);
    }
}

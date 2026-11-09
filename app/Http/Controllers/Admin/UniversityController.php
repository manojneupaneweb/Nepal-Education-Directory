<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UniversityController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(auth()->user()?->is_admin, 403);

        $term = trim($request->string('q')->toString());

        return view('admin.universities.index', [
            'universities' => University::query()
                ->with(['province'])
                ->withCount(['institutions', 'faculties', 'programs'])
                ->when($term !== '', function ($query) use ($term) {
                    $like = '%'.addcslashes($term, '%_\\').'%';
                    $query->where(function ($query) use ($like) {
                        $query->where('name', 'like', $like)
                            ->orWhere('abbreviation', 'like', $like)
                            ->orWhere('name_ne', 'like', $like);
                    });
                })
                ->orderBy('name')
                ->paginate(20)
                ->withQueryString(),
            'term' => $term,
            'total' => University::query()->count(),
        ]);
    }
}

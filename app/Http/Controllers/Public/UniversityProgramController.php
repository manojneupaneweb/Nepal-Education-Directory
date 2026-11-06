<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\EducationLevel;
use App\Models\Program;
use App\Models\Province;
use App\Models\University;
use App\Models\UniversityFaculty;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UniversityProgramController extends Controller
{
    public function index(Request $request): View
    {
        $universityId = $request->integer('university') ?: null;
        $facultyId = $request->integer('faculty') ?: null;

        if ($universityId && $facultyId && ! UniversityFaculty::query()->whereKey($facultyId)->where('university_id', $universityId)->exists()) {
            $facultyId = null;
        }

        $programs = Program::query()
            ->whereNotNull('university_id')
            ->with(['university.province', 'educationLevel', 'faculty', 'institutions'])
            ->when($request->string('q')->toString(), function ($query, string $term) {
                $query->where(function ($inner) use ($term) {
                    $inner->where('name', 'like', "%{$term}%")
                        ->orWhere('discipline', 'like', "%{$term}%")
                        ->orWhereHas('university', fn ($university) => $university->where('name', 'like', "%{$term}%"));
                });
            })
            ->when($universityId, fn ($query) => $query->where('university_id', $universityId))
            ->when($request->integer('province'), function ($query, int $id) {
                $query->whereHas('university', fn ($university) => $university->where('province_id', $id));
            })
            ->when($request->integer('level'), fn ($query, int $id) => $query->where('education_level_id', $id))
            ->when($facultyId, fn ($query) => $query->where('university_faculty_id', $facultyId))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $faculties = UniversityFaculty::query()
            ->with('university:id,name,abbreviation')
            ->when($universityId, fn ($query) => $query->where('university_id', $universityId))
            ->orderBy('name')
            ->get();

        return view('public.university-programs.index', [
            'programs' => $programs,
            'universities' => University::query()->orderBy('name')->get(),
            'provinces' => Province::query()->active()->orderBy('name')->get(),
            'levels' => EducationLevel::query()->orderBy('sort_order')->get(),
            'faculties' => $faculties,
            'selectedUniversity' => $universityId,
            'selectedFaculty' => $facultyId,
        ]);
    }

    public function university(string $university): View
    {
        $record = University::query()->where('slug', $university)->first()
            ?? University::query()->where('slug', $university.'-university')->first();

        abort_unless($record, 404);

        $programs = $record->programs()
            ->with('faculty')
            ->orderBy('name')
            ->paginate(24)
            ->withQueryString();

        return view('public.university-programs.university', [
            'university' => $record->loadCount(['faculties', 'programs']),
            'programs' => $programs,
            'faculties' => $record->faculties()->orderBy('name')->get(),
        ]);
    }

    public function show(Program $program): View
    {
        abort_unless($program->university_id, 404);
        $program->load(['university.province', 'university.district', 'educationLevel', 'faculty', 'institutions', 'category']);

        return view('public.university-programs.show', [
            'program' => $program,
        ]);
    }
}

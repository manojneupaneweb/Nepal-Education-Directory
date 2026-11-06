@extends('layouts.public')

@section('title', 'University academic programs in Nepal — '.config('app.name'))
@section('meta_description', 'Academic programs offered by universities in Nepal, with degree level, university, and the official source used to record each program.')
@section('canonical', route('university-programs.index'))

@section('content')
<section class="bg-gradient-to-br from-brand-900 via-brand-800 to-brand-700 py-10 text-white">
    <div class="mx-auto max-w-6xl px-4">
        <nav class="text-sm text-brand-100" aria-label="Breadcrumb">
            <a class="hover:text-white" href="{{ route('home') }}">Home</a>
            <span aria-hidden="true"> / </span>
            <a class="hover:text-white" href="{{ route('universities.index') }}">Universities</a>
            <span aria-hidden="true"> / </span>
            <span class="text-white">Academic programs</span>
        </nav>
        <h1 class="mt-4 text-3xl font-extrabold tracking-tight sm:text-4xl">University academic programs</h1>
        <p class="mt-3 max-w-3xl text-brand-100">Search by university and faculty. A program is listed only when a public source names it.</p>
        <p class="mt-6 inline-flex rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-brand-800 shadow-md">{{ $programs->total() }} programs</p>
    </div>
</section>

<div class="mx-auto max-w-6xl px-4 py-10">
    <form method="get" class="mb-8 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:grid-cols-3">
        <label class="block text-sm font-semibold text-slate-700">
            Search
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Program or subject" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm font-normal">
        </label>
        <label class="block text-sm font-semibold text-slate-700">
            University
            <select name="university" onchange="this.form.faculty.value=''; this.form.submit()" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-normal text-slate-900">
                <option value="">All universities</option>
                @foreach ($universities as $university)
                    <option value="{{ $university->id }}" @selected($selectedUniversity === $university->id)>{{ $university->name }}</option>
                @endforeach
            </select>
        </label>
        <label class="block text-sm font-semibold text-slate-700">
            Faculty
            <select name="faculty" onchange="this.form.submit()" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-normal text-slate-900">
                <option value="">All faculties</option>
                @if ($selectedUniversity)
                    @foreach ($faculties as $faculty)
                        <option value="{{ $faculty->id }}" @selected($selectedFaculty === $faculty->id)>{{ $faculty->name }}</option>
                    @endforeach
                @else
                    @foreach ($faculties->groupBy('university_id') as $group)
                        <optgroup label="{{ $group->first()->university?->name }}">
                            @foreach ($group as $faculty)
                                <option value="{{ $faculty->id }}" @selected($selectedFaculty === $faculty->id)>{{ $faculty->name }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                @endif
            </select>
        </label>
        <select name="province" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="">All provinces</option>
            @foreach ($provinces as $province)
                <option value="{{ $province->id }}" @selected(request('province') == $province->id)>{{ $province->name }}</option>
            @endforeach
        </select>
        <select name="level" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="">All degree levels</option>
            @foreach ($levels as $level)
                <option value="{{ $level->id }}" @selected(request('level') == $level->id)>{{ $level->name }}</option>
            @endforeach
        </select>
        <button class="rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white">Search</button>
    </form>

    <div class="grid gap-4 sm:grid-cols-2">
        @forelse ($programs as $program)
            <a href="{{ route('university-programs.show', $program) }}" class="flex flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:border-brand-300">
                <h2 class="text-lg font-bold text-slate-900">{{ $program->name }}</h2>
                <p class="mt-2 text-sm font-semibold text-brand-800">{{ $program->university?->name }}</p>
                <p class="mt-1 text-sm text-slate-600">{{ collect([$program->faculty?->name, $program->duration, $program->educationLevel?->name])->filter()->implode(' · ') ?: 'Details not recorded' }}</p>
                @if ($program->description)
                    <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ \Illuminate\Support\Str::limit($program->description, 140) }}</p>
                @endif
            </a>
        @empty
            <p class="text-slate-600">No academic programs match this search.</p>
        @endforelse
    </div>
    <div class="mt-8">{{ $programs->links() }}</div>
</div>
@endsection

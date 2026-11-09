@extends('layouts.public')

@section('title', 'Suggest an institution — '.config('app.name'))
@section('meta_description', 'Suggest a school, college, university, or training center that is missing from the Nepal education directory.')

@section('content')
<section class="border-b border-slate-200 bg-white">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-2 text-sm text-slate-500" aria-label="Breadcrumb">
            <a class="transition hover:text-blue-700" href="{{ route('home') }}">Home</a>
            <span aria-hidden="true">/</span>
            <span class="font-semibold text-slate-900">Suggest an institution</span>
        </nav>
        <h1 class="mt-4 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Suggest an institution</h1>
        <p class="mt-2 max-w-2xl text-base text-slate-600">
            Tell us about a school, college, university, or training center that is not listed yet. We review every suggestion before it appears in the directory.
        </p>
    </div>
</section>

<section class="bg-white py-12 sm:py-16">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 sm:px-6 lg:grid-cols-[2fr_1fr] lg:px-8">
        <div class="rounded border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            @if (session('status'))
                <div class="mb-6 rounded border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-900" role="status">
                    {{ session('status') }}
                </div>
            @endif

            <form action="{{ route('suggestions.store') }}" method="POST">
                @csrf
                <div>
                    <label for="institution_name" class="block text-sm font-semibold text-slate-700">
                        Institution name <span class="text-red-600">*</span>
                    </label>
                    <input id="institution_name" name="institution_name" type="text" required value="{{ old('institution_name') }}" placeholder="e.g. Tribhuvan University, Kirtipur Campus" class="form-input mt-2">
                    @error('institution_name')
                        <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="institution_type_id" class="block text-sm font-semibold text-slate-700">Type</label>
                        <select id="institution_type_id" name="institution_type_id" class="form-select mt-2">
                            <option value="">— Select a type —</option>
                            @foreach ($types as $type)
                                <option value="{{ $type->id }}" @selected(old('institution_type_id') == $type->id)>{{ $type->name }}</option>
                            @endforeach
                        </select>
                        @error('institution_type_id')
                            <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="province_id" class="block text-sm font-semibold text-slate-700">Province</label>
                        <select id="province_id" name="province_id" class="form-select mt-2">
                            <option value="">— Select a province —</option>
                            @foreach ($provinces as $province)
                                <option value="{{ $province->id }}" @selected(old('province_id') == $province->id)>{{ $province->name }}</option>
                            @endforeach
                        </select>
                        @error('province_id')
                            <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-5">
                    <label for="location" class="block text-sm font-semibold text-slate-700">District, municipality, or address</label>
                    <input id="location" name="location" type="text" value="{{ old('location') }}" placeholder="e.g. Kathmandu, Kirtipur" class="form-input mt-2">
                    @error('location')
                        <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-5">
                    <label for="website" class="block text-sm font-semibold text-slate-700">Official website</label>
                    <input id="website" name="website" type="url" value="{{ old('website') }}" placeholder="https://" class="form-input mt-2">
                    @error('website')
                        <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="submitter_name" class="block text-sm font-semibold text-slate-700">
                            Your name <span class="text-red-600">*</span>
                        </label>
                        <input id="submitter_name" name="submitter_name" type="text" required value="{{ old('submitter_name') }}" class="form-input mt-2">
                        @error('submitter_name')
                            <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="submitter_email" class="block text-sm font-semibold text-slate-700">
                            Email <span class="text-red-600">*</span>
                        </label>
                        <input id="submitter_email" name="submitter_email" type="email" required value="{{ old('submitter_email') }}" class="form-input mt-2">
                        @error('submitter_email')
                            <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-5">
                    <label for="notes" class="block text-sm font-semibold text-slate-700">Anything else we should know</label>
                    <textarea id="notes" name="notes" rows="4" class="form-input mt-2 resize-none" placeholder="Programs offered, affiliation, or a source we can check.">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-6">
                    <button type="submit" class="btn-primary">Submit suggestion</button>
                </div>
            </form>
        </div>

        <aside class="h-fit rounded border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-widest text-slate-500">What happens next</p>
            <div class="mt-5 space-y-5">
                @foreach ([
                    ['1', 'You submit the suggestion', 'We store the institution name, location, and your contact details.'],
                    ['2', 'We check official sources', 'A suggestion is not published until it can be verified.'],
                    ['3', 'It appears in the directory', 'Approved institutions show up in search and place pages.'],
                ] as [$step, $title, $desc])
                    <div class="flex items-start gap-4">
                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-blue-50 text-xs font-bold text-blue-800">{{ $step }}</span>
                        <div>
                            <p class="text-sm font-semibold text-slate-800">{{ $title }}</p>
                            <p class="mt-0.5 text-xs leading-relaxed text-slate-500">{{ $desc }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </aside>
    </div>
</section>
@endsection

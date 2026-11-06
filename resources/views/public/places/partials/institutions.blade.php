<div class="mt-10 grid gap-8 lg:grid-cols-2">
    <section>
        <h2 class="text-xl font-bold">Schools <span class="text-base font-normal text-slate-500">({{ number_format($schoolCount) }})</span></h2>
        @if ($schools->isEmpty())
            <p class="mt-4 rounded-lg border border-dashed border-slate-300 bg-white px-4 py-6 text-sm text-slate-600">No published schools are linked to {{ $placeName }} yet.</p>
        @else
            <ul class="mt-4 divide-y divide-slate-200 rounded-lg border border-slate-200 bg-white">
                @foreach ($schools as $school)
                    <li class="px-4 py-3">
                        <a class="font-semibold text-brand-800" href="{{ route('institutions.show', $school) }}">{{ $school->name }}</a>
                        <p class="mt-1 text-sm text-slate-600">{{ $school->municipality?->name }}@if ($school->district), {{ $school->district->name }}@endif</p>
                    </li>
                @endforeach
            </ul>
            <div class="mt-4">{{ $schools->links() }}</div>
            <a class="mt-3 inline-block text-sm font-semibold text-brand-700" href="{{ $schoolsUrl }}">All schools in {{ $placeName }}</a>
        @endif
    </section>
    <section>
        <h2 class="text-xl font-bold">Colleges <span class="text-base font-normal text-slate-500">({{ number_format($collegeCount) }})</span></h2>
        @if ($colleges->isEmpty())
            <p class="mt-4 rounded-lg border border-dashed border-slate-300 bg-white px-4 py-6 text-sm text-slate-600">No published colleges are linked to {{ $placeName }} yet.</p>
        @else
            <ul class="mt-4 divide-y divide-slate-200 rounded-lg border border-slate-200 bg-white">
                @foreach ($colleges as $college)
                    <li class="px-4 py-3">
                        <a class="font-semibold text-brand-800" href="{{ route('institutions.show', $college) }}">{{ $college->name }}</a>
                        <p class="mt-1 text-sm text-slate-600">{{ $college->municipality?->name }}@if ($college->district), {{ $college->district->name }}@endif</p>
                    </li>
                @endforeach
            </ul>
            <div class="mt-4">{{ $colleges->links() }}</div>
        @endif
    </section>
</div>

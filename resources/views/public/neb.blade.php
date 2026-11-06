@extends('layouts.public')

@section('title', 'National Examinations Board (NEB) — '.config('app.name'))
@section('meta_description', 'How Nepal’s National Examinations Board conducts SEE and Grade 11–12 examinations, and how school operation is managed separately by local government.')
@section('canonical', route('neb'))

@section('content')
<section class="border-b border-slate-200 bg-white py-12">
    <div class="mx-auto max-w-6xl px-4">
        <nav class="text-sm text-slate-500" aria-label="Breadcrumb">
            <a class="hover:text-brand-700" href="{{ route('home') }}">Home</a>
            <span> / </span>
            <span class="text-slate-800">NEB</span>
        </nav>
        <img src="{{ asset('images/logos/neb.jpg') }}" alt="National Examinations Board logo" class="mt-6 h-24 w-24 object-contain">
        <h1 class="mt-4 text-3xl font-extrabold text-slate-900">National Examinations Board</h1>
        <p class="mt-1 text-lg text-slate-600">राष्ट्रिय परीक्षा बोर्ड</p>
        <p class="mt-4 text-slate-700">NEB is the government board responsible for school-level public examinations in Nepal. Its own introduction says its main work is the development and management of the examination system.</p>
        <div class="mt-6 flex flex-wrap gap-3">
            <a href="https://neb.gov.np/" target="_blank" rel="noopener noreferrer" class="rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">Visit Official Website</a>
            <a href="https://neb.gov.np/brief-introduction" target="_blank" rel="noopener noreferrer" class="rounded-lg border border-brand-700 px-4 py-2 text-sm font-semibold text-brand-800 hover:bg-brand-50">Official introduction</a>
        </div>
    </div>
</section>

<article class="mx-auto max-w-6xl space-y-8 px-4 py-10 text-sm leading-relaxed text-slate-700">
    <section>
        <h2 class="text-xl font-bold text-slate-900">How NEB was formed</h2>
        <p class="mt-2">When the Education (Eighth Amendment) Act, 2073 came into force, the Higher Secondary Education Board was dissolved and the National Examinations Board took its place. The examination work previously done by the Higher Secondary Education Board’s Office of the Controller of Examinations continues under NEB. The Act describes NEB as an autonomous, organized body with its own seal. Property and liabilities that were in the name of the Higher Secondary Education Board transferred to NEB.</p>
    </section>
    <section>
        <h2 class="text-xl font-bold text-slate-900">What NEB controls</h2>
        <ul class="mt-2 list-disc space-y-2 pl-5">
            <li>Grade 10 Secondary Education Examination (SEE).</li>
            <li>Grade 11 and Grade 12 examinations.</li>
            <li>Direction of school examinations more broadly. The official introduction says Grade 10 examinations are conducted at provincial level, while overall guidance of school examinations remains within NEB’s responsibility.</li>
            <li>Issue of certificates. The central office, provincial offices, and district branch offices distribute certificates. Older certificates carry NEB’s seal and a note that they are issued under clause 41 (2) of the Education Act (Eighth Amendment), 2073.</li>
        </ul>
        <p class="mt-3">Grade 11–12 examinations and the Grade 10 examination are handled through separate Offices of the Controller of Examinations under the board.</p>
    </section>
    <section>
        <h2 class="text-xl font-bold text-slate-900">What NEB does not manage</h2>
        <p class="mt-2">NEB’s introduction is explicit about the split of duties. Permission to affiliate or open a school, scholarships, and the operation of schools are managed by the local level. Curriculum development and equivalence determination are done by the Curriculum Development Centre. NEB is the examination authority, not the body that licenses or runs a school day to day.</p>
    </section>
    <section>
        <h2 class="text-xl font-bold text-slate-900">Contact</h2>
        <p class="mt-2">Sanothimi, Bhaktapur, Bagmati Province. Phone (+977) 01-6630848 and 01-6630747. Email info@neb.gov.np, coe@neb.gov.np, and admin@neb.gov.np.</p>
        <p class="mt-2">Source: <a class="font-semibold text-brand-700" href="https://neb.gov.np/brief-introduction" target="_blank" rel="noopener noreferrer">neb.gov.np/brief-introduction</a> and <a class="font-semibold text-brand-700" href="https://neb.gov.np/contact-us" target="_blank" rel="noopener noreferrer">neb.gov.np/contact-us</a>.</p>
    </section>
</article>
@endsection

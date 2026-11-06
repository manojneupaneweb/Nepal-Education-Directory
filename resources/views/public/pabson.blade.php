@extends('layouts.public')

@section('title', 'PABSON — '.config('app.name'))
@section('meta_description', 'How the Private and Boarding Schools’ Organization Nepal supports member private schools through its constitution and committees.')
@section('canonical', route('pabson'))

@section('content')
<section class="border-b border-slate-200 bg-white py-12">
    <div class="mx-auto max-w-6xl px-4">
        <nav class="text-sm text-slate-500" aria-label="Breadcrumb">
            <a class="hover:text-brand-700" href="{{ route('home') }}">Home</a>
            <span> / </span>
            <span class="text-slate-800">PABSON</span>
        </nav>
        <img src="{{ asset('images/logos/pabson.jpg') }}" alt="PABSON logo" class="mt-6 h-24 w-24 object-contain">
        <h1 class="mt-4 text-3xl font-extrabold text-slate-900">PABSON</h1>
        <p class="mt-1 text-lg text-slate-600">Private and Boarding Schools' Organization Nepal</p>
        <p class="mt-4 text-slate-700">PABSON is a membership organization for private and boarding schools. It is not a government examination board and it does not license schools. Member schools still operate under Government of Nepal rules and the local authority where they are registered.</p>
        <a href="https://pabson.org/" target="_blank" rel="noopener noreferrer" class="mt-6 inline-flex rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">Visit Official Website</a>
    </div>
</section>

<article class="mx-auto max-w-6xl space-y-8 px-4 py-10 text-sm leading-relaxed text-slate-700">
    <section>
        <h2 class="text-xl font-bold text-slate-900">Why it was formed</h2>
        <p class="mt-2">PABSON’s own account says private schools grew across Nepal before and after 2028 BS, and founders needed an organized body. The stated purpose is to guide, promote, and protect private schools, coordinate professional development, and safeguard the professional rights of founders, principals, teachers, staff, and students. It also says it helps coordinate educational and social activities of member schools.</p>
    </section>
    <section>
        <h2 class="text-xl font-bold text-slate-900">How it is organized</h2>
        <p class="mt-2">PABSON is guided by its own constitution and describes itself as an umbrella organization for its member schools. It works through central, provincial, and district or local committees, plus sub-committees and departments. Those bodies are elected or nominated under the constitution.</p>
    </section>
    <section>
        <h2 class="text-xl font-bold text-slate-900">How schools join</h2>
        <p class="mt-2">Membership is voluntary. General membership is open to privately managed schools, from playgroup through higher secondary, that are registered with the District Education Office or the local agency and operate under the rules of the Government of Nepal. A school applies at the PABSON district executive office, agrees to follow PABSON’s constitution, code of conduct, and guidelines, and pays the registration fee. Member schools also pay an annual renewal fee.</p>
        <p class="mt-2">Joining PABSON does not replace government registration. A school must already be registered with the education authority before it can apply.</p>
    </section>
    <section>
        <h2 class="text-xl font-bold text-slate-900">Contact</h2>
        <p class="mt-2">Private and Boarding Schools' Organization Nepal, Dillibazar, Kathmandu 44600. Phone +977 01-4420663. Email pabsoncc@gmail.com.</p>
        <p class="mt-2">Source: <a class="font-semibold text-brand-700" href="https://pabson.org/" target="_blank" rel="noopener noreferrer">pabson.org</a> and <a class="font-semibold text-brand-700" href="https://pabson.org/membership" target="_blank" rel="noopener noreferrer">pabson.org/membership</a>.</p>
    </section>
</article>
@endsection

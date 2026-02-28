@extends('layouts.app')
@section('title', 'Tanya Jawab')

@section('content')
{{-- Style paksa agar background slate-50 menutup sempurna mepet header & footer --}}
<style>
    body { background-color: #f8fafc !important; }
    main, .content-wrapper { padding: 0 !important; margin: 0 !important; }
</style>

<div class="bg-slate-50 min-h-screen">
    {{-- max-w-3xl untuk skala lebih kecil, pt-20 agar tidak terlalu mepet header --}}
    <section class="max-w-3xl mx-auto px-10 pt-20 pb-16">
        <h2 class="text-3xl font-black text-center mb-12 text-blue-600">FAQ</h2>
        <div class="space-y-4">
            {{-- Loop data FAQ dari database --}}
            @forelse($faqs as $key => $faq)
                <details class="group bg-white p-6 rounded-2xl border-l-8 {{ $loop->iteration % 2 == 0 ? 'border-blue-500' : 'border-orange-500' }} shadow-sm" {{ $loop->first ? 'open' : '' }}>
                    <summary class="font-bold text-lg cursor-pointer list-none flex justify-between items-center text-slate-800">
                        {{ $faq->question }}
                        <i class="fas fa-chevron-down group-open:rotate-180 transition"></i>
                    </summary>
                    <div class="mt-4 text-slate-600 prose prose-slate max-w-none">
                        {!! $faq->answer !!}
                    </div>
                </details>
            @empty
                <div class="text-center py-10">
                    <p class="text-slate-400 font-medium">Belum ada tanya jawab tersedia.</p>
                </div>
            @endforelse
        </div>
    </section>
</div>
@endsection
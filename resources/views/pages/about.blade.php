@extends('layouts.app')
@section('title', 'Tentang Kami')

@section('content')
{{-- Style paksa agar background slate-50 menutup sempurna tanpa celah --}}
<style>
    body { background-color: #f8fafc !important; }
    main, .content-wrapper { padding: 0 !important; margin: 0 !important; }
</style>

{{-- py-20 tanpa min-h-screen agar jarak header & footer murni dari padding (sama dengan Hubungi Kami) --}}
<div class="py-20 px-10 bg-slate-50">
    {{-- Card dibuat max-w-2xl agar lebih ramping dan profesional --}}
    <div class="container mx-auto max-w-2xl bg-white p-10 md:p-12 rounded-[40px] shadow-xl border border-slate-100">
        <h1 class="text-3xl font-black text-blue-600 mb-6 text-center">Tentang Mandala<span class="text-orange-500">Bimbel</span></h1>
        
        <div class="space-y-6 text-slate-600 leading-relaxed text-center">
            <p class="text-sm md:text-base">MandalaBimbel adalah lembaga bimbingan belajar modern yang fokus pada pengembangan potensi akademik siswa melalui metode pembelajaran yang interaktif dan adaptif.</p>
            
            {{-- Bagian Visi Misi dibuat Center --}}
            <div class="grid md:grid-cols-2 gap-5 pt-4 text-center">
                <div class="bg-blue-50 p-6 rounded-3xl border border-blue-100/50">
                    <h3 class="font-bold text-blue-600 mb-2 italic flex justify-center items-center">
                        <span class="w-1.5 h-1.5 bg-blue-600 rounded-full mr-2"></span>
                        Misi Kami
                    </h3>
                    <p class="text-xs italic leading-loose">Memberikan akses pendidikan berkualitas tinggi dengan harga terjangkau bagi seluruh pelajar Indonesia.</p>
                </div>
                
                <div class="bg-orange-50 p-6 rounded-3xl border border-orange-100/50">
                    <h3 class="font-bold text-orange-500 mb-2 italic flex justify-center items-center">
                        <span class="w-1.5 h-1.5 bg-orange-500 rounded-full mr-2"></span>
                        Visi Kami
                    </h3>
                    <p class="text-xs italic leading-loose">Menjadi platform edukasi digital nomor satu yang mencetak generasi unggul dan berkarakter.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
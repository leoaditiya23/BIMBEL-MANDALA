@extends('layouts.app')
@section('title', 'Tentang Kami')

@section('content')
<div class="py-20 px-10 bg-slate-50">
    <div class="container mx-auto max-w-4xl bg-white p-12 rounded-[40px] shadow-xl border border-slate-100">
        <h1 class="text-4xl font-black text-blue-600 mb-6">Tentang Mandala<span class="text-orange-500">Bimbel</span></h1>
        <div class="space-y-6 text-slate-600 leading-relaxed">
            <p>MandalaBimbel adalah lembaga bimbingan belajar modern yang fokus pada pengembangan potensi akademik siswa melalui metode pembelajaran yang interaktif dan adaptif.</p>
            <div class="grid md:grid-cols-2 gap-8 py-8">
                <div class="bg-blue-50 p-6 rounded-2xl">
                    <h3 class="font-bold text-blue-600 mb-2 italic">Misi Kami</h3>
                    <p class="text-sm italic">Memberikan akses pendidikan berkualitas tinggi dengan harga terjangkau bagi seluruh pelajar Indonesia.</p>
                </div>
                <div class="bg-orange-50 p-6 rounded-2xl">
                    <h3 class="font-bold text-orange-500 mb-2 italic">Visi Kami</h3>
                    <p class="text-sm italic">Menjadi platform edukasi digital nomor satu yang mencetak generasi unggul dan berkarakter.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
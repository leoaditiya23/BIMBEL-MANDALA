@extends('layouts.app')
@section('title', 'Tanya Jawab')

@section('content')
<section class="max-w-4xl mx-auto px-10 py-16">
    <h2 class="text-4xl font-black text-center mb-12 text-blue-600">FAQ</h2>
    <div class="space-y-4">
        <details class="group bg-slate-50 p-6 rounded-2xl border-l-8 border-orange-500" open>
            <summary class="font-bold text-lg cursor-pointer list-none flex justify-between items-center">
                Apakah bisa bayar cicilan?
                <i class="fas fa-chevron-down group-open:rotate-180 transition"></i>
            </summary>
            <p class="mt-4 text-slate-600">Tentu! Khusus untuk Program Intensif, kami menyediakan layanan cicilan 2x.</p>
        </details>
        
        <details class="group bg-slate-50 p-6 rounded-2xl border-l-8 border-blue-500">
            <summary class="font-bold text-lg cursor-pointer list-none flex justify-between items-center">
                Bagaimana cara memilih mentor?
                <i class="fas fa-chevron-down group-open:rotate-180 transition"></i>
            </summary>
            <p class="mt-4 text-slate-600">Setelah mendaftar Program Reguler, Anda akan diberikan daftar mentor yang tersedia sesuai jadwal Anda.</p>
        </details>
    </div>
</section>
@endsection
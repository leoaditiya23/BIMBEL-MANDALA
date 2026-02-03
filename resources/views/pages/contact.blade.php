@extends('layouts.app')
@section('title', 'Hubungi Kami')

@section('content')
<div class="py-20 px-10 bg-slate-50">
    <div class="container mx-auto max-w-5xl grid md:grid-cols-2 gap-10">
        <div class="bg-white p-12 rounded-[40px] shadow-xl border border-slate-100">
            <h2 class="text-3xl font-black text-slate-800 mb-6">Hubungi Kami</h2>
            <form action="#" class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Nama</label>
                    <input type="text" class="w-full px-5 py-3 rounded-xl border border-slate-200 outline-none focus:border-blue-500" placeholder="Nama Anda">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Pesan</label>
                    <textarea class="w-full px-5 py-3 rounded-xl border border-slate-200 outline-none focus:border-blue-500 h-32" placeholder="Apa yang bisa kami bantu?"></textarea>
                </div>
                <button type="button" class="w-full py-4 bg-blue-600 text-white rounded-xl font-bold shadow-lg shadow-blue-200">Kirim Pesan</button>
            </form>
        </div>
        <div class="flex flex-col justify-center space-y-8 px-10">
            <div>
                <h3 class="text-xl font-bold text-blue-600">Alamat Kantor</h3>
                <p class="text-slate-500 mt-2">Tamansari Hills Residence Blok B01 No.10, RT.02/RW.10, Mangunharjo, Kec. Banyumanik, Kota Semarang, Jawa Tengah 50272</p>
            </div>
            <div>
                <h3 class="text-xl font-bold text-orange-500">Kontak Cepat</h3>
                <p class="text-slate-500 mt-2"><i class="fab fa-whatsapp mr-2"></i> +62 855-4000-0900</p>
                <p class="text-slate-500"><i class="far fa-envelope mr-2"></i> halo@mandalabimbel.com</p>
            </div>
        </div>
    </div>
</div>
@endsection
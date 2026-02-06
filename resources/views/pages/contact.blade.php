@extends('layouts.app')
@section('title', 'Hubungi Kami')

@section('content')
<div class="py-20 px-10 bg-slate-50">
    <div class="container mx-auto max-w-5xl grid md:grid-cols-2 gap-10">
        <div class="bg-white p-12 rounded-[40px] shadow-xl border border-slate-100">
            <h2 class="text-3xl font-black text-slate-800 mb-6">Hubungi Kami</h2>
            
            {{-- Alert Sukses --}}
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-700 rounded-2xl flex items-center shadow-sm italic text-sm">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
            @endif

           {{-- Form dihubungkan ke Route --}}
            <form action="{{ route('contact.store') }}" method="POST" class="space-y-5">
                @csrf
                {{-- Nama --}}
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Nama Lengkap</label>
                    <input type="text" name="name" placeholder="Nama Anda" required
                        class="w-full px-5 py-4 rounded-2xl bg-slate-50 border border-slate-100 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-slate-700 font-medium">
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- WhatsApp --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">No. WhatsApp</label>
                        <input type="number" name="whatsapp" placeholder="0812..." required
                            class="w-full px-5 py-4 rounded-2xl bg-slate-50 border border-slate-100 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-slate-700 font-medium">
                    </div>
                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Email (Opsional)</label>
                        <input type="email" name="email" placeholder="Email Anda"
                            class="w-full px-5 py-4 rounded-2xl bg-slate-50 border border-slate-100 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-slate-700 font-medium">
                    </div>
                </div>
                
                {{-- Pesan --}}
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Pesan Anda</label>
                    <textarea name="message" placeholder="Tulis pertanyaan Anda..." required rows="4"
                        class="w-full px-5 py-4 rounded-2xl bg-slate-50 border border-slate-100 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-slate-700 font-medium resize-none"></textarea>
                </div>
                
                {{-- Tombol Kirim --}}
                <button type="submit" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-5 rounded-2xl shadow-xl shadow-blue-200 hover:shadow-blue-300 transition-all transform hover:-translate-y-1 active:scale-95 flex items-center justify-center space-x-3 text-lg">
                    <i class="fas fa-paper-plane"></i>
                    <span>Kirim Pesan</span>
                </button>
            </form>
        </div>
        
        <div class="flex flex-col justify-center space-y-8 px-10">
            <div>
                <h3 class="text-xl font-bold text-blue-600">Alamat Kantor</h3>
                <p class="text-slate-500 mt-2 leading-relaxed">Tamansari Hills Residence Blok B01 No.10, RT.02/RW.10, Mangunharjo, Kec. Banyumanik, Kota Semarang, Jawa Tengah 50272</p>
            </div>
            <div>
                <h3 class="text-xl font-bold text-orange-500">Kontak Cepat</h3>
                <div class="mt-4 space-y-3">
                    <p class="text-slate-500 flex items-center"><i class="fab fa-whatsapp mr-3 text-green-500 text-lg"></i> +62 855-4000-0900</p>
                    <p class="text-slate-500 flex items-center"><i class="far fa-envelope mr-3 text-blue-400"></i> halo@mandalabimbel.com</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
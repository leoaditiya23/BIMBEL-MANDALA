@extends('layouts.app')
@section('title', 'Hubungi Kami')

@section('content')
{{-- Style paksa untuk memastikan background body sinkron --}}
<style>
    body { background-color: #f8fafc !important; }
</style>

{{-- py-0 bikin mepet atas bawah, items-stretch memastikan konten mengisi ruang --}}
<div class="w-full bg-slate-50 min-h-screen flex items-center py-10 px-10">
    <div class="container mx-auto max-w-5xl grid md:grid-cols-2 gap-10">
        
        {{-- CARD FORM --}}
        <div class="bg-white p-8 md:p-10 rounded-[40px] shadow-xl border border-slate-100 max-w-md w-full mx-auto md:ml-0">
            <h2 class="text-2xl font-black text-slate-800 mb-6">Hubungi Kami</h2>
            
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-700 rounded-2xl flex items-center shadow-sm italic text-sm">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('contact.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 ml-1">Nama Lengkap</label>
                    <input type="text" name="name" placeholder="Nama Anda" required
                        class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-100 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-slate-700 font-medium text-sm">
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5 ml-1">WhatsApp</label>
                        <input type="number" name="whatsapp" placeholder="08..." required
                            class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-100 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-slate-700 font-medium text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5 ml-1">Email</label>
                        <input type="email" name="email" placeholder="Email"
                            class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-100 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-slate-700 font-medium text-sm">
                    </div>
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 ml-1">Pesan Anda</label>
                    <textarea name="message" placeholder="Tulis pesan..." required rows="3"
                        class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-100 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-slate-700 font-medium text-sm resize-none"></textarea>
                </div>
                
                <button type="submit" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-4 rounded-xl shadow-lg shadow-blue-200 hover:shadow-blue-300 transition-all transform hover:-translate-y-1 active:scale-95 flex items-center justify-center space-x-3 text-base">
                    <i class="fas fa-paper-plane"></i>
                    <span>Kirim Pesan</span>
                </button>
            </form>
        </div>
        
        {{-- INFORMASI KONTAK --}}
        <div class="flex flex-col justify-center space-y-8 px-10">
            <div>
                <h3 class="text-xl font-bold text-blue-600">Alamat Kantor</h3>
                <p class="text-slate-500 mt-2 leading-relaxed">Tamansari Hills Residence Blok B01 No.10, RT.02/RW.10, Mangunharjo, Kec. Banyumanik, Kota Semarang, Jawa Tengah 50272</p>
            </div>
            <div>
                <h3 class="text-xl font-bold text-orange-500">Kontak Cepat</h3>
                <div class="mt-4 space-y-3">
                    <p class="text-slate-500 flex items-center font-medium"><i class="fab fa-whatsapp mr-3 text-green-500 text-lg"></i> +62 855-4000-0900</p>
                    <p class="text-slate-500 flex items-center font-medium"><i class="far fa-envelope mr-3 text-blue-400"></i> halo@mandalabimbel.com</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
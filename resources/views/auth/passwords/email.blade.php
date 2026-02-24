@extends('layouts.app')
@section('title', 'Lupa Password')

@section('content')
{{-- items-start untuk narik ke atas, pt-20 untuk kasih jarak dikit dari navbar --}}
<div class="min-h-screen flex items-start justify-center bg-slate-50 px-6 pt-20 pb-12">
    
    {{-- max-w-md adalah kunci supaya scalenya kecil sama kayak login --}}
    <div class="bg-white w-full max-w-md rounded-[40px] shadow-2xl overflow-hidden border border-slate-100">
        
        <div class="p-10 lg:p-12">
            <div class="mb-8">
                <a href="{{ route('login') }}" class="text-blue-600 font-bold text-xs hover:underline flex items-center mb-6 uppercase tracking-widest">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali ke Login
                </a>
                <h2 class="text-2xl font-black text-slate-800 uppercase tracking-tighter">Lupa Password?</h2>
                <p class="text-slate-500 mt-2 text-sm leading-relaxed">Masukkan email kamu, kami akan kirimkan link untuk buat password baru.</p>
            </div>

            @if (session('status'))
                <div class="bg-green-50 text-green-700 p-4 rounded-2xl mb-6 text-xs font-bold flex items-center border border-green-100">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('status') }}
                </div>
            @endif

            {{-- space-y dikurangi ke 4 supaya tombol naik mendekat ke input --}}
            <form action="{{ route('password.email') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-black text-slate-700 mb-3 uppercase tracking-wide ml-1">Alamat Email Terdaftar</label>
                    <input type="email" name="email" value="{{ old('email') }}" 
                        class="w-full px-5 py-4 rounded-2xl border border-slate-200 outline-none focus:border-blue-600 transition bg-slate-50 focus:bg-white text-sm @error('email') border-red-500 @enderror" 
                        placeholder="contoh: budi@gmail.com" required>
                    @error('email')
                        <p class="text-red-500 text-[10px] mt-2 font-bold italic">{{ $message }}</p>
                    @enderror
                </div>

                {{-- mt-2 dihilangkan agar tombol lebih rapat ke atas --}}
                <button type="submit" class="w-full py-4 bg-blue-600 text-white rounded-2xl font-black shadow-lg shadow-blue-200 hover:bg-orange-500 transition active:scale-95 duration-200 uppercase tracking-[0.2em] text-xs">
                    Kirim Link Reset
                </button>
            </form>

            {{-- mt dikurangi ke 6 agar footer system juga ikut naik --}}
            <div class="mt-6 pt-6 border-t border-slate-50 text-center">
                <p class="text-slate-400 text-[10px] uppercase font-bold tracking-widest">Mandala Bimbel System</p>
            </div>
        </div>
    </div>
</div>
@endsection
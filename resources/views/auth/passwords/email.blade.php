@extends('layouts.app')
@section('title', 'Lupa Password')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-slate-50 px-10 py-12">
    <div class="bg-white w-full max-w-5xl flex rounded-[40px] shadow-2xl overflow-hidden border border-slate-100">
        
        <div class="w-full md:w-1/2 p-12 lg:p-16">
            <div class="mb-10">
                <a href="{{ route('login') }}" class="text-blue-600 font-bold text-sm hover:underline flex items-center mb-6">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali ke Login
                </a>
                <h2 class="text-3xl font-black text-slate-800 uppercase tracking-tighter">Lupa Password?</h2>
                <p class="text-slate-500 mt-2">Jangan panik! Masukkan email kamu, kami akan kirimkan link untuk buat password baru.</p>
            </div>

            @if (session('status'))
                <div class="bg-green-100 text-green-700 p-4 rounded-2xl mb-6 text-sm font-bold flex items-center border border-green-200">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('status') }}
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Alamat Email Terdaftar</label>
                    <input type="email" name="email" value="{{ old('email') }}" 
                        class="w-full px-5 py-4 rounded-2xl border border-slate-200 outline-none focus:border-blue-600 transition bg-slate-50 focus:bg-white @error('email') border-red-500 @enderror" 
                        placeholder="contoh: budi@gmail.com" required>
                    @error('email')
                        <p class="text-red-500 text-xs mt-2 font-bold italic">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full py-4 bg-blue-600 text-white rounded-2xl font-black shadow-lg shadow-blue-200 hover:bg-orange-500 transition active:scale-95 duration-200 uppercase tracking-widest">
                    Kirim Link Reset
                </button>
            </form>
        </div>
        
        <div class="hidden md:flex w-1/2 bg-blue-600 items-center justify-center text-white p-12 relative overflow-hidden">
             <div class="absolute inset-0 opacity-10" style="background-image: url('https://www.transparenttextures.com/patterns/cubes.png');"></div>
            <div class="text-center relative z-10">
                <i class="fas fa-key text-[150px] opacity-20 mb-6 animate-bounce"></i>
                <h3 class="text-2xl font-bold uppercase tracking-widest">Atur Ulang Akses</h3>
                <p class="opacity-80 max-w-xs mx-auto">Satu langkah lagi untuk kembali belajar bersama Mandala Bimbel.</p>
            </div>
        </div>
    </div>
</div>
@endsection
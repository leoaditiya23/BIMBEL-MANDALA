@extends('layouts.app')
@section('title', 'Buat Password Baru')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-slate-50 px-10 py-12">
    <div class="bg-white w-full max-w-5xl flex rounded-[40px] shadow-2xl overflow-hidden border border-slate-100">
        
        <div class="w-full md:w-1/2 p-12 lg:p-16">
            <div class="mb-10">
                <h2 class="text-3xl font-black text-slate-800 uppercase tracking-tighter">Password Baru</h2>
                <p class="text-slate-500 mt-2">Silakan buat password baru yang kuat dan mudah kamu ingat.</p>
            </div>

            <form action="{{ route('password.update') }}" method="POST" class="space-y-5">
                @csrf
                {{-- Token & Email (Hidden/Readonly) --}}
                <input type="hidden" name="token" value="{{ $token }}">
                
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase mb-2 ml-1 tracking-widest">Konfirmasi Email</label>
                    <input type="email" name="email" value="{{ $email ?? old('email') }}" 
                        class="w-full px-5 py-4 rounded-2xl border border-slate-100 bg-slate-100 text-slate-500 cursor-not-allowed font-bold" 
                        readonly required>
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-700 uppercase mb-2 ml-1 tracking-widest">Password Baru</label>
                    <input type="password" name="password" 
                        class="w-full px-5 py-4 rounded-2xl border border-slate-200 outline-none focus:border-blue-600 transition bg-slate-50 focus:bg-white @error('password') border-red-500 @enderror" 
                        placeholder="••••••••" required autofocus>
                    @error('password')
                        <p class="text-red-500 text-[10px] mt-2 font-black uppercase tracking-tight">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-700 uppercase mb-2 ml-1 tracking-widest">Ulangi Password Baru</label>
                    <input type="password" name="password_confirmation" 
                        class="w-full px-5 py-4 rounded-2xl border border-slate-200 outline-none focus:border-blue-600 transition bg-slate-50 focus:bg-white" 
                        placeholder="••••••••" required>
                </div>

                <button type="submit" class="w-full py-4 bg-blue-600 text-white rounded-2xl font-black shadow-lg shadow-blue-200 hover:bg-orange-500 transition active:scale-95 duration-200 uppercase tracking-widest mt-4">
                    Update Password & Masuk
                </button>
            </form>
        </div>
        
        <div class="hidden md:flex w-1/2 bg-blue-600 items-center justify-center text-white p-12 relative overflow-hidden">
             <div class="absolute inset-0 opacity-10" style="background-image: url('https://www.transparenttextures.com/patterns/cubes.png');"></div>
            <div class="text-center relative z-10">
                <i class="fas fa-user-shield text-[150px] opacity-20 mb-6 animate-pulse"></i>
                <h3 class="text-2xl font-bold uppercase tracking-widest">Keamanan Akun</h3>
                <p class="opacity-80 max-w-xs mx-auto text-sm">Mandala Bimbel memastikan data dan akses belajar kamu tetap aman terlindungi.</p>
            </div>
        </div>
    </div>
</div>
@endsection
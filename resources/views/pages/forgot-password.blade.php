@extends('layouts.app')
@section('title', 'Lupa Password')

@section('content')
<div class="min-h-fit flex items-center justify-center bg-white px-6 py-12">
    <div class="bg-white w-full max-w-4xl flex rounded-[35px] shadow-2xl overflow-hidden border border-slate-100">
        
        <div class="w-full md:w-1/2 p-10">
            <div class="mb-6">
                <h2 class="text-2xl font-black text-slate-800">Lupa Password?</h2>
                <p class="text-xs text-slate-500">Jangan khawatir! Kami akan kirimkan link reset ke email Anda.</p>
            </div>

            @if(session('status'))
                <div class="bg-green-100 text-green-600 p-3 rounded-xl mb-4 text-xs font-bold flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('status') }}
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Email</label>
                    <input type="email" name="email" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-orange-500 transition text-sm" placeholder="email@anda.com" required>
                </div>
                
                <button type="submit" class="w-full py-3 bg-orange-500 text-white rounded-xl font-black shadow-lg hover:bg-orange-600 transition active:scale-95 duration-200 text-xs uppercase tracking-wider">
                    Kirim Link Reset
                </button>
            </form>
            <p class="mt-6 text-center text-xs text-slate-500 italic"><a href="{{ route('login') }}" class="text-orange-500 font-bold hover:underline">Kembali ke Login</a></p>
        </div>
        
        <div class="hidden md:flex w-1/2 bg-orange-500 items-center justify-center text-white p-10 relative overflow-hidden">
            <div class="absolute inset-0 opacity-10" style="background-image: url('https://www.transparenttextures.com/patterns/cubes.png');"></div>
            <div class="text-center relative z-10">
                <i class="fas fa-lock-open text-[150px] opacity-20 mb-4 animate-pulse"></i>
                <h3 class="text-xl font-bold leading-tight">Reset Password Anda</h3>
                <p class="text-xs opacity-80 mt-2">Pulihkan akses ke akun Anda dengan mudah.</p>
            </div>
        </div>
    </div>
</div>
@endsection
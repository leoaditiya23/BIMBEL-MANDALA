@extends('layouts.app')
@section('title', 'Lupa Password')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-slate-50 px-10 py-12">
    <div class="bg-white w-full max-w-md p-10 rounded-[40px] shadow-2xl border border-slate-100">
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-orange-100 text-orange-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-lock-open text-3xl"></i>
            </div>
            <h2 class="text-2xl font-black text-slate-800">Lupa Password?</h2>
            <p class="text-slate-500 text-sm mt-2">Jangan khawatir! Masukkan email Anda dan kami akan kirimkan link reset.</p>
        </div>

        @if(session('status'))
            <div class="bg-green-100 text-green-600 p-4 rounded-xl mb-6 text-sm font-bold">
                {{ session('status') }}
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 ml-2">Email Terdaftar</label>
                <input type="email" name="email" class="w-full px-5 py-3 rounded-xl border border-slate-200 outline-none focus:border-orange-500 transition" placeholder="email@anda.com" required>
            </div>
            
            <button type="submit" class="w-full py-4 bg-orange-500 text-white rounded-xl font-black shadow-lg hover:bg-blue-600 transition active:scale-95 duration-200 uppercase tracking-wider">
                Kirim Link Reset
            </button>
        </form>

        <div class="mt-8 text-center">
            <a href="{{ route('login') }}" class="text-slate-400 text-sm hover:text-blue-600 transition">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Login
            </a>
        </div>
    </div>
</div>
@endsection
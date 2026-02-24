@extends('layouts.app')
@section('title', 'Masuk')

@section('content')
{{-- 
    UBAHAN:
    1. bg-slate-50 diubah ke bg-white agar warna latar belakang menyatu dengan footer/navbar (menghilangkan garis kontras).
    2. py-12 disesuaikan agar jarak kotakan ke footer terlihat profesional.
--}}
<div class="min-h-fit flex items-center justify-center bg-white px-6 py-12">
    {{-- Lebar max-w-4xl tetap dipertahankan --}}
    <div class="bg-white w-full max-w-4xl flex rounded-[35px] shadow-2xl overflow-hidden border border-slate-100">
        
        <div class="w-full md:w-1/2 p-10">
            <div class="mb-6">
                <h2 class="text-2xl font-black text-slate-800">Masuk</h2>
                <p class="text-xs text-slate-500">Gunakan akun MandalaBimbel Anda.</p>
            </div>

            @if($errors->any())
                <div class="bg-red-100 text-red-600 p-3 rounded-xl mb-4 text-xs font-bold flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-600 transition text-sm" placeholder="email@anda.com" required>
                </div>
                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label class="block text-xs font-bold text-slate-700">Password</label>
                        <a href="{{ route('password.request') }}" class="text-[10px] font-black text-blue-600 hover:text-orange-500 uppercase tracking-tight transition">Lupa Password?</a>
                    </div>
                    <input type="password" name="password" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-600 transition text-sm" placeholder="••••••••" required>
                </div>
                <button type="submit" class="w-full py-3 bg-blue-600 text-white rounded-xl font-black shadow-lg hover:bg-orange-500 transition active:scale-95 duration-200 text-xs">
                    MASUK SEKARANG
                </button>
            </form>
            <p class="mt-6 text-center text-xs text-slate-500 italic">Belum punya akun? <a href="{{ route('register') }}" class="text-orange-500 font-bold hover:underline">Daftar</a></p>
        </div>
        
        <div class="hidden md:flex w-1/2 bg-blue-600 items-center justify-center text-white p-10 relative overflow-hidden">
            <div class="absolute inset-0 opacity-10" style="background-image: url('https://www.transparenttextures.com/patterns/cubes.png');"></div>
            <div class="text-center relative z-10">
                {{-- Icon besar text-[150px] sesuai permintaan terakhir --}}
                <i class="fas fa-user-graduate text-[150px] opacity-20 mb-4 animate-pulse"></i>
                <h3 class="text-xl font-bold leading-tight">Selamat Datang Kembali!</h3>
                <p class="text-xs opacity-80 mt-2">Teruskan perjalanan belajarmu hari ini.</p>
            </div>
        </div>
    </div>
</div>

{{-- Script SweetAlert tetap sama --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: 'PENDAFTARAN BERHASIL!',
            text: "{{ session('success') }}",
            icon: 'success',
            confirmButtonColor: '#2563eb', 
            confirmButtonText: 'OKE, SAYA MENGERTI',
            customClass: {
                popup: 'rounded-[30px]',
                confirmButton: 'rounded-xl px-10 py-3 font-bold'
            }
        });
    });
</script>
@endif

@if (session('status'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: 'BERHASIL!',
            text: "{{ session('status') }}",
            icon: 'success',
            confirmButtonColor: '#2563eb',
            confirmButtonText: 'MANTAP',
            customClass: {
                popup: 'rounded-[30px]',
                confirmButton: 'rounded-xl px-10 py-3 font-bold'
            }
        });
    });
</script>
@endif

@endsection
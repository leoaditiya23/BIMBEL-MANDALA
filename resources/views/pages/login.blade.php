@extends('layouts.app')
@section('title', 'Masuk')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-slate-50 px-10 py-12">
    <div class="bg-white w-full max-w-5xl flex rounded-[40px] shadow-2xl overflow-hidden border border-slate-100">
        <div class="w-full md:w-1/2 p-12 lg:p-16">
            <div class="mb-10">
                <h2 class="text-3xl font-black text-slate-800">Masuk</h2>
                <p class="text-slate-500">Gunakan akun MandalaBimbel Anda.</p>
            </div>

            @if($errors->any())
                <div class="bg-red-100 text-red-600 p-4 rounded-xl mb-6 text-sm font-bold flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.store') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full px-5 py-3 rounded-xl border border-slate-200 outline-none focus:border-blue-600 transition" placeholder="email@anda.com" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Password</label>
                    <input type="password" name="password" class="w-full px-5 py-3 rounded-xl border border-slate-200 outline-none focus:border-blue-600 transition" placeholder="••••••••" required>
                </div>
                <button type="submit" class="w-full py-4 bg-blue-600 text-white rounded-xl font-black shadow-lg hover:bg-orange-500 transition active:scale-95 duration-200">
                    MASUK SEKARANG
                </button>
            </form>
            <p class="mt-8 text-center text-slate-500 italic">Belum punya akun? <a href="{{ route('register') }}" class="text-orange-500 font-bold hover:underline">Daftar</a></p>
        </div>
        
        <div class="hidden md:flex w-1/2 bg-blue-600 items-center justify-center text-white p-12 relative overflow-hidden">
             <div class="absolute inset-0 opacity-10" style="background-image: url('https://www.transparenttextures.com/patterns/cubes.png');"></div>
            <div class="text-center relative z-10">
                <i class="fas fa-user-graduate text-[150px] opacity-20 mb-6 animate-pulse"></i>
                <h3 class="text-2xl font-bold">Selamat Datang Kembali!</h3>
                <p class="opacity-80">Teruskan perjalanan belajarmu hari ini.</p>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT POPUP BERHASIL DAFTAR --}}
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

@endsection
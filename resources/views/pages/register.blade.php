@extends('layouts.app')
@section('title', 'Daftar Akun Siswa')

@section('content')
{{-- 
    UBAHAN: 
    1. Menggunakan max-w-4xl agar layout memiliki ruang untuk tetap menyamping (tidak sesak).
    2. Memastikan flex-row tetap terjaga pada layar md ke atas.
--}}
<div class="min-h-fit flex items-center justify-center bg-white py-12 px-6">
    {{-- Container utama dibuat melebar ke samping dengan max-w-4xl --}}
    <div class="bg-white w-full max-w-4xl flex rounded-[35px] shadow-2xl overflow-hidden border border-slate-100">
        
        {{-- Sisi Dekoratif (Kiri) --}}
        <div class="hidden md:flex w-[30%] bg-orange-500 items-center justify-center p-8 relative overflow-hidden text-white flex-col">
    <div class="relative z-10 text-center">
        <h3 class="text-xl font-black leading-tight">Halo Calon<br>Juara!</h3>
        <p class="text-orange-50 text-[10px] mt-2 opacity-90">Mulai petualangan belajarmu sekarang.</p>
    </div>
    <div class="absolute inset-0 opacity-10" style="background-image: url('https://www.transparenttextures.com/patterns/cubes.png');"></div>
</div>

        {{-- Sisi Form (Kanan) - Melebar ke samping --}}
        <div class="w-full md:w-[70%] p-8 lg:p-10 overflow-y-auto max-h-[90vh]">
            <div class="text-center mb-6">
                <div class="text-2xl font-black text-blue-900 mb-1">Mandala <span class="text-orange-500">Bimbel</span></div>
                <h2 class="text-lg font-bold text-slate-800">Registrasi Siswa</h2>
                <p class="text-slate-400 text-[9px] mt-1 uppercase tracking-widest">Akun Siswa Baru</p>
            </div>

            @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-3 mb-4 rounded shadow-sm" role="alert">
                <p class="font-bold text-[10px]">Ups! Gagal:</p>
                <ul class="mt-1 list-disc list-inside text-[9px]">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form id="registerForm" action="{{ route('register.store') }}" method="POST" class="space-y-3">
                @csrf
                <input type="hidden" name="role" value="siswa">
                
                <div class="text-center text-slate-400 text-[9px] font-semibold italic uppercase tracking-tighter">--- Identitas ---</div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-700 mb-1 ml-3">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="w-full px-4 py-2 rounded-full border border-slate-200 focus:border-orange-500 outline-none transition text-xs" placeholder="Nama Lengkap" required>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-700 mb-1 ml-3">Tgl Lahir</label>
                        <input type="date" name="birth_date" value="{{ old('birth_date') }}" class="w-full px-4 py-2 rounded-full border border-slate-200 focus:border-orange-500 outline-none transition text-xs text-slate-600" required>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-700 mb-1 ml-3">Gender</label>
                        <div class="flex gap-2 mt-2 ml-1">
                            <label class="flex items-center text-[10px] text-slate-600 cursor-pointer">
                                <input type="radio" name="gender" value="Laki-laki" {{ old('gender') == 'Laki-laki' ? 'checked' : '' }} class="mr-1 accent-orange-500" required> Laki-Laki                            </label>
                            <label class="flex items-center text-[10px] text-slate-600 cursor-pointer">
                                <input type="radio" name="gender" value="Perempuan" {{ old('gender') == 'Perempuan' ? 'checked' : '' }} class="mr-1 accent-orange-500"> Perempuan
                            </label>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-700 mb-1 ml-3">WhatsApp</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="w-full px-4 py-2 rounded-full border border-slate-200 focus:border-orange-500 outline-none transition text-xs" placeholder="08..." required>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-700 mb-1 ml-3">Referral</label>
                        <input type="text" name="referral" value="{{ old('referral') }}" class="w-full px-4 py-2 rounded-full border border-slate-200 focus:border-orange-500 outline-none transition text-xs" placeholder="Kode">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-700 mb-1 ml-3">Sekolah</label>
                    <input type="text" name="school" value="{{ old('school') }}" class="w-full px-4 py-2 rounded-full border border-slate-200 focus:border-orange-500 outline-none transition text-xs" placeholder="Nama Sekolah" required>
                </div>

                <div class="text-center text-slate-400 text-[9px] font-semibold italic pt-1 uppercase tracking-tighter">--- Akun ---</div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-700 mb-1 ml-3">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-2 rounded-full border border-slate-200 focus:border-orange-500 outline-none transition text-xs" placeholder="Email" required>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-700 mb-1 ml-3">Username</label>
                        <input type="text" name="username" value="{{ old('username') }}" class="w-full px-4 py-2 rounded-full border border-slate-200 focus:border-orange-500 outline-none transition text-xs" placeholder="User" required>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="relative">
                        <label class="block text-[10px] font-bold text-slate-700 mb-1 ml-3">Password</label>
                        <input id="password" type="password" name="password" class="w-full px-4 py-2 rounded-full border border-slate-200 focus:border-orange-500 outline-none transition text-xs" placeholder="••••" required>
                        <i id="togglePassword" class="far fa-eye absolute right-4 top-8 text-blue-900 text-[10px] cursor-pointer"></i>
                    </div>
                    <div class="relative">
                        <label class="block text-[10px] font-bold text-slate-700 mb-1 ml-3">Konfirmasi</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" class="w-full px-4 py-2 rounded-full border border-slate-200 focus:border-orange-500 outline-none transition text-xs" placeholder="••••" required>
                        <i id="toggleConfirmPassword" class="far fa-eye absolute right-4 top-8 text-blue-900 text-[10px] cursor-pointer"></i>
                    </div>
                </div>

                <div class="pt-3 flex flex-col items-center">
                    <button type="submit" class="bg-orange-500 text-white px-10 py-2.5 rounded-full font-black shadow-lg hover:bg-orange-600 transition-all text-xs active:scale-95 duration-200">
                        DAFTAR SEKARANG
                    </button>
                    <p class="text-[9px] text-slate-500 mt-3 italic">Sudah punya akun? <a href="{{ route('login') }}" class="text-orange-500 font-bold hover:underline">Login disini</a></p>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const setupToggle = (btnId, inputId) => {
            const btn = document.querySelector(btnId);
            const input = document.querySelector(inputId);
            if(btn && input) {
                btn.addEventListener('click', () => {
                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);
                    btn.classList.toggle('fa-eye');
                    btn.classList.toggle('fa-eye-slash');
                });
            }
        };
        setupToggle('#togglePassword', '#password');
        setupToggle('#toggleConfirmPassword', '#password_confirmation');
    });
</script>
@endsection
@extends('layouts.app')
@section('title', 'Daftar Akun Siswa')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-slate-50 py-12 px-10">
    <div class="bg-white w-full max-w-5xl flex rounded-[40px] shadow-2xl overflow-hidden border border-slate-100">
        
        <div class="hidden md:flex w-[35%] bg-orange-500 items-center justify-center p-12 relative overflow-hidden text-white flex-col">
            <div class="relative z-10 text-center">
                <i class="fas fa-user-graduate text-[100px] mb-8"></i>
                <h3 class="text-2xl font-black whitespace-nowrap">Halo Calon Juara!</h3>
                <p class="text-orange-50 text-sm mt-2 opacity-90">Daftar sekarang sebagai Siswa dan mulai petualangan belajarmu.</p>
            </div>
        </div>

        <div class="w-full md:w-[65%] p-10 lg:p-12 overflow-y-auto max-h-[90vh]">
            <div class="text-center mb-6">
                <div class="text-3xl font-black text-blue-900 mb-1">Mandala <span class="text-orange-500">Bimbel</span></div>
                <h2 class="text-xl font-bold text-slate-800">Registrasi Siswa</h2>
                <p class="text-slate-400 text-[10px] mt-1 uppercase tracking-widest">Khusus Pendaftaran Akun Siswa Baru</p>
            </div>

            {{-- 1. REVISI: Tambahkan Alert Error di sini agar tahu kenapa database menolak --}}
            @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm" role="alert">
                <p class="font-bold text-xs">Ups! Pendaftaran Gagal:</p>
                <ul class="mt-1 list-disc list-inside text-[10px]">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form id="registerForm" action="{{ route('register.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="role" value="siswa">
                
                <div class="text-center text-slate-400 text-xs font-semibold italic">---Identitas Pendaftar---</div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1 ml-4">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="w-full px-5 py-2.5 rounded-full border border-slate-200 focus:border-orange-500 outline-none transition text-sm" placeholder="Nama Lengkap Anda" required>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1 ml-4">Tanggal Lahir</label>
                        <input type="date" name="birth_date" value="{{ old('birth_date') }}" class="w-full px-5 py-2.5 rounded-full border border-slate-200 focus:border-orange-500 outline-none transition text-sm text-slate-600" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1 ml-4">Jenis Kelamin</label>
                        <div class="flex gap-4 mt-2 ml-2">
                            <label class="flex items-center text-xs text-slate-600 cursor-pointer">
                                <input type="radio" name="gender" value="Laki-laki" {{ old('gender') == 'Laki-laki' ? 'checked' : '' }} class="mr-1 accent-orange-500" required> Laki-Laki
                            </label>
                            <label class="flex items-center text-xs text-slate-600 cursor-pointer">
                                <input type="radio" name="gender" value="Perempuan" {{ old('gender') == 'Perempuan' ? 'checked' : '' }} class="mr-1 accent-orange-500"> Perempuan
                            </label>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1 ml-4">Nomor HP (WhatsApp)</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="w-full px-5 py-2.5 rounded-full border border-slate-200 focus:border-orange-500 outline-none transition text-sm" placeholder="Contoh: 0812..." required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1 ml-4">Referral</label>
                        <input type="text" name="referral" value="{{ old('referral') }}" class="w-full px-5 py-2.5 rounded-full border border-slate-200 focus:border-orange-500 outline-none transition text-sm" placeholder="Kode Referral (Opsional)">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1 ml-4">Asal Sekolah / Instansi</label>
                    <input type="text" name="school" value="{{ old('school') }}" class="w-full px-5 py-2.5 rounded-full border border-slate-200 focus:border-orange-500 outline-none transition text-sm" placeholder="Nama Sekolah Anda" required>
                </div>

                <div class="text-center text-slate-400 text-xs font-semibold italic pt-2">---Akun Pendaftar---</div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1 ml-4">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="w-full px-5 py-2.5 rounded-full border border-slate-200 focus:border-orange-500 outline-none transition text-sm" placeholder="email@contoh.com" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1 ml-4">Username</label>
                        <input type="text" name="username" value="{{ old('username') }}" class="w-full px-5 py-2.5 rounded-full border border-slate-200 focus:border-orange-500 outline-none transition text-sm" placeholder="Username" required>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="relative">
                        <label class="block text-xs font-bold text-slate-700 mb-1 ml-4">Password</label>
                        <input id="password" type="password" name="password" class="w-full px-5 py-2.5 rounded-full border border-slate-200 focus:border-orange-500 outline-none transition text-sm" placeholder="••••••••" required>
                        <i id="togglePassword" class="far fa-eye absolute right-5 top-9 text-blue-900 text-xs cursor-pointer"></i>
                    </div>
                    <div class="relative">
                        <label class="block text-xs font-bold text-slate-700 mb-1 ml-4">Konfirmasi</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" class="w-full px-5 py-2.5 rounded-full border border-slate-200 focus:border-orange-500 outline-none transition text-sm" placeholder="••••••••" required>
                        <i id="toggleConfirmPassword" class="far fa-eye absolute right-5 top-9 text-blue-900 text-xs cursor-pointer"></i>
                    </div>
                </div>

                <div class="pt-4 flex flex-col items-center">
                    <button type="submit" class="bg-orange-500 text-white px-12 py-2.5 rounded-full font-bold shadow-lg hover:bg-orange-600 transition-all text-sm active:scale-95">
                        Daftar Sekarang
                    </button>
                    <p class="text-[10px] text-slate-500 mt-4">Sudah punya akun? <a href="{{ route('login') }}" class="text-orange-500 font-bold hover:underline">Login disini</a></p>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- 1. Logika Show/Hide Password ---
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
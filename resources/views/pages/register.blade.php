@extends('layouts.app')
@section('title', 'Daftar Akun')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-slate-50 py-12 px-10">
    <div class="bg-white w-full max-w-5xl flex rounded-[40px] shadow-2xl overflow-hidden border border-slate-100">
        
        <div class="hidden md:flex w-[35%] bg-orange-500 items-center justify-center p-12 relative overflow-hidden text-white flex-col">
            <div class="relative z-10 text-center">
                <i class="fas fa-paper-plane text-[100px] mb-8"></i>
                <h3 class="text-2xl font-black whitespace-nowrap">Wujudkan Impianmu!</h3>
                <p class="text-orange-50 text-sm mt-2 opacity-90">Bergabunglah dan jadilah juara kelas.</p>
            </div>
        </div>

        <div class="w-full md:w-[65%] p-10 lg:p-12 overflow-y-auto max-h-[90vh]">
            <div class="text-center mb-6">
                <div class="text-3xl font-black text-blue-900 mb-1">Mandala <span class="text-orange-500">Bimbel</span></div>
                <h2 class="text-xl font-bold text-slate-800">Buat Akun Baru</h2>
                <p class="text-slate-400 text-[10px] mt-1 uppercase tracking-widest">Isi data diri untuk mulai belajar!</p>
            </div>

            <form action="{{ route('register.store') }}" method="POST" class="space-y-4">
                @csrf
                
                <div class="text-center text-slate-400 text-xs font-semibold italic">---Identitas Pendaftar---</div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1 ml-4">Nama Lengkap</label>
                    <input type="text" name="name" class="w-full px-5 py-2.5 rounded-full border border-slate-200 focus:border-orange-500 outline-none transition text-sm" placeholder="Nama Lengkap Anda" required>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1 ml-4">Tanggal Lahir</label>
                        <input type="date" name="birth_date" class="w-full px-5 py-2.5 rounded-full border border-slate-200 focus:border-orange-500 outline-none transition text-sm text-slate-400" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1 ml-4">Jenis Kelamin</label>
                        <div class="flex gap-4 mt-2 ml-2">
                            <label class="flex items-center text-xs text-slate-600 cursor-pointer">
                                <input type="radio" name="gender" value="Laki-laki" class="mr-1 accent-orange-500"> Laki-Laki
                            </label>
                            <label class="flex items-center text-xs text-slate-600 cursor-pointer">
                                <input type="radio" name="gender" value="Perempuan" class="mr-1 accent-orange-500"> Perempuan
                            </label>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1 ml-4">Nomor HP</label>
                        <input type="text" name="phone" class="w-full px-5 py-2.5 rounded-full border border-slate-200 focus:border-orange-500 outline-none transition text-sm" placeholder="No HP" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1 ml-4">Referal</label>
                        <input type="text" name="referral" class="w-full px-5 py-2.5 rounded-full border border-slate-200 focus:border-orange-500 outline-none transition text-sm" placeholder="Kode Referal">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1 ml-4">Asal Sekolah / Instansi</label>
                    <input type="text" name="school" class="w-full px-5 py-2.5 rounded-full border border-slate-200 focus:border-orange-500 outline-none transition text-sm" placeholder="Nama Sekolah Anda" required>
                </div>

                <div class="text-center text-slate-400 text-xs font-semibold italic pt-2">---Akun Pendaftar---</div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1 ml-4">Email</label>
                        <input type="email" name="email" class="w-full px-5 py-2.5 rounded-full border border-slate-200 focus:border-orange-500 outline-none transition text-sm" placeholder="email@contoh.com" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1 ml-4">Username</label>
                        <input type="text" name="username" class="w-full px-5 py-2.5 rounded-full border border-slate-200 focus:border-orange-500 outline-none transition text-sm" placeholder="Username" required>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="relative">
                        <label class="block text-xs font-bold text-slate-700 mb-1 ml-4">Password</label>
                        <input type="password" name="password" class="w-full px-5 py-2.5 rounded-full border border-slate-200 focus:border-orange-500 outline-none transition text-sm" placeholder="••••••••" required>
                        <i class="far fa-eye absolute right-5 top-9 text-blue-900 text-xs cursor-pointer"></i>
                    </div>
                    <div class="relative">
                        <label class="block text-xs font-bold text-slate-700 mb-1 ml-4">Konfirmasi</label>
                        <input type="password" name="password_confirmation" class="w-full px-5 py-2.5 rounded-full border border-slate-200 focus:border-orange-500 outline-none transition text-sm" placeholder="••••••••" required>
                        <i class="far fa-eye absolute right-5 top-9 text-blue-900 text-xs cursor-pointer"></i>
                    </div>
                </div>

                <div class="pt-4 flex justify-start">
                    <button type="submit" class="bg-orange-500 text-white px-12 py-2.5 rounded-full font-bold shadow-lg hover:bg-orange-600 transition-all text-sm active:scale-95">
                        Kirim
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
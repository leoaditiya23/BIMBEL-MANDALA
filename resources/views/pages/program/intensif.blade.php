@extends('layouts.app')
@section('title', 'Pendaftaran Program Intensif - Mandala')

@section('content')
<div class="min-h-screen bg-[#FDFDFD] pb-24" x-data="{ 
    /* Ambil step dari URL (?step=3). Jika tidak ada, default ke 1 */
    step: {{ request()->get('step') ?? 1 }}, 
    metode: 'online', 
    kategori: 'UTBK-SAINTEK',
    batch: 'Batch 1 (Februari - Mei)',
    showPayment: false,
    buktiBayar: null,
    basePrice: 1250000,
    
    paketMateri: {
        'UTBK-SAINTEK': 'TPS, Literasi, Penalaran Matematika + Fisika, Kimia, Biologi, Matematika IPA',
        'UTBK-SOSHUM': 'TPS, Literasi, Penalaran Matematika + Ekonomi, Geografi, Sosiologi, Sejarah',
        'TKA-SD': 'Pendalaman Materi Tematik, Matematika, dan Persiapan Ujian Sekolah SD',
        'TKA-SMP': 'Fokus Materi Matematika, IPA, IPS, dan Bahasa Inggris Level SMP'
    },

    get totalPrice() {
        let total = this.basePrice;
        if (this.metode === 'offline') total += 500000;
        return total;
    },

    {{-- Fungsi Pesan Otomatis WA --}}
    sendWA() {
        let msg = `Halo Admin Mandala! %0A%0ASaya ingin konfirmasi pendaftaran Program Intensif:%0A%0A` +
                  `*Program:* ${this.kategori}%0A` +
                  (this.kategori.includes('UTBK') ? `*Batch:* ${this.batch}%0A` : '') +
                  `*Metode:* ${this.metode.toUpperCase()}%0A` +
                  `*Lokasi:* ${this.metode === 'offline' ? 'Semarang (Pusat)' : 'Online Class'}%0A` +
                  `*Total:* Rp ${this.totalPrice.toLocaleString('id-ID')}%0A%0AMohon bantuannya untuk proses verifikasi bukti bayar. Terima kasih!`;
        window.open(`https://wa.me/+6285540000900?text=${msg}`, '_blank');
    }
}">

    {{-- Minimalist Hero Header --}}
    <div class="bg-gradient-to-br from-orange-600 to-orange-500 pt-24 pb-40 px-6 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <svg class="h-full w-full" fill="none" viewBox="0 0 400 400">
                <defs><pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" stroke="white" stroke-width="1"/></pattern></defs>
                <rect width="100%" height="100%" fill="url(#grid)" />
            </svg>
        </div>
        
        <div class="relative z-10 container mx-auto text-center">
            <div class="inline-flex items-center space-x-2 bg-white/20 backdrop-blur-md px-4 py-1.5 rounded-full border border-white/30 mb-6">
                <span class="w-2 h-2 bg-white rounded-full animate-pulse"></span>
                <span class="text-white text-[10px] font-black uppercase tracking-[0.2em]">Pendaftaran Angkatan 2026</span>
            </div>
            <h1 class="text-5xl md:text-7xl font-black text-white uppercase tracking-tight leading-none">
                INTENSIVE <span class="text-blue-900">ACADEMY</span>
            </h1>
            <p class="text-orange-50 mt-6 max-w-2xl mx-auto font-medium text-lg leading-relaxed opacity-90">
                Akselerasi pemahaman materi dengan metode taktis dan efisien. 
                <span class="block mt-1 font-black text-white">Satu langkah lebih dekat dengan impianmu.</span>
            </p>
        </div>
    </div>

    <div class="container mx-auto px-6 -mt-24 relative z-20">
        <div class="max-w-5xl mx-auto">
            
            {{-- Professional Stepper --}}
            <div class="flex items-center justify-center mb-12 space-x-4">
                <template x-for="i in [1, 2, 3]">
                    <div class="flex items-center">
                        <div :class="step >= i ? 'bg-white text-orange-600 scale-110 shadow-xl border-white' : 'bg-orange-800/30 text-orange-200 border-transparent'" 
                             class="w-10 h-10 rounded-full flex items-center justify-center font-black transition-all duration-500 border-2">
                            <span x-text="i"></span>
                        </div>
                        <div x-show="i < 3" class="w-12 md:w-20 h-0.5 mx-2 rounded-full bg-white/20 overflow-hidden">
                            <div class="h-full bg-white transition-all duration-700" :style="`width: ${step > i ? '100%' : '0%'}`"></div>
                        </div>
                    </div>
                </template>
            </div>

            <div class="bg-white rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.05)] border border-slate-100 overflow-hidden">
                <div class="p-8 md:p-14">
                    
                    {{-- STEP 1: PROGRAM --}}
                    <div x-show="step === 1" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-4">
                        <div class="grid lg:grid-cols-2 gap-12">
                            <div>
                                <h3 class="text-xs font-black text-orange-600 uppercase tracking-widest mb-6 flex items-center">
                                    <span class="w-8 h-[2px] bg-orange-600 mr-3"></span> Pilih Fokus Program
                                </h3>
                                <div class="space-y-4">
                                    <template x-for="t in ['UTBK-SAINTEK', 'UTBK-SOSHUM', 'TKA-SD', 'TKA-SMP']">
                                        <label class="block cursor-pointer">
                                            <input type="radio" name="tipe" :value="t" x-model="kategori" class="hidden peer">
                                            <div class="p-6 border-2 border-slate-50 rounded-2xl peer-checked:border-orange-500 peer-checked:bg-orange-50/50 transition-all hover:bg-slate-50">
                                                <div class="flex justify-between items-center">
                                                    <div>
                                                        <h4 class="font-black text-slate-800 uppercase" x-text="t.replace('-', ' ')"></h4>
                                                        <p class="text-[10px] font-bold text-slate-400 uppercase mt-1" x-text="t.includes('UTBK') ? 'Persiapan Seleksi Nasional' : 'Penguasaan Materi Dasar'"></p>
                                                    </div>
                                                    <div class="w-6 h-6 rounded-full border-2 border-slate-200 flex items-center justify-center peer-checked:bg-orange-500">
                                                        <div class="w-2 h-2 bg-white rounded-full" x-show="kategori === t"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    </template>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-xs font-black text-orange-600 uppercase tracking-widest mb-6 flex items-center">
                                    <span class="w-8 h-[2px] bg-orange-600 mr-3"></span> Metode Pembelajaran
                                </h3>
                                <div class="grid grid-cols-1 gap-4">
                                    <label class="cursor-pointer">
                                        <input type="radio" x-model="metode" value="online" class="hidden peer">
                                        <div class="p-6 border-2 border-slate-50 rounded-2xl peer-checked:border-orange-500 peer-checked:bg-orange-50/50 transition-all">
                                            <div class="flex items-center space-x-4">
                                                <div class="w-12 h-12 bg-white shadow-sm border border-slate-100 rounded-xl flex items-center justify-center text-orange-500">
                                                    <i class="fas fa-video"></i>
                                                </div>
                                                <div>
                                                    <span class="block font-black uppercase text-sm text-slate-800">Online Intensive</span>
                                                    <span class="text-[10px] font-bold text-slate-400 uppercase">Interactive via Zoom</span>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" x-model="metode" value="offline" class="hidden peer">
                                        <div class="p-6 border-2 border-slate-50 rounded-2xl peer-checked:border-orange-500 peer-checked:bg-orange-50/50 transition-all">
                                            <div class="flex items-center space-x-4">
                                                <div class="w-12 h-12 bg-white shadow-sm border border-slate-100 rounded-xl flex items-center justify-center text-orange-500">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                </div>
                                                <div>
                                                    <span class="block font-black uppercase text-sm text-slate-800">Offline Camp</span>
                                                    <span class="text-[10px] font-bold text-orange-600 uppercase">Kantor Pusat Semarang</span>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <div x-show="metode === 'offline'" class="mt-6 p-5 bg-blue-50/50 rounded-2xl border border-blue-100 flex items-start space-x-3">
                                    <i class="fas fa-info-circle text-blue-500 mt-1"></i>
                                    <p class="text-[11px] font-bold text-blue-900 uppercase leading-relaxed">
                                        Pembelajaran tatap muka akan dilaksanakan di Mandala Learning Center Semarang.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <button @click="step = 2" class="w-full mt-12 bg-slate-900 text-white py-6 rounded-2xl font-black uppercase tracking-[0.2em] hover:bg-orange-600 transition-all shadow-xl">
                            Selanjutnya <i class="fas fa-chevron-right ml-2 text-xs"></i>
                        </button>
                    </div>

                    {{-- STEP 2: BATCH & DETAIL --}}
                    <div x-show="step === 2" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-4" x-cloak>
                        <div class="max-w-3xl mx-auto">
                            <template x-if="kategori.includes('UTBK')">
                                <div class="mb-12">
                                    <h3 class="text-xs font-black text-orange-600 uppercase tracking-widest mb-6 text-center">Pilih Periode Belajar</h3>
                                    <div class="grid md:grid-cols-2 gap-4">
                                        <template x-for="b in ['Batch 1 (Februari - Mei)', 'Batch 2 (Juni - Agustus)']">
                                            <button @click="batch = b" 
                                                    :class="batch === b ? 'bg-orange-600 text-white shadow-orange-200 shadow-2xl border-orange-300' : 'bg-slate-50 text-slate-400 hover:bg-slate-100 border-transparent'"
                                                    class="py-10 rounded-[2rem] font-black text-[11px] uppercase transition-all tracking-widest border-2" 
                                                    x-text="b">
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <div class="bg-slate-900 rounded-[2.5rem] p-10 text-white relative overflow-hidden shadow-2xl">
                                <div class="relative z-10">
                                    <div class="flex items-center space-x-3 mb-4 opacity-60">
                                        <i class="fas fa-book-open text-xs"></i>
                                        <span class="text-[10px] font-black uppercase tracking-widest">Kurikulum Program</span>
                                    </div>
                                    <h4 class="text-2xl font-black uppercase mb-4 tracking-tight" x-text="kategori.replace('-', ' ')"></h4>
                                    <p class="text-slate-400 font-medium leading-relaxed border-l-2 border-orange-500 pl-6" x-text="paketMateri[kategori]"></p>
                                </div>
                                <i class="fas fa-shield-alt absolute bottom-0 right-0 text-white/5 text-[150px] -mb-10 -mr-10"></i>
                            </div>

                            <div class="mt-12 flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
                                <button @click="step = 1" class="flex-1 border-2 border-slate-100 text-slate-400 py-6 rounded-2xl font-black uppercase tracking-widest hover:bg-slate-50 transition">Kembali</button>
                                
                                {{-- CEK LOGIN: Jika belum login, arahkan ke rute jembatan --}}
                                @auth
                                    <button @click="step = 3" class="flex-[2] bg-orange-600 text-white py-6 rounded-2xl font-black uppercase tracking-widest hover:bg-slate-900 transition shadow-xl">
                                        Lihat Ringkasan <i class="fas fa-file-invoice ml-2"></i>
                                    </button>
                                @else
                                    <a href="{{ route('pendaftaran.lanjut') }}" class="flex-[2] bg-blue-600 text-white py-6 rounded-2xl font-black uppercase tracking-widest hover:bg-blue-700 transition shadow-xl flex items-center justify-center">
                                        <span>Login untuk Melanjutkan</span>
                                        <i class="fas fa-sign-in-alt ml-2 text-xs"></i>
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>

                    {{-- STEP 3: INVOICE & BUKTI --}}
                    <div x-show="step === 3" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-4" x-cloak>
                        <div class="grid lg:grid-cols-5 gap-8 items-start">
                            <div class="lg:col-span-3 bg-white border border-slate-100 rounded-[2.5rem] p-8 md:p-12 shadow-sm">
                                <h3 class="text-2xl font-black text-slate-800 uppercase tracking-tighter mb-8">Ringkasan Pesanan</h3>
                                <div class="space-y-6">
                                    <div class="flex justify-between items-end border-b border-slate-50 pb-4">
                                        <div>
                                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Program Utama</p>
                                            <p class="font-black text-slate-800 uppercase" x-text="kategori"></p>
                                        </div>
                                        <p class="text-sm font-bold text-slate-500" x-text="'Rp ' + basePrice.toLocaleString('id-ID')"></p>
                                    </div>
                                    <template x-if="kategori.includes('UTBK')">
                                        <div class="flex justify-between items-end border-b border-slate-50 pb-4">
                                            <div>
                                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Periode Batch</p>
                                                <p class="font-black text-slate-800 uppercase" x-text="batch"></p>
                                            </div>
                                            <span class="text-[10px] font-black text-orange-600 uppercase bg-orange-50 px-2 py-1 rounded-md">Terpilih</span>
                                        </div>
                                    </template>
                                    <div class="flex justify-between items-end border-b border-slate-50 pb-4">
                                        <div>
                                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Metode & Lokasi</p>
                                            <p class="font-black text-slate-800 uppercase" x-text="metode === 'offline' ? 'Offline (Semarang)' : 'Online Class'"></p>
                                        </div>
                                        <p class="text-sm font-bold text-slate-500" x-text="metode === 'offline' ? '+ Rp 500.000' : 'Rp 0'"></p>
                                    </div>
                                    <div class="pt-4 flex justify-between items-center">
                                        <h4 class="text-sm font-black text-slate-800 uppercase">Total Bayar</h4>
                                        <h4 class="text-3xl font-black text-orange-600" x-text="'Rp ' + totalPrice.toLocaleString('id-ID')"></h4>
                                    </div>
                                </div>
                            </div>

                            <div class="lg:col-span-2 space-y-6">
                                <div class="bg-orange-50 rounded-3xl p-8 border border-orange-100 text-center">
                                    <img src="https://upload.wikimedia.org/wikipedia/id/thumb/f/fa/Bank_Mandiri_logo.svg/1200px-Bank_Mandiri_logo.svg.png" class="h-6 mx-auto mb-6 grayscale opacity-70" alt="Bank Mandiri">
                                    <p class="text-[10px] font-black text-orange-800 uppercase tracking-widest mb-2">Nomor Rekening</p>
                                    <div class="text-xl font-black text-slate-800 mb-6 tracking-wider">900 1234 5678 00</div>
                                    
                                    <label class="cursor-pointer group block">
                                        <div :class="buktiBayar ? 'bg-green-500 text-white border-green-600' : 'bg-white text-orange-600 border-orange-200'" 
                                             class="px-6 py-4 rounded-2xl font-black text-[10px] uppercase transition-all shadow-sm flex items-center justify-center space-x-2 border-2">
                                            <i class="fas" :class="buktiBayar ? 'fa-check-circle' : 'fa-camera'"></i>
                                            <span x-text="buktiBayar ? 'File Terpilih' : 'Upload Bukti Bayar'"></span>
                                        </div>
                                        <input type="file" class="hidden" @change="buktiBayar = $event.target.files[0]">
                                    </label>
                                    
                                    <p x-show="buktiBayar" class="text-[10px] font-bold text-green-600 mt-2 truncate" x-text="buktiBayar.name"></p>
                                </div>

                                <button @click="showPayment = true" 
                                        :disabled="!buktiBayar"
                                        :class="!buktiBayar ? 'opacity-50 cursor-not-allowed bg-slate-400' : 'bg-slate-900 hover:bg-orange-600'"
                                        class="w-full text-white py-6 rounded-3xl font-black uppercase tracking-widest transition shadow-2xl">
                                    Konfirmasi Selesai
                                </button>
                                
                                <button @click="step = 2" class="w-full text-slate-400 font-black uppercase text-[10px] tracking-widest">Kembali ke sebelumnya</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- MODAL POP UP --}}
    <div x-show="showPayment" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-slate-900/80 backdrop-blur-md" x-cloak x-transition>
        <div class="bg-white w-full max-w-md rounded-[3.5rem] p-12 text-center shadow-2xl relative">
            <div class="w-24 h-24 bg-orange-50 text-orange-600 rounded-full flex items-center justify-center text-4xl mx-auto mb-8 border-4 border-white shadow-xl">
                <i class="fas fa-clock animate-pulse"></i>
            </div>
            <h2 class="text-4xl font-black text-slate-800 uppercase tracking-tighter mb-4">Satu Langkah Lagi!</h2>
            <p class="text-slate-400 text-[11px] font-bold uppercase tracking-[0.15em] leading-relaxed mb-10 px-2">
                Pendaftaranmu sedang dalam tahap <span class="text-orange-600">Verifikasi Antrean</span>. Silakan klik tombol di bawah untuk mempercepat konfirmasi.
            </p>
            
            <div class="space-y-4">
                <button @click="sendWA()" class="flex items-center justify-center space-x-3 w-full bg-[#25D366] text-white py-5 rounded-2xl font-black uppercase tracking-widest hover:scale-[1.02] transition-all shadow-xl shadow-green-200">
                    <i class="fab fa-whatsapp text-xl"></i>
                    <span>Konfirmasi via WA</span>
                </button>
                <a href="/" class="block w-full bg-slate-50 text-slate-400 py-5 rounded-2xl font-black uppercase tracking-widest hover:bg-slate-100 transition-all text-xs">
                    Selesai & Ke Beranda
                </a>
            </div>
        </div>
    </div>

</div>

<style>
    [x-cloak] { display: none !important; }
    * { font-style: normal !important; font-family: 'Plus Jakarta Sans', sans-serif; }
    h1, h2, h3, h4, span, p, button { font-style: normal !important; }
</style>
@endsection
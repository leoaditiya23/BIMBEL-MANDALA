@extends('layouts.app')
@section('title', 'Pendaftaran Program Intensif - Mandala')

@section('content')
<div class="min-h-screen bg-[#FDFDFD] pb-24" x-data="{ 
    step: {{ request()->get('step') ?? 1 }}, 
    metode: 'online', 
    kategori: 'UTBK-SAINTEK',
    batch: 'Batch 1 (Februari - Mei)',
    showPayment: false,
    buktiBayar: null,

    // Ambil data harga dari database
    programsData: {!! json_encode($programsByName ?? []) !!},

    // REVISI: Tambahkan getter listHarga agar looping di baris 83 tidak error
    listHarga: {
        'UTBK-SAINTEK': {{ DB::table('programs')->where('type', 'intensif')->where('name', 'UTBK-SAINTEK')->value('price') ?? 1500000 }},
        'UTBK-SOSHUM': {{ DB::table('programs')->where('type', 'intensif')->where('name', 'UTBK-SOSHUM')->value('price') ?? 1500000 }},
        'TKA-SD': {{ DB::table('programs')->where('type', 'intensif')->where('name', 'TKA-SD')->value('price') ?? 1500000 }},
        'TKA-SMP': {{ DB::table('programs')->where('type', 'intensif')->where('name', 'TKA-SMP')->value('price') ?? 1500000 }}
    },

    // Dapatkan harga berdasarkan kategori yang dipilih
    getHarga(kategori) {
        return this.listHarga[kategori] ?? 1500000;
    },

    getTotalPrice() {
        return this.getHarga(this.kategori);
    },

    paketMateri: {
        'UTBK-SAINTEK': 'TPS, Literasi, Penalaran Matematika + Fisika, Kimia, Biologi, Matematika IPA',
        'UTBK-SOSHUM': 'TPS, Literasi, Penalaran Matematika + Ekonomi, Geografi, Sosiologi, Sejarah',
        'TKA-SD': 'Pendalaman Materi Tematik, Matematika, dan Persiapan Ujian Sekolah SD',
        'TKA-SMP': 'Fokus Materi Matematika, IPA, IPS, dan Bahasa Inggris Level SMP'
    },

    sendWA() {
        let msg = `Halo Admin Mandala! %0A%0ASaya ingin konfirmasi pendaftaran Program Intensif:%0A%0A` +
                  `*Program:* ${this.kategori}%0A` +
                  (this.kategori.includes('UTBK') ? `*Batch:* ${this.batch}%0A` : '') +
                  `*Metode:* ${this.metode.toUpperCase()}%0A` +
                  `*Total:* Rp ${this.getTotalPrice().toLocaleString('id-ID')}`;
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
                    
                    {{-- STEP 1: PILIH PROGRAM --}}
                    <div x-show="step === 1" x-transition:enter="transition ease-out duration-300" class="space-y-12">
                        <div class="grid lg:grid-cols-2 gap-12">
                            <div>
                                <h3 class="text-xs font-black text-orange-600 uppercase mb-6 tracking-widest flex items-center">
                                    <span class="w-8 h-[2px] bg-orange-600 mr-3"></span> Pilih Fokus Program
                                </h3>
                                <div class="space-y-4">
                                    <template x-for="t in ['UTBK-SAINTEK', 'UTBK-SOSHUM', 'TKA-SD', 'TKA-SMP']">
                                        <label class="block cursor-pointer">
                                            <input type="radio" :value="t" x-model="kategori" class="hidden peer">
                                            <div class="p-6 border-2 border-slate-50 rounded-2xl peer-checked:border-orange-500 peer-checked:bg-orange-50 transition-all hover:bg-slate-50">
                                                <div class="flex justify-between items-center">
                                                    <div>
                                                        <h4 class="font-black text-slate-800 uppercase text-sm" x-text="t.replace('-', ' ')"></h4>
                                                        <p class="text-[10px] font-black text-orange-600 mt-1 uppercase" x-text="'Biaya: Rp ' + listHarga[t].toLocaleString('id-ID')"></p>
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
                                <h3 class="text-xs font-black text-orange-600 uppercase mb-6 tracking-widest flex items-center">
                                    <span class="w-8 h-[2px] bg-orange-600 mr-3"></span> Metode Belajar
                                </h3>
                                <div class="space-y-4 mb-8">
                                    <label class="cursor-pointer block">
                                        <input type="radio" x-model="metode" value="online" class="hidden peer">
                                        <div class="p-5 border-2 border-slate-50 rounded-2xl peer-checked:border-orange-500 peer-checked:bg-orange-50 transition-all">
                                            <span class="font-black uppercase text-xs">Online Intensive (Via Zoom)</span>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer block">
                                        <input type="radio" x-model="metode" value="offline" class="hidden peer">
                                        <div class="p-5 border-2 border-slate-50 rounded-2xl peer-checked:border-orange-500 peer-checked:bg-orange-50 transition-all">
                                            <div class="flex justify-between items-center">
                                                <span class="font-black uppercase text-xs text-slate-800">Offline Camp Semarang</span>
                                                <span class="text-[9px] font-black bg-green-100 text-green-600 px-2 py-1 rounded">HARGA SAMA</span>
                                            </div>

                                            {{-- Detail Lokasi Tamansari Hills --}}
                                            <div x-show="metode === 'offline'" x-transition class="mt-4 p-4 bg-white rounded-xl border border-blue-100 shadow-sm">
                                                <div class="flex items-start gap-3 text-blue-900">
                                                    <i class="fas fa-map-marker-alt mt-1"></i>
                                                    <div>
                                                        <p class="text-[10px] font-black uppercase">Tamansari Hills Residence</p>
                                                        <p class="text-[9px] text-slate-500 italic leading-relaxed mt-1">
                                                            Blok Emerald No. 12, Mangunharjo, Tembalang. Lingkungan sejuk dan tenang.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <div class="bg-slate-900 rounded-3xl p-8 text-white shadow-2xl">
                                    <p class="text-[10px] font-black text-orange-500 uppercase mb-2 tracking-widest">Total Biaya</p>
                                    <h4 class="text-4xl font-black" x-text="'Rp ' + getTotalPrice().toLocaleString('id-ID')"></h4>
                                </div>
                            </div>
                        </div>
                        <button @click="step = 2; window.scrollTo({top: 0, behavior: 'smooth'})" 
                                class="w-full bg-slate-900 text-white py-6 rounded-3xl font-black uppercase tracking-[0.2em] hover:bg-orange-600 transition-all shadow-xl">
                            Lanjutkan Ke Jadwal
                        </button>
                    </div>

                    {{-- STEP 2: JADWAL & MATERI --}}
                    <div x-show="step === 2" x-cloak x-transition:enter="transition ease-out duration-300" class="max-w-3xl mx-auto space-y-8">
                        <template x-if="kategori.includes('UTBK')">
                            <div class="bg-white rounded-[2rem] p-8 border border-slate-100 shadow-sm text-center">
                                <h3 class="text-xs font-black text-orange-600 uppercase tracking-widest mb-6 italic">Pilih Batch Belajarmu</h3>
                                <div class="grid md:grid-cols-2 gap-4">
                                    <template x-for="b in ['Batch 1 (Februari - Mei)', 'Batch 2 (Juni - Agustus)']">
                                        <button @click="batch = b" 
                                                :class="batch === b ? 'border-orange-500 bg-orange-50 text-orange-700 shadow-inner' : 'border-slate-100 text-slate-400 hover:bg-slate-50'" 
                                                class="py-8 px-4 rounded-2xl font-black text-[11px] uppercase border-2 transition-all tracking-widest">
                                            <span x-text="b"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </template>

                        {{-- DETAIL KURIKULUM --}}
                        <div class="bg-slate-900 rounded-[2.5rem] p-10 text-white relative overflow-hidden shadow-2xl">
                            <div class="relative z-10">
                                <div class="flex items-center space-x-3 mb-6 opacity-60">
                                    <i class="fas fa-book-open text-xs text-orange-500"></i>
                                    <span class="text-[10px] font-black uppercase tracking-widest">Detail Kurikulum & Materi</span>
                                </div>
                                <h4 class="text-2xl font-black uppercase mb-4 tracking-tight" x-text="kategori.replace('-', ' ')"></h4>
                                <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/10">
                                    <p class="text-slate-300 font-medium leading-relaxed text-sm" x-text="paketMateri[kategori]"></p>
                                </div>
                            </div>
                            <i class="fas fa-graduation-cap absolute bottom-[-20px] right-[-20px] text-white/5 text-[150px] rotate-12"></i>
                        </div>

                        {{-- TOMBOL NAVIGASI --}}
                        <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4 pt-4">
                            <button @click="step = 1" class="flex-1 border-2 border-slate-100 text-slate-400 py-6 rounded-2xl font-black uppercase tracking-widest hover:bg-slate-50 transition flex items-center justify-center">
                                <i class="fas fa-arrow-left mr-2 text-xs"></i> Kembali
                            </button>
                            
                            @auth
                                <button @click="step = 3" class="flex-[2] bg-orange-600 text-white py-6 rounded-2xl font-black uppercase tracking-widest hover:bg-slate-900 transition shadow-xl shadow-orange-200 flex items-center justify-center">
                                    Cek Ringkasan Bayar <i class="fas fa-receipt ml-2"></i>
                                </button>
                            @else
                                <a href="{{ route('pendaftaran.lanjut', ['type' => 'intensif']) }}" 
                                   class="flex-[2] bg-blue-600 text-white py-6 rounded-2xl font-black uppercase tracking-widest hover:bg-blue-700 transition shadow-xl flex items-center justify-center">
                                    <span>Login untuk Melanjutkan</span>
                                    <i class="fas fa-sign-in-alt ml-2 text-xs"></i>
                                </a>
                            @endauth
                        </div>
                    </div>

                    {{-- STEP 3: INVOICE & BUKTI --}}
                    <div x-show="step === 3" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-4" x-cloak>
                        <form action="{{ route('enroll.program') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <input type="hidden" name="program_id" :value="
                                kategori === 'UTBK-SAINTEK' ? 1 : 
                                (kategori === 'UTBK-SOSHUM' ? 2 : 
                                (kategori === 'TKA-SD' ? 3 : 4))
                            ">
                            <input type="hidden" name="total_harga" :value="getTotalPrice()">

                            <div class="grid lg:grid-cols-5 gap-8 items-start">
                                <div class="lg:col-span-3 bg-slate-50 border border-slate-100 rounded-[2rem] p-8">
                                    <h3 class="text-xl font-black text-slate-800 uppercase tracking-tighter mb-6 flex items-center">
                                        <i class="fas fa-file-invoice-dollar mr-3 text-orange-600"></i> Ringkasan Pesanan
                                    </h3>
                                    <div class="space-y-4">
                                        <div class="flex justify-between pb-4 border-b border-slate-200">
                                            <span class="text-[10px] font-black text-slate-400 uppercase">Program</span>
                                            <span class="font-black text-slate-800 uppercase text-xs" x-text="kategori"></span>
                                        </div>
                                        
                                        <div class="pt-6 bg-slate-900 rounded-3xl p-6 text-white shadow-lg border-b-4 border-orange-500">
                                            <span class="text-[10px] font-black text-white/50 uppercase tracking-widest">Total Pembayaran (+Kode Unik)</span>
                                            <div class="flex items-baseline gap-1 mt-1">
                                                <h3 class="text-3xl font-black text-orange-500" 
                                                    x-text="'Rp ' + (getTotalPrice() + parseInt('{{ substr(preg_replace('/[^0-9]/', '', Auth::user()->whatsapp ?? '000'), -3) }}')).toLocaleString('id-ID')">
                                                </h3>
                                            </div>
                                            <p class="text-[9px] text-white/70 mt-3 leading-relaxed italic">
                                                <i class="fas fa-magic mr-1 text-orange-400"></i>
                                                Sistem menyisipkan 3 digit terakhir nomor WA Anda sebagai kode verifikasi. Mohon transfer <strong>tepat</strong> sesuai angka di atas.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="lg:col-span-2 space-y-4">
                                    <div class="bg-orange-50 rounded-3xl p-6 border border-orange-100 text-center">
                                        <p class="text-[10px] font-black text-orange-800 uppercase tracking-widest mb-1">Transfer Ke Mandiri</p>
                                        <div class="text-xl font-black text-slate-800">900 1234 5678 00</div>
                                        <p class="text-[9px] font-bold text-slate-400 mb-6 uppercase text-center">A.N MANDALA ACADEMY</p>
                                        
                                        <label class="cursor-pointer group block">
                                            <div :class="buktiBayar ? 'bg-emerald-500 text-white border-emerald-600' : 'bg-white text-orange-600 border-orange-200'" 
                                                 class="px-6 py-4 rounded-2xl font-black text-[10px] uppercase transition-all shadow-sm flex items-center justify-center space-x-2 border-2">
                                                <i class="fas" :class="buktiBayar ? 'fa-check-circle' : 'fa-camera'"></i>
                                                <span x-text="buktiBayar ? 'Bukti Terupload' : 'Upload Bukti Bayar'"></span>
                                            </div>
                                            <input type="file" name="bukti_pembayaran" class="hidden" required
                                                   @change="buktiBayar = $event.target.files[0]">
                                        </label>
                                        @error('bukti_pembayaran')
                                            <p class="text-red-500 text-[10px] mt-2 font-bold">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <button type="submit" 
                                            :disabled="!buktiBayar"
                                            :class="!buktiBayar ? 'opacity-50 cursor-not-allowed bg-slate-400' : 'bg-slate-900 hover:bg-orange-600 active:scale-95'"
                                            class="w-full text-white py-6 rounded-3xl font-black uppercase tracking-widest transition-all shadow-2xl">
                                        Konfirmasi Sekarang
                                    </button>

                                    <button type="button" @click="step = 2" 
                                            class="w-full text-slate-400 font-black uppercase text-[10px] tracking-widest hover:text-slate-600 transition text-center">
                                        Kembali
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
    * { font-style: normal !important; font-family: 'Plus Jakarta Sans', sans-serif; }
</style>
@endsection
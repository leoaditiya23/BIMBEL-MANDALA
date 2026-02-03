@extends('layouts.app')
@section('title', 'Pendaftaran Program Reguler - Mandala')

@section('content')
<div class="min-h-screen bg-slate-50 pb-20" x-data="{ 
    // 1. LOGIKA POSISI STEP (Membaca parameter ?step dari Controller/URL)
    step: {{ $step ?? 1 }}, 
    
    // 2. LOGIKA DATA (Ambil dari sessionStorage jika ada)
    metode: sessionStorage.getItem('reg_metode') || 'online', 
    jenjang: sessionStorage.getItem('reg_jenjang') || '', 
    tipePaket: sessionStorage.getItem('reg_tipePaket') || 'eceran',
    selectedMapel: JSON.parse(sessionStorage.getItem('reg_selectedMapel')) || [],
    mauMengaji: sessionStorage.getItem('reg_mauMengaji') === 'true',
    lokasi: sessionStorage.getItem('reg_lokasi') || '',
    
    buktiTransfer: null,
    showSuccessModal: false,
    
    // Fungsi simpan state agar tidak hilang saat redirect login
    saveToSession() {
        sessionStorage.setItem('reg_metode', this.metode);
        sessionStorage.setItem('reg_jenjang', this.jenjang);
        sessionStorage.setItem('reg_tipePaket', this.tipePaket);
        sessionStorage.setItem('reg_selectedMapel', JSON.stringify(this.selectedMapel));
        sessionStorage.setItem('reg_mauMengaji', this.mauMengaji);
        sessionStorage.setItem('reg_lokasi', this.lokasi);
    },

    prices: {
        'TK': 125000,
        'SD': 150000,
        'SMP': 175000,
        'SMA': 200000,
        'borongan': 650000,
        'add_mengaji': 50000 
    },

    listMapel: {
        'TK': ['Calistung (Baca Tulis Hitung)', 'Mewarnai & Kreativitas', 'Bahasa Inggris Dasar'],
        'SD': ['Matematika SD', 'IPA SD', 'Bahasa Indonesia SD', 'Bahasa Inggris SD', 'Tematik'],
        'SMP': ['Matematika SMP', 'IPA Terpadu', 'Bahasa Inggris SMP', 'Bahasa Indonesia SMP', 'Fisika SMP', 'Biologi SMP', 'IPS SMP'],
        'SMA': ['Matematika Wajib', 'Matematika Peminatan', 'Fisika', 'Kimia', 'Biologi', 'Ekonomi', 'Geografi', 'Sejarah', 'Sosiologi', 'Bahasa Inggris', 'Bahasa Indonesia']
    },

    listLokasi: [
        'Semarang Barat (Ngaliyan)', 'Semarang Timur (Pedurungan)', 
        'Semarang Selatan (Banyumanik)', 'Semarang Tengah (Gajahmada)', 'Luar Kota (Area Khusus)'
    ],

    toggleMapel(item) {
        if (this.selectedMapel.includes(item)) {
            this.selectedMapel = this.selectedMapel.filter(i => i !== item);
        } else {
            this.selectedMapel.push(item);
        }
        this.saveToSession();
    },

    handleFileUpload(event) {
        const file = event.target.files[0];
        if (file) {
            this.buktiTransfer = URL.createObjectURL(file);
        }
    },

    get totalPrice() {
        let total = 0;
        if (this.tipePaket === 'borongan') {
            total = this.prices.borongan;
        } else {
            let pricePerMapel = this.prices[this.jenjang] || 0;
            total = this.selectedMapel.length * pricePerMapel;
        }
        if (this.mauMengaji) total += this.prices.add_mengaji;
        if (this.metode === 'offline') total += 100000;
        return total;
    }
}" 
x-init="$watch('metode', () => saveToSession()); $watch('jenjang', () => saveToSession()); $watch('tipePaket', () => saveToSession()); $watch('mauMengaji', () => saveToSession()); $watch('lokasi', () => saveToSession());"
class="relative">

    {{-- Header Section --}}
    <div class="bg-slate-900 pt-20 pb-32 px-6 relative overflow-hidden text-center">
        <div class="absolute top-0 right-0 w-96 h-96 bg-blue-600/10 rounded-full -mr-20 -mt-20 blur-3xl"></div>
        <div class="relative z-10 container mx-auto">
            <span class="text-blue-400 font-bold tracking-widest text-xs uppercase">Bimbingan Belajar Mandala</span>
            <h1 class="text-5xl md:text-6xl font-black text-white uppercase tracking-tighter mt-2">
                Program <span class="text-blue-500">Reguler</span>
            </h1>
            <p class="text-slate-400 mt-4 max-w-xl mx-auto font-medium">
                Belajar lebih efektif dengan bantuan <span class="text-orange-500 font-bold">Personal Mentor</span> yang fokus pada kemajuanmu.
            </p>
        </div>
    </div>

    <div class="container mx-auto px-6 -mt-20 relative z-20">
        <div class="max-w-4xl mx-auto">
            
            {{-- Stepper Progress --}}
            <div class="flex items-center justify-between mb-10 px-4 md:px-10">
                <template x-for="(label, i) in ['Metode', 'Konfigurasi', 'Pembayaran']">
                    <div class="flex items-center flex-1 last:flex-none">
                        <div class="flex flex-col items-center">
                            <div :class="step >= (i+1) ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/40' : 'bg-white text-slate-300'" 
                                 class="w-12 h-12 rounded-2xl flex items-center justify-center font-bold transition-all duration-300 mb-2 border-2 border-transparent"
                                 x-text="i+1"></div>
                            <span class="text-[10px] uppercase font-bold tracking-wider" :class="step >= (i+1) ? 'text-blue-600' : 'text-slate-400'" x-text="label"></span>
                        </div>
                        <div x-show="i < 2" :class="step > (i+1) ? 'bg-blue-600' : 'bg-slate-200'" class="h-[2px] flex-grow mx-4 -mt-6 rounded-full transition-all"></div>
                    </div>
                </template>
            </div>

            <div class="bg-white rounded-[2rem] shadow-2xl p-8 md:p-14 border border-slate-100">
                
                <form action="{{ route('enroll.program') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    {{-- DATA TERSEMBUNYI UNTUK DATABASE --}}
                    <input type="hidden" name="program_id" value="1">
                    <input type="hidden" name="metode" :value="metode">
                    <input type="hidden" name="jenjang" :value="jenjang">
                    <input type="hidden" name="total_harga" :value="totalPrice">
                    
                    {{-- KIRIM ARRAY MAPEL KE LARAVEL --}}
                    <template x-for="mapel in selectedMapel">
                        <input type="hidden" name="selected_mapel[]" :value="mapel">
                    </template>

                    {{-- STEP 1: METODE --}}
                    <div x-show="step === 1" x-transition>
                        <h3 class="text-xl font-bold text-slate-800 mb-8 flex items-center gap-2 uppercase tracking-tight">
                            <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center text-sm">1</span>
                            PILIH METODE BELAJAR
                        </h3>

                        <div class="grid md:grid-cols-2 gap-8">
                            <label @click="metode = 'online'; lokasi = ''" class="cursor-pointer group">
                                <input type="radio" name="metode_radio" value="online" x-model="metode" class="hidden">
                                <div class="p-8 border-2 border-slate-100 rounded-[2rem] h-full transition-all hover:border-blue-200 text-center md:text-left" :class="metode === 'online' ? 'border-blue-600 bg-blue-50' : ''">
                                    <div class="w-16 h-16 bg-blue-600 text-white rounded-2xl flex items-center justify-center mb-6 mx-auto md:mx-0">
                                        <i class="fas fa-video text-2xl"></i>
                                    </div>
                                    <h4 class="font-bold text-xl text-slate-800 uppercase tracking-tight">Reguler Online</h4>
                                    <p class="text-slate-500 text-sm mt-2 leading-relaxed">Sesi belajar interaktif via Zoom/Gmeet dari mana saja.</p>
                                </div>
                            </label>

                            <label @click="metode = 'offline'" class="cursor-pointer group">
                                <input type="radio" name="metode_radio" value="offline" x-model="metode" class="hidden">
                                <div class="p-8 border-2 border-slate-100 rounded-[2rem] h-full transition-all hover:border-blue-200 text-center md:text-left" :class="metode === 'offline' ? 'border-blue-600 bg-blue-50' : ''">
                                    <div class="w-16 h-16 bg-slate-900 text-white rounded-2xl flex items-center justify-center mb-6 mx-auto md:mx-0">
                                        <i class="fas fa-car text-2xl"></i>
                                    </div>
                                    <h4 class="font-bold text-xl text-slate-800 uppercase tracking-tight">Reguler Offline</h4>
                                    <p class="text-slate-500 text-sm mt-2 leading-relaxed">Mentor datang ke rumah (Area Semarang & sekitarnya).</p>
                                    
                                    <div x-show="metode === 'offline'" class="mt-6 space-y-2 text-left" @click.stop x-transition>
                                        <span class="text-[10px] font-bold text-blue-600 uppercase tracking-widest">PILIH WILAYAH</span>
                                        <select name="lokasi_input" x-model="lokasi" class="w-full bg-white border-2 border-slate-100 p-4 rounded-2xl text-sm font-bold outline-none focus:border-blue-600 transition-all">
                                            <option value="">-- LOKASI SEMARANG --</option>
                                            <template x-for="loc in listLokasi">
                                                <option :value="loc" x-text="loc"></option>
                                            </template>
                                        </select>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="mt-14">
                            <button type="button" @click="step = 2; saveToSession()" :disabled="metode === 'offline' && !lokasi"
                                    class="w-full bg-blue-600 text-white py-6 rounded-[1.5rem] font-bold uppercase tracking-[0.2em] disabled:opacity-50 shadow-xl shadow-blue-200 hover:bg-blue-700 transition-all">
                                PILIH PAKET BELAJAR
                            </button>
                        </div>
                    </div>

                    {{-- STEP 2: KONFIGURASI --}}
                    <div x-show="step === 2" x-transition x-cloak>
                        <div class="space-y-12">
                            <section>
                                <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2 uppercase tracking-tight">
                                    <span class="w-7 h-7 bg-blue-600 text-white rounded flex items-center justify-center text-xs">1</span>
                                    JENJANG SEKOLAH
                                </h3>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <template x-for="j in ['TK', 'SD', 'SMP', 'SMA']">
                                        <button type="button" @click="jenjang = j; selectedMapel = []; saveToSession()" 
                                                :class="jenjang === j ? 'bg-blue-600 text-white shadow-lg border-blue-600' : 'bg-white text-slate-500 border-slate-200 hover:border-blue-400'"
                                                class="py-5 border-2 rounded-2xl font-black transition-all uppercase text-sm" x-text="j"></button>
                                    </template>
                                </div>
                            </section>

                            <section x-show="jenjang" x-transition>
                                <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2 uppercase tracking-tight">
                                    <span class="w-7 h-7 bg-blue-600 text-white rounded flex items-center justify-center text-xs">2</span>
                                    PILIHAN PAKET BELAJAR
                                </h3>
                                <div class="grid md:grid-cols-2 gap-6">
                                    <button type="button" @click="tipePaket = 'eceran'; selectedMapel = []; saveToSession()" 
                                            :class="tipePaket === 'eceran' ? 'border-blue-600 bg-blue-50/50 shadow-inner' : 'border-slate-100 bg-slate-50'"
                                            class="p-8 border-2 rounded-3xl text-left transition-all relative">
                                        <span class="font-bold text-slate-800 uppercase block tracking-tight">SATUAN / PER MAPEL</span>
                                        <p class="text-xs text-slate-500 mt-2 font-medium">Fokus pada mata pelajaran tertentu saja.</p>
                                    </button>
                                    <button type="button" @click="tipePaket = 'borongan'; selectedMapel = ['Semua Mata Pelajaran']; saveToSession()" 
                                            :class="tipePaket === 'borongan' ? 'border-orange-500 bg-orange-50/50' : 'border-slate-100 bg-slate-50'"
                                            class="p-8 border-2 rounded-3xl text-left transition-all relative overflow-hidden">
                                        <span class="font-bold text-orange-600 uppercase block tracking-tight">PAKET BORONGAN</span>
                                        <p class="text-xs text-slate-500 mt-2 font-medium">Semua materi kurikulum sekolah.</p>
                                        <div class="absolute top-0 right-0 bg-orange-500 text-white text-[10px] font-black px-6 py-1 rounded-bl-xl shadow-sm uppercase tracking-widest">HEMAT</div>
                                    </button>
                                </div>
                            </section>

                            <section x-show="jenjang" x-transition>
                                <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2 uppercase tracking-tight">
                                    <span class="w-7 h-7 bg-blue-600 text-white rounded flex items-center justify-center text-xs">3</span>
                                    EKSTRA PENDAMPINGAN
                                </h3>
                                <div @click="mauMengaji = !mauMengaji; saveToSession()" 
                                     :class="mauMengaji ? 'border-emerald-500 bg-emerald-50' : 'border-slate-100 bg-slate-50'"
                                     class="p-6 border-2 rounded-3xl cursor-pointer flex items-center justify-between transition-all hover:border-emerald-200">
                                    <div class="flex items-center gap-5">
                                        <div :class="mauMengaji ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-400'" class="w-14 h-14 rounded-2xl flex items-center justify-center text-xl">
                                            <i class="fas fa-book-quran"></i>
                                        </div>
                                        <div>
                                            <span class="block font-bold text-slate-800 uppercase text-sm">TAMBAHAN MENGAJI / IQRA</span>
                                            <span class="text-[11px] font-bold text-emerald-600 uppercase tracking-widest">+ Rp 50.000 / BULAN</span>
                                        </div>
                                    </div>
                                    <div :class="mauMengaji ? 'bg-emerald-500 scale-110' : 'bg-slate-300'" class="w-7 h-7 rounded-full border-4 border-white shadow-md transition-all"></div>
                                </div>
                            </section>

                            <section x-show="tipePaket === 'eceran' && jenjang" x-transition>
                                <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2 uppercase tracking-tight">
                                    <span class="w-7 h-7 bg-blue-600 text-white rounded flex items-center justify-center text-xs">4</span>
                                    PILIH MATA PELAJARAN
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <template x-for="m in listMapel[jenjang]">
                                        <div @click="toggleMapel(m)" 
                                             :class="selectedMapel.includes(m) ? 'bg-slate-900 text-white border-slate-900 shadow-lg translate-x-1' : 'bg-white border-slate-200 text-slate-600 hover:border-blue-400'"
                                             class="p-5 border-2 rounded-2xl cursor-pointer flex items-center justify-between transition-all">
                                            <span class="text-sm font-bold uppercase tracking-tight" x-text="m"></span>
                                            <i class="fas" :class="selectedMapel.includes(m) ? 'fa-check-square' : 'fa-plus text-slate-200'"></i>
                                        </div>
                                    </template>
                                </div>
                            </section>
                        </div>

                        <div class="mt-16 flex flex-col md:flex-row gap-4">
                            <button type="button" @click="step = 1" class="flex-1 bg-slate-100 text-slate-500 py-6 rounded-2xl font-bold uppercase hover:bg-slate-200 transition">KEMBALI</button>
                            
                            @auth
                                {{-- JIKA LOGIN: Lanjut ke Step 3 --}}
                                <button type="button" 
                                        @click="if(selectedMapel.length > 0) { step = 3; saveToSession(); }" 
                                        :disabled="selectedMapel.length === 0"
                                        class="flex-[2] bg-slate-900 text-white py-6 rounded-2xl font-bold uppercase tracking-[0.2em] disabled:opacity-50">
                                    LANJUT PEMBAYARAN
                                </button>
                            @else
                                {{-- JIKA BELUM LOGIN: Lempar ke Jembatan agar balik ke Step 3 --}}
                                <a :href="'{{ route('pendaftaran.lanjut') }}?type=reguler'" 
                                   @click="saveToSession()"
                                   class="flex-[2] bg-blue-600 text-white py-6 rounded-2xl font-bold uppercase tracking-[0.2em] text-center flex items-center justify-center shadow-lg shadow-blue-200">
                                    LOGIN UNTUK MELANJUTKAN
                                </a>
                            @endauth
                        </div>
                    </div>

                    {{-- STEP 3: RINGKASAN & UPLOAD --}}
                    <div x-show="step === 3" x-transition x-cloak>
                        <div class="grid md:grid-cols-2 gap-10">
                            <div class="space-y-6">
                                <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">RINGKASAN PESANAN</h3>
                                <div class="bg-slate-900 rounded-[2rem] p-8 text-white shadow-xl relative overflow-hidden">
                                    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-600/20 rounded-full -mr-16 -mt-16 blur-2xl"></div>
                                    <div class="space-y-4 relative z-10">
                                        <div class="flex justify-between border-b border-white/10 pb-3">
                                            <span class="text-[10px] text-white/50 font-bold uppercase tracking-widest">JENJANG</span>
                                            <span class="font-bold text-sm uppercase" x-text="jenjang"></span>
                                        </div>
                                        <div class="border-b border-white/10 pb-4">
                                            <span class="text-[10px] text-white/50 font-bold uppercase tracking-widest block mb-2">MAPEL:</span>
                                            <div class="flex flex-wrap gap-1.5">
                                                <template x-for="m in selectedMapel">
                                                    <span class="bg-white/10 text-[9px] font-bold px-3 py-1 rounded-lg uppercase" x-text="m"></span>
                                                </template>
                                                <template x-if="mauMengaji">
                                                    <span class="bg-emerald-500/20 text-emerald-400 text-[9px] font-bold px-3 py-1 rounded-lg uppercase">PLUS MENGAJI</span>
                                                </template>
                                            </div>
                                        </div>
                                        <div class="pt-2">
                                            <p class="text-[10px] text-white/50 font-bold uppercase tracking-widest">TOTAL INVESTASI</p>
                                            <h3 class="text-4xl font-black tracking-tighter mt-1" x-text="'Rp ' + totalPrice.toLocaleString('id-ID')"></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">TRANSFER PEMBAYARAN</h3>
                                <div class="bg-blue-50 border-2 border-blue-100 rounded-[2rem] p-6 space-y-3">
                                    <div class="flex items-center gap-3">
                                        <div class="bg-white px-3 py-1.5 rounded-lg border border-blue-200">
                                            <span class="text-blue-600 font-black text-xs">BCA</span>
                                        </div>
                                        <span class="text-[10px] font-bold text-blue-600 uppercase tracking-widest">Mandala Education Group</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-2xl font-black text-slate-800 tracking-wider">8901 2233 44</span>
                                        <button type="button" class="text-[10px] font-bold bg-blue-600 text-white px-4 py-2 rounded-xl uppercase">SALIN</button>
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">UPLOAD BUKTI TRANSFER</span>
                                    <label class="block border-2 border-dashed border-slate-200 rounded-[2rem] p-8 text-center cursor-pointer hover:bg-slate-50 transition relative overflow-hidden h-40 flex items-center justify-center">
                                        <input type="file" name="bukti_pembayaran" @change="handleFileUpload" class="hidden" accept="image/*">
                                        <template x-if="!buktiTransfer">
                                            <div class="text-slate-400 space-y-2">
                                                <i class="fas fa-camera text-2xl"></i>
                                                <p class="text-[10px] font-bold uppercase">KLIK UNTUK UPLOAD</p>
                                            </div>
                                        </template>
                                        <template x-if="buktiTransfer">
                                            <div class="absolute inset-0 p-2">
                                                <img :src="buktiTransfer" class="w-full h-full object-cover rounded-[1.5rem]">
                                            </div>
                                        </template>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-12 flex flex-col md:flex-row gap-4">
                            <button type="button" @click="step = 2" class="flex-1 bg-slate-100 text-slate-500 py-6 rounded-2xl font-bold uppercase">KEMBALI</button>
                            <button type="submit" :disabled="!buktiTransfer"
                                    class="flex-[2] bg-orange-500 text-white py-6 rounded-2xl font-bold uppercase shadow-xl tracking-[0.2em] disabled:opacity-50">
                                KONFIRMASI SEKARANG
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL SUCCESS --}}
    @if(session('success'))
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-slate-900/95 backdrop-blur-md">
        <div class="bg-white w-full max-w-md rounded-[3rem] p-10 text-center shadow-2xl relative border-t-8 border-orange-500">
            <div class="w-20 h-20 bg-orange-50 text-orange-500 rounded-full flex items-center justify-center text-3xl mx-auto mb-6 animate-pulse">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <h2 class="text-2xl font-black text-slate-800 uppercase mb-4 tracking-tight">MENUNGGU KONFIRMASI</h2>
            <p class="text-slate-500 text-sm leading-relaxed mb-8 font-medium">
                Pendaftaran Anda sedang diproses oleh sistem. Admin Mandala akan melakukan verifikasi pembayaran dalam 1x24 jam.
            </p>
            <div class="space-y-3">
                <a href="https://wa.me/+6285540000900" target="_blank" class="flex items-center justify-center gap-3 w-full bg-emerald-500 text-white py-5 rounded-2xl font-bold uppercase tracking-widest shadow-lg shadow-emerald-100">
                    <i class="fab fa-whatsapp text-xl"></i> HUBUNGI ADMIN
                </a>
                <a href="{{ route('dashboard') }}" class="block w-full py-4 text-slate-400 font-bold uppercase text-[10px] tracking-widest hover:text-blue-600 transition">KE DASHBOARD</a>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
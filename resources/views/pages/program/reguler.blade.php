@extends('layouts.app')
@section('title', 'Pendaftaran Program Reguler - Mandala')

@section('content')
<div class="min-h-screen bg-slate-50 pb-20" x-data="{ 
    // 1. LOGIKA POSISI STEP
    step: {{ request('step') ?? 1 }}, 
    
    // 2. LOGIKA DATA
    metode: sessionStorage.getItem('reg_metode') || 'online', 
    jenjang: sessionStorage.getItem('reg_jenjang') || '', 
    tipePaket: sessionStorage.getItem('reg_tipePaket') || 'eceran',
    selectedMapel: JSON.parse(sessionStorage.getItem('reg_selectedMapel')) || [],
    mauMengaji: sessionStorage.getItem('reg_mauMengaji') === 'true',
    lokasi: sessionStorage.getItem('reg_lokasi') || '',
    buktiTransfer: null,
    
    saveToSession() {
        sessionStorage.setItem('reg_metode', this.metode);
        sessionStorage.setItem('reg_jenjang', this.jenjang);
        sessionStorage.setItem('reg_tipePaket', this.tipePaket);
        sessionStorage.setItem('reg_selectedMapel', JSON.stringify(this.selectedMapel));
        sessionStorage.setItem('reg_mauMengaji', this.mauMengaji);
        sessionStorage.setItem('reg_lokasi', this.lokasi);
    },

    pilihPaket(tipe) {
        this.tipePaket = tipe;
        if(tipe === 'borongan' && this.jenjang) {
            this.selectedMapel = [...this.listMapel[this.jenjang]];
        } else {
            this.selectedMapel = [];
        }
        this.saveToSession();
    },

    prices: {
        'TK': 125000, 'SD': 150000, 'SMP': 175000, 'SMA': 200000,
        'diskon_borongan': 0.25, 'add_mengaji': 50000 
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
    let pricePerMapel = this.prices[this.jenjang] || 0;
    
    // Jika pilih Borongan, harga dihitung dari total semua mapel di jenjang itu dikurangi diskon
    if (this.tipePaket === 'borongan' && this.jenjang && this.listMapel[this.jenjang]) {
        let totalNormal = this.listMapel[this.jenjang].length * pricePerMapel;
        total = totalNormal - (totalNormal * this.prices.diskon_borongan);
    } else {
        // Jika Eceran, hanya hitung yang diklik saja
        total = this.selectedMapel.length * pricePerMapel;
    }

    // Tambahan biaya lain
    if (this.mauMengaji) total += this.prices.add_mengaji;
    if (this.metode === 'offline') total += 100000;
    
    return total;
},

    get normalPriceBorongan() {
        let pricePerMapel = this.prices[this.jenjang] || 0;
        return (this.jenjang && this.listMapel[this.jenjang]) ? this.listMapel[this.jenjang].length * pricePerMapel : 0;
    },

    showConfirmation(event) {
    Swal.fire({
        title: 'Konfirmasi Pembayaran?',
        text: 'Pastikan bukti transfer sudah benar. Admin akan segera memverifikasi pendaftaranmu.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#2563eb',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'YA, KIRIM SEKARANG',
        cancelButtonText: 'CEK LAGI',
        customClass: { popup: 'rounded-[2rem]' }
    }).then((result) => {
        if (result.isConfirmed) {
            // Tampilkan loading yang akan hilang otomatis saat halaman berganti
            Swal.fire({
                title: 'Sedang Mengirim...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => { 
                    Swal.showLoading();
                }
            });
            // Submit form secara manual
            event.target.closest('form').submit();
        }
    });
    }
}" 
x-init="$watch('metode', () => saveToSession()); $watch('jenjang', () => saveToSession()); $watch('mauMengaji', () => saveToSession()); $watch('lokasi', () => saveToSession());"
class="relative">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                    <input type="hidden" name="program_id" value="1"> 
                    <input type="hidden" name="metode" :value="metode">
                    <input type="hidden" name="jenjang" :value="jenjang">
                    <input type="hidden" name="total_harga" :value="totalPrice">
                    <input type="hidden" name="paket" :value="tipePaket">
                    <input type="hidden" name="mau_mengaji" :value="mauMengaji ? 1 : 0">
                    <input type="hidden" name="lokasi" :value="lokasi">
                    
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
                                <div class="p-8 border-2 border-slate-100 rounded-[2rem] h-full transition-all hover:border-blue-200" :class="metode === 'online' ? 'border-blue-600 bg-blue-50' : ''">
                                    <div class="w-16 h-16 bg-blue-600 text-white rounded-2xl flex items-center justify-center mb-6">
                                        <i class="fas fa-video text-2xl"></i>
                                    </div>
                                    <h4 class="font-bold text-xl text-slate-800 uppercase tracking-tight">Reguler Online</h4>
                                    <p class="text-slate-500 text-sm mt-2 leading-relaxed">Sesi belajar interaktif via Zoom/Gmeet.</p>
                                </div>
                            </label>

                            <label @click="metode = 'offline'" class="cursor-pointer group">
                                <div class="p-8 border-2 border-slate-100 rounded-[2rem] h-full transition-all hover:border-blue-200" :class="metode === 'offline' ? 'border-blue-600 bg-blue-50' : ''">
                                    <div class="w-16 h-16 bg-slate-900 text-white rounded-2xl flex items-center justify-center mb-6">
                                        <i class="fas fa-car text-2xl"></i>
                                    </div>
                                    <h4 class="font-bold text-xl text-slate-800 uppercase tracking-tight">Reguler Offline</h4>
                                    <div x-show="metode === 'offline'" class="mt-6" @click.stop>
                                        <select x-model="lokasi" class="w-full border-2 border-slate-100 p-4 rounded-2xl text-sm font-bold outline-none focus:border-blue-600">
                                            <option value="">-- PILIH WILAYAH --</option>
                                            <template x-for="loc in listLokasi"><option :value="loc" x-text="loc"></option></template>
                                        </select>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="mt-14">
                            <button type="button" 
                                    @click="step = 2; saveToSession()" 
                                    :disabled="(metode === 'offline' && !lokasi) || !metode" 
                                    :class="(metode === 'offline' && !lokasi) || !metode ? 'bg-slate-300 cursor-not-allowed shadow-none' : 'bg-blue-600 shadow-xl'"
                                    class="w-full text-white py-6 rounded-[1.5rem] font-bold uppercase tracking-[0.2em] transition-all duration-300">
                                LANJUT KONFIGURASI
                            </button>
                            <p x-show="metode === 'offline' && !lokasi" class="text-center text-red-500 text-[10px] font-bold mt-4 uppercase tracking-widest animate-pulse">
                                * Harap pilih wilayah lokasi belajar terlebih dahulu
                            </p>
                        </div>
                    </div>

                    {{-- STEP 2: KONFIGURASI --}}
                    <div x-show="step === 2" x-transition x-cloak class="relative">
                        <div class="space-y-12">
                            {{-- 1. JENJANG --}}
                            <section>
                                <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2 uppercase tracking-tight">
                                    <span class="w-7 h-7 bg-blue-600 text-white rounded flex items-center justify-center text-xs font-black">1</span>
                                    JENJANG SEKOLAH
                                </h3>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <template x-for="j in ['TK', 'SD', 'SMP', 'SMA']">
                                        <button type="button" @click="jenjang = j; pilihPaket(tipePaket)" 
                                                :class="jenjang === j ? 'bg-blue-600 text-white border-blue-600 shadow-lg' : 'bg-white text-slate-500 border-slate-200'"
                                                class="py-5 border-2 rounded-2xl font-black uppercase text-sm transition-all" x-text="j"></button>
                                    </template>
                                </div>
                            </section>

                            {{-- 2. PAKET --}}
                            <section x-show="jenjang" x-transition>
                                <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2 uppercase tracking-tight">
                                    <span class="w-7 h-7 bg-blue-600 text-white rounded flex items-center justify-center text-xs font-black">2</span>
                                    BANDINGKAN PAKET BELAJAR
                                </h3>
                                <div class="grid md:grid-cols-2 gap-6">
                                    <button type="button" @click="pilihPaket('eceran')" 
                                            :class="tipePaket === 'eceran' ? 'border-blue-600 bg-blue-50' : 'border-slate-100 bg-slate-50'"
                                            class="p-8 border-2 rounded-3xl text-left transition-all group">
                                        <span class="font-bold text-slate-800 uppercase block tracking-tight">SATUAN / ECERAN</span>
                                        <div class="mt-2 text-2xl font-black">
                                            <span x-text="'Rp ' + (prices[jenjang] || 0).toLocaleString('id-ID')"></span>
                                            <span class="text-[10px] text-slate-400 font-bold">/ MAPEL</span>
                                        </div>
                                    </button>

                                    <button type="button" @click="pilihPaket('borongan')" 
                                            :class="tipePaket === 'borongan' ? 'border-orange-500 bg-orange-50' : 'border-slate-100 bg-slate-50'"
                                            class="p-8 border-2 rounded-3xl text-left transition-all relative overflow-hidden group">
                                        <div class="flex justify-between items-start">
                                            <span class="font-bold text-orange-600 uppercase block tracking-tight">PAKET BORONGAN</span>
                                            <span class="bg-orange-500 text-white text-[9px] px-2 py-1 rounded-md font-black italic">HEMAT 25%</span>
                                        </div>
                                        <div class="mt-2">
                                            <span class="text-xs text-slate-400 line-through font-bold" x-text="'Rp ' + normalPriceBorongan.toLocaleString('id-ID')"></span>
                                            <div class="text-2xl font-black text-slate-800">
                                                <span x-text="'Rp ' + (normalPriceBorongan - (normalPriceBorongan * prices.diskon_borongan)).toLocaleString('id-ID')"></span>
                                                <span class="text-[10px] text-slate-400 font-bold">/ BULAN</span>
                                            </div>
                                        </div>
                                        <div class="absolute top-0 right-0 bg-orange-500 text-white text-[10px] font-black px-6 py-1 rounded-bl-xl uppercase tracking-widest">BEST DEAL</div>
                                    </button>
                                </div>
                            </section>

                            {{-- 3. MAPEL --}}
                            <section x-show="jenjang" x-transition>
                                <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2 uppercase tracking-tight">
                                    <span class="w-7 h-7 bg-blue-600 text-white rounded flex items-center justify-center text-xs font-black">3</span>
                                    PILIH MATA PELAJARAN
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <template x-for="m in listMapel[jenjang]">
                                        <div @click="toggleMapel(m)" 
                                             :class="selectedMapel.includes(m) ? 'bg-slate-900 text-white border-slate-900 shadow-md' : 'bg-white border-slate-200 text-slate-600 hover:border-blue-300'"
                                             class="p-5 border-2 rounded-2xl cursor-pointer flex items-center justify-between transition-all duration-200">
                                            <span class="text-sm font-bold uppercase tracking-tight" x-text="m"></span>
                                            <i class="fas" :class="selectedMapel.includes(m) ? 'fa-check-circle text-blue-400' : 'fa-plus text-slate-200'"></i>
                                        </div>
                                    </template>
                                </div>

                                <div x-show="selectedMapel.length > 0" x-transition 
     class="mt-8 p-8 bg-slate-50 border-2 border-slate-100 rounded-[2rem]">
    
    <div class="flex justify-between text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-6">
        <span>Itemized Receipt</span>
        <span>Subtotal</span>
    </div>

    <div class="space-y-4">
        <div class="flex justify-between items-start">
            <div class="flex flex-col">
                <span class="text-sm font-bold text-slate-700" x-text="tipePaket === 'borongan' ? 'Paket Seluruh Mata Pelajaran' : 'Pilihan Mata Pelajaran Satuan'"></span>
                <span class="text-[10px] text-slate-400 font-medium" 
                      x-text="tipePaket === 'borongan' 
                        ? listMapel[jenjang].length + ' Mapel x Rp ' + (prices[jenjang] || 0).toLocaleString('id-ID')
                        : selectedMapel.length + ' Mapel x Rp ' + (prices[jenjang] || 0).toLocaleString('id-ID')">
                </span>
            </div>
            <span class="text-sm font-black text-slate-900" 
                  x-text="'Rp ' + (tipePaket === 'borongan' ? normalPriceBorongan : selectedMapel.length * prices[jenjang]).toLocaleString('id-ID')">
            </span>
        </div>

        <div x-show="tipePaket === 'borongan'" class="flex justify-between items-center p-4 bg-orange-50 rounded-2xl border border-orange-100" x-transition>
            <div class="flex flex-col">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-black text-orange-600 uppercase tracking-tight">Potongan Hemat</span>
                    <span class="bg-orange-500 text-white text-[8px] px-1.5 py-0.5 rounded font-black">25% OFF</span>
                </div>
                <span class="text-[10px] text-orange-400 font-medium italic">Khusus pengambilan paket borongan</span>
            </div>
            <span class="text-sm font-black text-orange-600" 
                  x-text="'- Rp ' + (normalPriceBorongan * prices.diskon_borongan).toLocaleString('id-ID')">
            </span>
        </div>

        <div x-show="mauMengaji" class="flex justify-between items-center py-2 border-t border-slate-200/60 border-dashed" x-transition>
            <div class="flex flex-col">
                <span class="text-sm font-bold text-emerald-600 uppercase text-[11px] tracking-tight">Ekstra Pendampingan</span>
                <span class="text-[10px] text-slate-400 font-medium">Program Mengaji Privat</span>
            </div>
            <span class="text-sm font-black text-slate-900">+ Rp 50.000</span>
        </div>

        <div x-show="metode === 'offline'" class="flex justify-between items-center py-2 border-t border-slate-200/60 border-dashed" x-transition>
            <div class="flex flex-col">
                <span class="text-sm font-bold text-blue-600 uppercase text-[11px] tracking-tight">Biaya Operasional</span>
                <span class="text-[10px] text-slate-400 font-medium">Kunjungan Mentor ke Lokasi</span>
            </div>
            <span class="text-sm font-black text-slate-900">+ Rp 100.000</span>
        </div>

        <div class="mt-6 pt-6 border-t-4 border-double border-slate-200 flex justify-between items-end">
            <div>
                <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Bayar</span>
                <span class="text-[9px] text-blue-500 font-bold italic">*Sudah termasuk PPN & Admin</span>
            </div>
            <div class="text-right">
                <span class="text-2xl font-black text-slate-900 tracking-tighter" x-text="'Rp ' + totalPrice.toLocaleString('id-ID')"></span>
                                        </div>
                                    </div>
                                </div>
                            </section>

                            {{-- 4. MENGAJI --}}
                            <section x-show="jenjang" x-transition class="mb-12">
                                <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2 uppercase tracking-tight">
                                    <span class="w-7 h-7 bg-blue-600 text-white rounded flex items-center justify-center text-xs font-black">4</span>
                                    EKSTRA PENDAMPINGAN
                                </h3>
                                <div @click="mauMengaji = !mauMengaji; saveToSession()" 
                                     :class="mauMengaji ? 'border-emerald-500 bg-emerald-50 shadow-md' : 'border-slate-100 bg-slate-50'"
                                     class="p-6 border-2 rounded-3xl cursor-pointer flex items-center justify-between transition-all group">
                                    <div class="flex items-center gap-5">
                                        <div :class="mauMengaji ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-400'" class="w-14 h-14 rounded-2xl flex items-center justify-center text-xl transition-colors">
                                            <i class="fas fa-book-quran"></i>
                                        </div>
                                        <div>
                                            <span class="block font-bold text-slate-800 uppercase text-sm">TAMBAHAN MENGAJI</span>
                                            <span class="text-[11px] font-bold text-emerald-600 uppercase tracking-widest">+ Rp 50.000 / BULAN</span>
                                        </div>
                                    </div>
                                    <div :class="mauMengaji ? 'bg-emerald-500' : 'bg-slate-300'" class="w-7 h-7 rounded-full border-4 border-white shadow-md transition-all"></div>
                                </div>
                            </section>
                        </div>

                        {{-- SUMMARY BAR --}}
                        <div class="mt-10 p-8 bg-slate-900 text-white rounded-[2rem] shadow-xl border border-white/5">
                            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                                <div class="text-center md:text-left">
                                    <span class="text-[10px] font-bold text-blue-400 uppercase tracking-[0.2em] block mb-1">TOTAL PEMBAYARAN:</span>
                                    <h3 class="text-3xl font-black tracking-tighter" x-text="'Rp ' + totalPrice.toLocaleString('id-ID')"></h3>
                                </div>
                                
                                <div class="flex gap-3 w-full md:w-auto">
                                    <button type="button" @click="step = 1; window.scrollTo(0,0)" 
                                            class="flex-1 md:px-8 py-4 bg-white/10 hover:bg-white/20 rounded-2xl font-bold uppercase text-[10px] tracking-widest transition-all">
                                        KEMBALI
                                    </button>
                                    @auth
                                        <button type="button" @click="step = 3; saveToSession(); window.scrollTo(0,0)" 
                                                :disabled="selectedMapel.length === 0" 
                                                class="flex-[2] md:px-12 py-4 bg-blue-600 hover:bg-blue-500 text-white rounded-2xl font-bold uppercase text-[10px] tracking-widest shadow-lg disabled:opacity-50 transition-all">
                                            LANJUT BAYAR
                                        </button>
                                    @else
                                        <a :href="'{{ route('pendaftaran.lanjut') }}?type=reguler&step=3'" @click="saveToSession()" 
                                           class="flex-[2] md:px-12 py-4 bg-orange-500 hover:bg-orange-400 text-white rounded-2xl font-bold uppercase text-[10px] tracking-widest text-center shadow-lg transition-all">
                                            LOGIN UNTUK BAYAR
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
{{-- STEP 3: PEMBAYARAN (VERSI NOMINAL UNIK) --}}
<div x-show="step === 3" x-transition x-cloak>
    <form action="{{ route('enroll.program') }}" method="POST" enctype="multipart/form-data">
        @csrf
        {{-- Hidden Inputs --}}
        <input type="hidden" name="program_id" value="1"> {{-- Sesuaikan ID Program --}}
        <input type="hidden" name="total_harga" :value="totalPrice">
        <input type="hidden" name="jenjang" :value="jenjang">

        <div class="grid md:grid-cols-2 gap-10">
            {{-- KIRI: RINGKASAN & TOTAL --}}
            <div class="space-y-6">
                <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">RINGKASAN PESANAN</h3>
                <div class="bg-slate-900 rounded-[2rem] p-8 text-white shadow-xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-600/20 rounded-full -mr-16 -mt-16 blur-2xl"></div>
                    
                    <div class="space-y-4 relative z-10">
                        <div class="flex justify-between border-b border-white/10 pb-3">
                            <span class="text-[10px] text-white/50 font-bold uppercase tracking-widest">PROGRAM</span>
                            <span class="font-bold text-sm uppercase" x-text="jenjang"></span>
                        </div>
                        
                        {{-- TOTAL DENGAN KODE UNIK --}}
                        <div class="pt-4 bg-white/5 p-6 rounded-2xl border border-white/10">
                            <p class="text-[10px] text-blue-400 font-black uppercase tracking-widest mb-1">TOTAL TRANSFER (Hingga Digit Terakhir)</p>
                            <h3 class="text-4xl font-black tracking-tighter text-orange-500" 
                                x-text="'Rp ' + (totalPrice + parseInt('{{ substr(preg_replace('/[^0-9]/', '', Auth::user()->whatsapp ?? '000'), -3) }}')).toLocaleString('id-ID')">
                            </h3>
                            <div class="mt-3 flex items-start gap-2">
                                <i class="fas fa-info-circle text-orange-500 mt-0.5 text-xs"></i>
                                <p class="text-[9px] text-white/60 leading-tight italic">
                                    Sistem menggunakan 3 digit terakhir nomor WhatsApp Anda sebagai kode unik untuk verifikasi otomatis.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KANAN: INSTRUKSI BANK & UPLOAD --}}
            <div class="space-y-6">
                <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">TRANSFER PEMBAYARAN</h3>
                
                <div class="bg-blue-50 border-2 border-blue-100 rounded-[2rem] p-6 text-center">
                    <span class="text-blue-600 font-black text-[10px] px-3 py-1 bg-white rounded-lg border border-blue-200 uppercase">BCA - Mandala Group</span>
                    <div class="text-3xl font-black text-slate-800 tracking-wider py-2">8901 2233 44</div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase">A.N MANDALA ACADEMY</p>
                </div>

                <div class="space-y-3">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">UPLOAD BUKTI TRANSFER</span>
                    <label class="block border-2 border-dashed border-slate-200 rounded-[2rem] p-8 text-center cursor-pointer hover:bg-slate-50 transition relative h-40 flex items-center justify-center overflow-hidden">
                        <input type="file" name="bukti_pembayaran" @change="handleFileUpload" class="hidden" accept="image/*" required>
                        <template x-if="!buktiTransfer">
                            <div class="text-slate-400">
                                <i class="fas fa-camera text-2xl mb-2"></i>
                                <p class="text-[10px] font-bold uppercase">KLIK UNTUK UPLOAD</p>
                            </div>
                        </template>
                        <template x-if="buktiTransfer">
                            <img :src="buktiTransfer" class="absolute inset-0 w-full h-full object-cover rounded-[1.5rem]">
                        </template>
                    </label>
                </div>
            </div>
        </div>

        <div class="mt-12 flex flex-col md:flex-row gap-4">
            <button type="button" @click="step = 2" class="flex-1 bg-slate-100 text-slate-500 py-6 rounded-2xl font-bold uppercase hover:bg-slate-200 transition">KEMBALI</button>
            <button type="submit" 
                    :disabled="!buktiTransfer" 
                    class="w-full bg-orange-500 text-white py-6 rounded-2xl font-bold uppercase shadow-xl tracking-[0.2em] disabled:opacity-50 hover:bg-orange-600 transition-all">
                KONFIRMASI SEKARANG
            </button>
        </div>
    </form>
</div>

@if(session('success'))
<script>
    Swal.fire({
        title: 'PENDAFTARAN TERKIRIM!',
        html: `
            <div class="text-center p-2">
                <div class="mb-4 text-slate-600 leading-relaxed text-sm">
                    Terima kasih! Bukti transfer kamu sudah kami terima. 
                    <br><span class="font-bold text-blue-600 italic">Status: Menunggu Verifikasi Admin.</span>
                </div>
                <div class="bg-blue-50 border-2 border-dashed border-blue-100 rounded-[2rem] p-6 mb-6">
                    <p class="text-[10px] font-black text-blue-400 uppercase tracking-[0.2em] mb-3">Konfirmasi via WhatsApp</p>
                    <a href="https://wa.me/628123456789?text=Halo%20Admin%20Mandala..." 
                       target="_blank" 
                       class="inline-flex items-center gap-3 bg-[#25D366] text-white px-8 py-4 rounded-2xl font-bold hover:scale-105 transition-all shadow-lg shadow-emerald-200">
                        <i class="fab fa-whatsapp text-xl"></i> HUBUNGI ADMIN WA
                    </a>
                </div>
            </div>`,
        icon: 'success',
        confirmButtonText: 'KE DASHBOARD SAYA',
        confirmButtonColor: '#2563eb',
        allowOutsideClick: false, // Agar user tidak sengaja menutup tanpa klik tombol
        customClass: { popup: 'rounded-[2.5rem] border-none shadow-2xl', confirmButton: 'rounded-xl px-10 py-4 font-bold uppercase tracking-widest text-[10px]' }
    }).then((result) => {
        if (result.isConfirmed) {
            // ARAHKAN KE ROUTE DASHBOARD DISINI
            window.location.href = "{{ route('dashboard') }}"; 
        }
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        title: 'WADUH!',
        text: "{{ session('error') }}",
        icon: 'error',
        confirmButtonColor: '#ef4444',
        customClass: { popup: 'rounded-[2rem]' }
    });
</script>
@endif

<style>
    [x-cloak] { display: none !important; }
    .swal2-popup { animation: swal-show 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important; }
</style>
@endsection

@extends('layouts.app')
@section('title', 'Pendaftaran Program Reguler - Mandala')

@section('content')
@php
    $initialStep = (int) ($step ?? 1);
    $restoreFromSession = (bool) ($restoreFromSession ?? false);
@endphp

<script>
    (() => {
        const restoreFromSession = @json($restoreFromSession);
        const requestedStep = {{ $initialStep }};

        const clearRegulerState = () => {
            Object.keys(sessionStorage).forEach((key) => {
                if (key.startsWith('reg_')) {
                    sessionStorage.removeItem(key);
                }
            });
        };

        const getSelectedMapelCount = () => {
            const raw = sessionStorage.getItem('reg_selectedMapel');
            if (!raw) return 0;

            try {
                const parsed = JSON.parse(raw);
                return Array.isArray(parsed) ? parsed.length : 0;
            } catch (error) {
                return 0;
            }
        };

        const hasDraft = Boolean(sessionStorage.getItem('reg_jenjang'))
            || Boolean(sessionStorage.getItem('reg_kelas'))
            || getSelectedMapelCount() > 0;

        window.__regStepOverride = null;

        if (!restoreFromSession) {
            clearRegulerState();
            return;
        }

        // Lindungi URL step=3 lama agar tidak membuka pembayaran tanpa draft.
        if (requestedStep === 3 && !hasDraft) {
            clearRegulerState();
            window.__regStepOverride = 1;
        }
    })();
</script>

<div class="min-h-screen bg-slate-50 pb-20" x-data="{ 
    // 1. LOGIKA POSISI STEP
    step: window.__regStepOverride !== null ? window.__regStepOverride : {{ $initialStep }}, 
    
    // 2. LOGIKA DATA
    metode: sessionStorage.getItem('reg_metode') || 'online', 
    jenjang: sessionStorage.getItem('reg_jenjang') || '', 
    kelas: sessionStorage.getItem('reg_kelas') || '', 
    showKelasOptions: false,
    tipePaket: sessionStorage.getItem('reg_tipePaket') || 'eceran',
    selectedMapel: JSON.parse(sessionStorage.getItem('reg_selectedMapel')) || [],
    mauMengaji: sessionStorage.getItem('reg_mauMengaji') === 'true',
    lokasi: sessionStorage.getItem('reg_lokasi') || '',
    alamatSiswa: sessionStorage.getItem('reg_alamat_siswa') || '',
    
    extraHours: parseInt(sessionStorage.getItem('reg_extraHours')) || 0,
    perMinggu: parseInt(sessionStorage.getItem('reg_perMinggu')) || 1,
    jadwalDetail: sessionStorage.getItem('reg_jadwalDetail') || '',
    scheduleSlots: JSON.parse(sessionStorage.getItem('reg_scheduleSlots') || '[]'),
    dayOptions: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
    selectedDay: sessionStorage.getItem('reg_selectedDay') || 'Senin',
    selectedTime: sessionStorage.getItem('reg_selectedTime') || '16:00',
    scheduleNote: sessionStorage.getItem('reg_scheduleNote') || '',
    
    goToStep(target) {
        this.step = target;
        this.saveToSession();
        window.scrollTo(0,0);
    },

    saveToSession() {
        sessionStorage.setItem('reg_step', this.step);
        sessionStorage.setItem('reg_metode', this.metode);
        sessionStorage.setItem('reg_jenjang', this.jenjang);
        sessionStorage.setItem('reg_kelas', this.kelas);
        sessionStorage.setItem('reg_tipePaket', this.tipePaket);
        sessionStorage.setItem('reg_selectedMapel', JSON.stringify(this.selectedMapel));
        sessionStorage.setItem('reg_mauMengaji', this.mauMengaji);
        sessionStorage.setItem('reg_lokasi', this.lokasi);
        sessionStorage.setItem('reg_alamat_siswa', this.alamatSiswa);
        sessionStorage.setItem('reg_extraHours', this.extraHours);
        sessionStorage.setItem('reg_perMinggu', this.perMinggu);
        sessionStorage.setItem('reg_jadwalDetail', this.jadwalDetail);
        sessionStorage.setItem('reg_scheduleSlots', JSON.stringify(this.scheduleSlots));
        sessionStorage.setItem('reg_selectedDay', this.selectedDay);
        sessionStorage.setItem('reg_selectedTime', this.selectedTime);
        sessionStorage.setItem('reg_scheduleNote', this.scheduleNote);
    },

    initScheduleBuilder() {
        if (!Array.isArray(this.scheduleSlots)) {
            this.scheduleSlots = [];
        }

        if (this.scheduleSlots.length === 0 && this.jadwalDetail) {
            const found = [...this.jadwalDetail.matchAll(/([A-Za-z]+)\s*\((\d{1,2}[:.]\d{2})\)/g)];
            this.scheduleSlots = found
                .map(match => {
                    const day = this.dayOptions.find(option => option.toLowerCase() === match[1].toLowerCase());
                    const time = (match[2] || '').replace('.', ':');
                    return day && /^\d{2}:\d{2}$/.test(time) ? { day, time } : null;
                })
                .filter(Boolean);
        }

        this.sortScheduleSlots();
        this.syncScheduleDetail();
    },

    sortScheduleSlots() {
        this.scheduleSlots.sort((a, b) => {
            const dayDiff = this.dayOptions.indexOf(a.day) - this.dayOptions.indexOf(b.day);
            if (dayDiff !== 0) return dayDiff;
            return a.time.localeCompare(b.time);
        });
    },

    syncScheduleDetail() {
        this.jadwalDetail = this.scheduleSlots.map(slot => `${slot.day} (${slot.time})`).join(', ');
        this.saveToSession();
    },

    addScheduleSlot() {
        if (!this.selectedDay || !this.selectedTime) return;

        if (this.scheduleSlots.length >= this.perMinggu) {
            return;
        }

        const normalizedTime = this.selectedTime.slice(0, 5);
        const exists = this.scheduleSlots.some(slot => slot.day === this.selectedDay && slot.time === normalizedTime);
        if (exists) {
            return;
        }

        this.scheduleSlots.push({ day: this.selectedDay, time: normalizedTime });
        this.sortScheduleSlots();
        this.syncScheduleDetail();
    },

    removeScheduleSlot(index) {
        this.scheduleSlots.splice(index, 1);
        this.syncScheduleDetail();
    },

    autoGenerateSchedule() {
        const count = Math.max(1, Math.min(this.perMinggu, this.dayOptions.length));
        const time = (this.selectedTime || '16:00').slice(0, 5);
        this.scheduleSlots = this.dayOptions.slice(0, count).map(day => ({ day, time }));
        this.syncScheduleDetail();
    },

    pilihPaket(tipe) {
        this.tipePaket = tipe;
        if(tipe === 'borongan' && this.jenjang) {
            this.selectedMapel = this.listMapel[this.jenjang].map(m => m.id);
        } else {
            this.selectedMapel = [];
        }
        this.saveToSession();
    },

    prices: {
        'per_id': {
            @foreach($programs as $p)
                '{{ $p->id }}': {{ $p->price }},
            @endforeach
        },
        'quran_prices': {
            @foreach($programs->unique('jenjang') as $p)
                '{{ $p->jenjang }}': {{ $p->quran_price ?? 0 }},
            @endforeach
        },
        'extra_prices': {
            @foreach($programs->unique('jenjang') as $p)
                '{{ $p->jenjang }}': {{ $p->extra_meeting_price ?? 0 }},
            @endforeach
        },
        'diskon_borongan': 0.25
    },

    listMapel: {
        @foreach(['TK', 'SD', 'SMP', 'SMA'] as $j)
            '{{ $j }}': [
                @foreach($programs->where('jenjang', $j) as $p)
                    { id: '{{ $p->id }}', name: '{{ $p->name }}' },
                @endforeach
            ],
        @endforeach
    },

    listLokasi: [
        'Semarang Barat (Ngaliyan)', 'Semarang Timur (Pedurungan)', 
        'Semarang Selatan (Banyumanik)', 'Semarang Tengah (Gajahmada)', 'Luar Kota (Area Khusus)'
    ],

    toggleMapel(id) {
        if (this.selectedMapel.includes(id)) {
            this.selectedMapel = this.selectedMapel.filter(i => i !== id);
        } else {
            this.selectedMapel.push(id);
        }
        this.saveToSession();
    },

    get totalPrice() {
        if (!this.jenjang || this.selectedMapel.length === 0) return 0;
        let subtotalMapel = 0;
        this.selectedMapel.forEach(id => {
            subtotalMapel += (this.prices.per_id[id] || 0);
        });
        if (this.tipePaket === 'borongan') {
            subtotalMapel = subtotalMapel * (1 - this.prices.diskon_borongan);
        }
        let total = (subtotalMapel * this.perMinggu) * 4;
        if (this.mauMengaji) {
            total += (this.prices.quran_prices[this.jenjang] || 0);
        }
        if (this.extraHours > 0) {
            let extraPriceRate = this.prices.extra_prices[this.jenjang] || 0;
            total += (this.extraHours * extraPriceRate * this.perMinggu * 4);
        }
        return Math.round(total);
    }
}"
x-init="initScheduleBuilder(); $watch('step', () => saveToSession()); $watch('metode', () => saveToSession()); $watch('jenjang', () => saveToSession()); $watch('mauMengaji', () => saveToSession()); $watch('lokasi', () => saveToSession()); $watch('selectedMapel', () => saveToSession()); $watch('extraHours', () => saveToSession()); $watch('perMinggu', () => { if (scheduleSlots.length > perMinggu) { scheduleSlots = scheduleSlots.slice(0, perMinggu); syncScheduleDetail(); } saveToSession(); });"
class="relative">

    {{-- Header Section --}}
    <div class="bg-slate-900 pt-20 pb-32 px-6 relative overflow-hidden text-center">
        <div class="absolute top-0 right-0 w-96 h-96 bg-blue-600/10 rounded-full -mr-20 -mt-20 blur-3xl"></div>
        <div class="relative z-10 container mx-auto transform scale-[0.85] origin-top transition-all duration-500">
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
                {{-- SATU FORM UNTUK SEMUA --}}
                <form id="formPendaftaran" action="{{ route('enroll.program') }}" method="POST" enctype="multipart/form-data">
    @csrf
    {{-- Input hidden otomatis --}}
    <input type="hidden" name="program_id" :value="selectedMapel[0]"> 
    <input type="hidden" name="total_harga" :value="totalPrice">
    <input type="hidden" name="jenjang" :value="jenjang">
    <input type="hidden" name="kelas" :value="kelas">
    <input type="hidden" name="tipe_paket" :value="tipePaket">
    <input type="hidden" name="per_minggu" :value="perMinggu">
    <input type="hidden" name="extra_hours" :value="extraHours">
    <input type="hidden" name="is_mengaji" :value="mauMengaji ? 1 : 0">
    <input type="hidden" name="jadwal_detail" :value="jadwalDetail">
    <input type="hidden" name="selected_subjects" :value="JSON.stringify(selectedMapel)">
    <input type="hidden" name="lokasi_cabang" :value="lokasi">
    <input type="hidden" name="alamat_siswa" :value="alamatSiswa">
        <input type="hidden" name="payment_method" value="midtrans">
                    
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
                            <label @click="metode = 'online'; lokasi = 'ONLINE (ZOOM/GMEET)'; alamatSiswa = ''" class="cursor-pointer group">
                                <div class="p-8 border-2 border-slate-100 rounded-[2rem] h-full transition-all hover:border-blue-200" :class="metode === 'online' ? 'border-blue-600 bg-blue-50' : ''">
                                    <div class="w-16 h-16 bg-blue-600 text-white rounded-2xl flex items-center justify-center mb-6">
                                        <i class="fas fa-video text-2xl"></i>
                                    </div>
                                    <h4 class="font-bold text-xl text-slate-800 uppercase tracking-tight">Reguler Online</h4>
                                    <p class="text-slate-500 text-sm mt-2 leading-relaxed">Sesi belajar interaktif via Zoom/Gmeet.</p>
                                </div>
                            </label>

                            <label @click="metode = 'offline'; lokasi = ''" class="cursor-pointer group">
                                <div class="p-8 border-2 border-slate-100 rounded-[2rem] h-full transition-all hover:border-blue-200" :class="metode === 'offline' ? 'border-blue-600 bg-blue-50' : ''">
                                    <div class="w-16 h-16 bg-slate-900 text-white rounded-2xl flex items-center justify-center mb-6">
                                        <i class="fas fa-car text-2xl"></i>
                                    </div>
                                    <h4 class="font-bold text-xl text-slate-800 uppercase tracking-tight">Reguler Offline</h4>
                                    
                                    <div x-show="metode === 'offline'" class="mt-6" @click.stop>
                                        <select x-model="lokasi" name="lokasi_cabang_select" class="w-full border-2 border-slate-100 p-4 rounded-2xl text-sm font-bold outline-none focus:border-blue-600 mb-4">
                                            <option value="">-- PILIH WILAYAH --</option>
                                            <template x-for="loc in listLokasi">
                                                <option :value="loc" x-text="loc"></option>
                                            </template>
                                        </select>

                                        <template x-if="lokasi">
                                            <div x-transition>
                                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Alamat Lengkap Rumah / Titik Temu</label>
                                                <textarea 
                                                    required
                                                    x-model="alamatSiswa"
                                                    @input="saveToSession()"
                                                    class="w-full border-2 border-slate-100 p-4 rounded-2xl text-sm font-medium outline-none focus:border-blue-600 min-h-[100px]"
                                                    placeholder="Contoh: Jl. Merpati No.12..."></textarea>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="mt-14">
                            <button type="button" 
                                    @click="goToStep(2)" 
                                    :disabled="(metode === 'offline' && (!lokasi || !alamatSiswa)) || !metode" 
                                    :class="(metode === 'offline' && (!lokasi || !alamatSiswa)) || !metode ? 'bg-slate-300 cursor-not-allowed shadow-none' : 'bg-blue-600 shadow-xl'"
                                    class="w-full text-white py-6 rounded-[1.5rem] font-bold uppercase tracking-[0.2em] transition-all duration-300">
                                LANJUT KONFIGURASI
                            </button>
                        </div>
                    </div>

         {{-- STEP 2: KONFIGURASI --}}
<div x-show="step === 2" x-transition x-cloak class="relative">
    <div class="space-y-12">
        {{-- 1. JENJANG SEKOLAH --}}
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

        {{-- 2. PILIH TINGKAT KELAS (DROPDOWN) --}}
        <section x-show="jenjang" x-transition class="mt-8">
            <button type="button" @click="showKelasOptions = !showKelasOptions" class="w-full flex items-center justify-between p-5 bg-gradient-to-r from-blue-50 to-slate-50 border-2 border-slate-200 rounded-2xl mb-4 hover:border-blue-300 transition-all">
                <div class="flex items-center gap-3">
                    <i class="fas fa-graduation-cap text-blue-600 text-sm"></i>
                    <span class="text-sm font-bold text-slate-700 uppercase tracking-tight">PILIH TINGKAT KELAS</span>
                </div>
                <div class="flex items-center gap-2">
                    <template x-if="kelas">
                        <span class="text-xs font-bold text-blue-600 bg-blue-100 px-3 py-1 rounded-lg" x-text="kelas"></span>
                    </template>
                    <i class="fas fa-chevron-down text-slate-400 transition-transform" :class="showKelasOptions ? 'rotate-180' : ''"></i>
                </div>
            </button>
            <div x-show="showKelasOptions" x-transition class="grid grid-cols-3 md:grid-cols-6 gap-3 p-5 bg-white border-2 border-slate-200 rounded-2xl">
                <template x-if="jenjang === 'SD'">
                    <template x-for="n in [1,2,3,4,5,6]">
                        <button type="button" @click="kelas = n + ' SD'; saveToSession(); showKelasOptions = false" 
                                :class="kelas === n + ' SD' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-500 border-slate-200'"
                                class="py-3 border-2 rounded-xl font-bold text-xs transition-all" x-text="n"></button>
                    </template>
                </template>

                <template x-if="jenjang === 'SMP'">
                    <template x-for="n in [7,8,9]">
                        <button type="button" @click="kelas = n + ' SMP'; saveToSession(); showKelasOptions = false" 
                                :class="kelas === n + ' SMP' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-500 border-slate-200'"
                                class="py-3 border-2 rounded-xl font-bold text-xs transition-all" x-text="n"></button>
                    </template>
                </template>

                <template x-if="jenjang === 'SMA'">
                    <template x-for="n in [10,11,12]">
                        <button type="button" @click="kelas = n + ' SMA'; saveToSession(); showKelasOptions = false" 
                                :class="kelas === n + ' SMA' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-500 border-slate-200'"
                                class="py-3 border-2 rounded-xl font-bold text-xs transition-all" x-text="n"></button>
                    </template>
                </template>

                <template x-if="jenjang === 'TK'">
                    <template x-for="t in ['TK A', 'TK B']">
                        <button type="button" @click="kelas = t; saveToSession(); showKelasOptions = false" 
                                :class="kelas === t ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-500 border-slate-200'"
                                class="py-3 border-2 rounded-xl font-bold text-xs transition-all" x-text="t"></button>
                    </template>
                </template>
            </div>
        </section>

        {{-- 2. BANDINGKAN PAKET BELAJAR --}}
        <section x-show="jenjang" x-transition>
            <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2 uppercase tracking-tight">
                <span class="w-7 h-7 bg-blue-600 text-white rounded flex items-center justify-center text-xs font-black">2</span>
                BANDINGKAN PAKET BELAJAR
            </h3>
            <div class="grid md:grid-cols-2 gap-6">
                {{-- ECERAN --}}
                <button type="button" @click="pilihPaket('eceran')" 
                        :class="tipePaket === 'eceran' ? 'border-blue-600 bg-blue-50' : 'border-slate-100 bg-slate-50'"
                        class="p-8 border-2 rounded-3xl text-left transition-all group">
                    <span class="font-bold text-slate-800 uppercase block tracking-tight">SATUAN / ECERAN</span>
                    <div class="mt-2 text-2xl font-black">
                        <span x-text="selectedMapel.length > 0 ? 'Rp ' + (selectedMapel.reduce((sum, id) => sum + (prices.per_id[id] || 0), 0)).toLocaleString('id-ID') : 'Rp -'"></span>
                        <span class="text-[10px] text-slate-400 font-bold">/ PERTEMUAN</span>
                    </div>
                </button>

                {{-- BORONGAN --}}
                <button type="button" @click="pilihPaket('borongan')" 
                        :class="tipePaket === 'borongan' ? 'border-orange-500 bg-orange-50' : 'border-slate-100 bg-slate-50'"
                        class="p-8 border-2 rounded-3xl text-left transition-all relative overflow-hidden group">
                    <div class="flex justify-between items-start">
                        <span class="font-bold text-orange-600 uppercase block tracking-tight">PAKET BORONGAN</span>
                        <span class="bg-orange-500 text-white text-[9px] px-2 py-1 rounded-md font-black italic">HEMAT 25%</span>
                    </div>
                    <div class="mt-2">
                        <span class="text-xs text-slate-400 line-through font-bold" 
                              x-text="selectedMapel.length > 0 ? 'Rp ' + (selectedMapel.reduce((sum, id) => sum + (prices.per_id[id] || 0), 0)).toLocaleString('id-ID') : ''"></span>
                        <div class="text-2xl font-black text-slate-800">
                            <span x-text="selectedMapel.length > 0 ? 'Rp ' + Math.round(selectedMapel.reduce((sum, id) => sum + (prices.per_id[id] || 0), 0) * 0.75).toLocaleString('id-ID') : 'Rp -'"></span>
                            <span class="text-[10px] text-slate-400 font-bold">/ PERTEMUAN</span>
                        </div>
                    </div>
                    <div class="absolute top-0 right-0 bg-orange-500 text-white text-[10px] font-black px-6 py-1 rounded-bl-xl uppercase tracking-widest">BEST DEAL</div>
                </button>
            </div>
        </section>

        {{-- 3. PILIH MATA PELAJARAN --}}
        <section x-show="jenjang" x-transition>
            <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2 uppercase tracking-tight">
                <span class="w-7 h-7 bg-blue-600 text-white rounded flex items-center justify-center text-xs font-black">3</span>
                PILIH MATA PELAJARAN
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <template x-for="mapel in (listMapel[jenjang] || [])" :key="mapel.id">
                    <div @click="toggleMapel(mapel.id)" 
                         :class="selectedMapel.includes(mapel.id) ? 'bg-slate-900 text-white border-slate-900 shadow-md' : 'bg-white border-slate-200 text-slate-600 hover:border-blue-300'"
                         class="p-5 border-2 rounded-2xl cursor-pointer flex flex-col transition-all duration-200">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-bold uppercase tracking-tight" x-text="mapel.name"></span>
                            <i class="fas" :class="selectedMapel.includes(mapel.id) ? 'fa-check-circle text-blue-400' : 'fa-plus text-slate-200'"></i>
                        </div>
                        <span class="text-[10px] mt-1 font-bold opacity-60" 
                              x-text="'Rp ' + (prices.per_id[mapel.id] || 0).toLocaleString('id-ID')"></span>
                    </div>
                </template>
            </div>
        </section>

        {{-- 4. FREKUENSI PERTEMUAN --}}
        <section x-show="jenjang" x-transition>
            <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2 uppercase tracking-tight">
                <span class="w-7 h-7 bg-blue-600 text-white rounded flex items-center justify-center text-xs font-black">4</span>
                FREKUENSI PERTEMUAN
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <template x-for="f in [1, 2, 3, 4, 5]">
                    <button type="button" @click="perMinggu = f; saveToSession()" 
                            :class="perMinggu === f ? 'bg-slate-900 text-white border-slate-900 shadow-lg' : 'bg-white text-slate-500 border-slate-200'"
                            class="py-4 border-2 rounded-2xl font-bold transition-all text-sm">
                        <span x-text="f + 'x Seminggu'"></span>
                    </button>
                </template>
            </div>
            <p class="mt-3 text-[11px] text-slate-400 font-medium italic">*Tagihan akan dihitung otomatis untuk 4 minggu (1 bulan)</p>
        </section>

        {{-- 5. PENGATURAN JADWAL RUTIN --}}
        <section x-show="jenjang" x-transition class="space-y-6">
            <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2 uppercase tracking-tight">
                <span class="w-7 h-7 bg-blue-600 text-white rounded flex items-center justify-center text-xs font-black">5</span>
                PENGATURAN JADWAL RUTIN
            </h3>
            <div class="bg-white border-2 border-slate-100 rounded-[2.5rem] p-8 shadow-sm space-y-4">
                <div class="flex items-center gap-3 ml-2">
                    <i class="fas fa-calendar-check text-blue-600 text-xs"></i>
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Pilih Hari & Jam (Format Otomatis)</label>
                </div>

                <div class="grid md:grid-cols-[1fr_1fr_auto_auto] gap-3 items-end">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Hari</label>
                        <select x-model="selectedDay" @change="saveToSession()" class="w-full bg-slate-50 border-2 border-slate-100 focus:border-blue-500 rounded-2xl p-4 text-sm font-bold text-slate-700 outline-none">
                            <template x-for="day in dayOptions" :key="day">
                                <option :value="day" x-text="day"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Jam Belajar</label>
                        <input type="time" x-model="selectedTime" @change="saveToSession()" class="w-full bg-slate-50 border-2 border-slate-100 focus:border-blue-500 rounded-2xl p-4 text-sm font-bold text-slate-700 outline-none">
                    </div>
                    <button type="button" @click="addScheduleSlot()"
                            :class="scheduleSlots.length >= perMinggu ? 'bg-slate-300 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-500'"
                            class="h-[52px] px-5 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all">
                        Tambah
                    </button>
                    <button type="button" @click="autoGenerateSchedule()" class="h-[52px] px-5 bg-slate-900 hover:bg-slate-700 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all">
                        Auto Isi
                    </button>
                </div>

                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest ml-1">
                    Jadwal dipilih <span x-text="scheduleSlots.length"></span> dari <span x-text="perMinggu"></span> pertemuan per minggu.
                </p>

                <div class="flex flex-wrap gap-2 min-h-[38px]">
                    <template x-for="(slot, index) in scheduleSlots" :key="slot.day + '-' + slot.time">
                        <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-blue-50 border border-blue-100 text-blue-700 text-[11px] font-bold">
                            <span x-text="slot.day + ' (' + slot.time + ')' "></span>
                            <button type="button" @click="removeScheduleSlot(index)" class="text-blue-400 hover:text-rose-500">
                                <i class="fas fa-times text-[10px]"></i>
                            </button>
                        </span>
                    </template>
                </div>

                <div class="relative">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Format Terkirim ke Admin</label>
                    <textarea x-model="jadwalDetail" readonly
                              placeholder="Jadwal akan dibuat otomatis..."
                              class="w-full bg-slate-50 border-2 border-slate-100 rounded-[1.5rem] p-5 text-sm text-slate-600 font-medium transition-all min-h-[90px] outline-none placeholder:text-slate-300"></textarea>
                </div>
            </div>
        </section>

        {{-- 6. TAMBAHAN JAM BELAJAR --}}
        <section x-show="jenjang" x-transition>
            <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2 uppercase tracking-tight">
                <span class="w-7 h-7 bg-blue-600 text-white rounded flex items-center justify-center text-xs font-black">6</span>
                TAMBAHAN JAM BELAJAR
            </h3>
            <div class="bg-white border-2 border-slate-100 rounded-[2.5rem] p-8 flex flex-col md:flex-row items-center justify-between gap-6 shadow-sm">
                <div class="flex items-center gap-5">
                    <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-xl">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <span class="block font-bold text-slate-800 uppercase text-sm tracking-tight">Tambah Durasi Sesi</span>
                        <span class="text-[11px] font-bold text-blue-600 uppercase tracking-widest" 
                              x-text="'+ Rp ' + (prices.extra_prices[jenjang] || 0).toLocaleString('id-ID') + ' / JAM'"></span>
                    </div>
                </div>
                <div class="flex items-center gap-6 bg-slate-50 p-2 rounded-2xl border border-slate-100">
                    <button type="button" @click="if(extraHours > 0) { extraHours--; saveToSession() }" 
                            class="w-12 h-12 bg-white text-slate-600 rounded-xl shadow-sm font-bold text-xl flex items-center justify-center">-</button>
                    <div class="text-center min-w-[60px]">
                        <span class="block text-2xl font-black text-slate-800" x-text="extraHours"></span>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">JAM</span>
                    </div>
                    <button type="button" @click="extraHours++; saveToSession()" 
                            class="w-12 h-12 bg-white text-slate-600 rounded-xl shadow-sm font-bold text-xl flex items-center justify-center">+</button>
                </div>
            </div>
        </section>

        {{-- 7. EKSTRA PENDAMPINGAN --}}
        <section x-show="jenjang" x-transition>
            <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2 uppercase tracking-tight">
                <span class="w-7 h-7 bg-blue-600 text-white rounded flex items-center justify-center text-xs font-black">7</span>
                EKSTRA PENDAMPINGAN
            </h3>
            <div @click="mauMengaji = !mauMengaji; saveToSession()" 
                 :class="mauMengaji ? 'border-emerald-500 bg-emerald-50 shadow-md' : 'border-slate-100 bg-slate-50'"
                 class="p-6 border-2 rounded-3xl cursor-pointer flex items-center justify-between transition-all group">
                <div class="flex items-center gap-5">
                    <div :class="mauMengaji ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-400'" class="w-14 h-14 rounded-2xl flex items-center justify-center text-xl">
                        <i class="fas fa-book-quran"></i>
                    </div>
                    <div>
                        <span class="block font-bold text-slate-800 uppercase text-sm">TAMBAHAN MENGAJI</span>
                        <span class="text-[11px] font-bold text-emerald-600 uppercase tracking-widest" 
                              x-text="'+ Rp ' + (prices.quran_prices[jenjang] || 0).toLocaleString('id-ID') + ' / BULAN'"></span>
                    </div>
                </div>
                <div :class="mauMengaji ? 'bg-emerald-500' : 'bg-slate-300'" class="w-7 h-7 rounded-full border-4 border-white shadow-md transition-all"></div>
            </div>
        </section>

        {{-- SUMMARY BAR --}}
        <div class="mt-10 p-8 bg-slate-900 text-white rounded-[2rem] shadow-xl">
            <div class="mb-8 border-b border-white/10 pb-6">
                <h4 class="text-[10px] font-black text-blue-400 uppercase tracking-[0.2em] mb-4 text-center">Rincian Kalkulasi Biaya Detail</h4>
                
                <div class="space-y-4">
                    <div class="p-4 bg-white/5 rounded-2xl border border-white/10">
                        <div class="flex justify-between items-start text-sm">
                            <div class="flex flex-col">
                                <span class="text-slate-400 font-bold uppercase text-[10px]">Harga Dasar Mapel</span>
                                <span class="text-white text-xs" x-text="selectedMapel.length + ' Pelajaran Terpilih'"></span>
                            </div>
                            <div class="text-right">
                                <template x-if="tipePaket === 'borongan'">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] text-orange-400 font-black italic">DISKON BORONGAN -25%</span>
                                        <div class="flex items-center justify-end gap-2">
                                            <span class="text-xs text-slate-500 line-through" x-text="'Rp ' + (selectedMapel.reduce((sum, id) => sum + (prices.per_id[id] || 0), 0)).toLocaleString('id-ID')"></span>
                                            <span class="font-bold text-white" x-text="'Rp ' + Math.round(selectedMapel.reduce((sum, id) => sum + (prices.per_id[id] || 0), 0) * 0.75).toLocaleString('id-ID')"></span>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="tipePaket !== 'borongan'">
                                    <span class="font-bold text-white" x-text="'Rp ' + (selectedMapel.reduce((sum, id) => sum + (prices.per_id[id] || 0), 0)).toLocaleString('id-ID')"></span>
                                </template>
                                <span class="text-[9px] text-slate-500 block uppercase font-bold">Per Kedatangan</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between items-center px-4 text-sm">
                        <div class="flex flex-col">
                            <span class="text-slate-400">Total Sesi Rutin</span>
                            <span class="text-[10px] text-blue-400 font-bold" x-text="perMinggu + 'x Seminggu × 4 Minggu = ' + (perMinggu * 4) + ' Sesi'"></span>
                        </div>
                        <span class="font-bold text-white" x-text="'Rp ' + ( (tipePaket === 'borongan' ? Math.round(selectedMapel.reduce((sum, id) => sum + (prices.per_id[id] || 0), 0) * 0.75) : selectedMapel.reduce((sum, id) => sum + (prices.per_id[id] || 0), 0)) * (perMinggu * 4) ).toLocaleString('id-ID')"></span>
                    </div>

                    <template x-if="extraHours > 0">
                        <div class="flex justify-between items-center px-4 text-sm border-t border-white/5 pt-3">
                            <div class="flex flex-col">
                                <span class="text-slate-400">Tambahan Durasi Belajar</span>
                                <span class="text-[10px] text-blue-400 font-bold" x-text="extraHours + ' Jam/Sesi × ' + (perMinggu * 4) + ' Sesi'"></span>
                            </div>
                            <div class="text-right">
                                <span class="font-bold text-blue-400" x-text="'+ Rp ' + (extraHours * (prices.extra_prices[jenjang] || 0) * perMinggu * 4).toLocaleString('id-ID')"></span>
                                <span class="text-[9px] text-slate-500 block uppercase" x-text="'@ Rp ' + (prices.extra_prices[jenjang] || 0).toLocaleString('id-ID') + '/Jam'"></span>
                            </div>
                        </div>
                    </template>

                    <template x-if="mauMengaji">
                        <div class="flex justify-between items-center px-4 text-sm border-t border-white/5 pt-3">
                            <span class="text-slate-400">Ekstra Pendampingan Mengaji</span>
                            <span class="font-bold text-emerald-400" x-text="'+ Rp ' + (prices.quran_prices[jenjang] || 0).toLocaleString('id-ID')"></span>
                        </div>
                    </template>
                </div>
            </div>

            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="text-center md:text-left">
                    <span class="text-[10px] font-bold text-blue-400 uppercase tracking-[0.2em] block mb-1">TOTAL ESTIMASI TAGIHAN BULANAN:</span>
                    <h3 class="text-4xl font-black tracking-tighter text-white" x-text="'Rp ' + totalPrice.toLocaleString('id-ID')"></h3>
                    <p class="text-[9px] text-slate-500 italic mt-1">*Sudah termasuk biaya pendaftaran & modul</p>
                </div>
                <div class="flex gap-3 w-full md:w-auto">
                    <button type="button" @click="goToStep(1)" 
                            class="flex-1 md:px-8 py-4 bg-white/10 hover:bg-white/20 rounded-2xl font-bold uppercase text-[10px] tracking-widest transition-all">
                        KEMBALI
                    </button>
                    
                    @auth
                        <button type="button" @click="goToStep(3)"
                                :disabled="!jenjang || !kelas || selectedMapel.length === 0 || scheduleSlots.length === 0" 
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
    </div> {{-- Penutup space-y-12 --}}
</div> {{-- Penutup Step 2 (x-show="step === 2") --}}


{{-- STEP 3: PEMBAYARAN --}}
<div x-show="step === 3" x-transition x-cloak>
    <div class="grid md:grid-cols-2 gap-10">
        {{-- RINGKASAN --}}
        <div class="space-y-6">
            <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">RINGKASAN PESANAN</h3>
            <div class="bg-slate-900 rounded-[2rem] p-8 text-white shadow-xl relative overflow-hidden">
                <div class="space-y-4 relative z-10">
                    <div class="flex justify-between border-b border-white/10 pb-3">
                        <span class="text-[10px] text-white/50 font-bold uppercase tracking-widest">JENJANG</span>
                        <span class="font-bold text-sm uppercase" x-text="jenjang"></span>
                    </div>
                    
                    <div class="pt-4 bg-white/5 p-6 rounded-2xl border border-white/10">
                        <p class="text-[10px] text-blue-400 font-black uppercase tracking-widest mb-1">TOTAL PEMBAYARAN</p>
                        <h3 class="text-4xl font-black tracking-tighter text-orange-500" 
                            x-text="'Rp ' + totalPrice.toLocaleString('id-ID')">
                        </h3>
                        <p class="text-[9px] text-white/60 mt-2 italic">*Nominal final dibayarkan sesuai yang tertera saat checkout.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- PAYMENT GATEWAY --}}
        <div class="space-y-6">
            <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">PEMBAYARAN ONLINE AMAN</h3>
            <div class="bg-blue-50 border-2 border-blue-100 rounded-[2rem] p-6">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-blue-600 font-black text-[10px] px-3 py-1 bg-white rounded-lg border border-blue-200 uppercase">Secure Checkout</span>
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">Encrypted</span>
                </div>
                <p class="text-sm text-slate-700 font-bold leading-relaxed">Lanjutkan ke halaman pembayaran aman untuk menyelesaikan transaksi secara otomatis.</p>
                <div class="mt-4 grid grid-cols-1 gap-2">
                    <div class="flex items-center gap-2 text-[11px] text-slate-600 font-medium">
                        <i class="fas fa-check-circle text-emerald-500"></i>
                        <span>Status pembayaran akan update otomatis setelah transaksi sukses.</span>
                    </div>
                    <div class="flex items-center gap-2 text-[11px] text-slate-600 font-medium">
                        <i class="fas fa-check-circle text-emerald-500"></i>
                        <span>Tidak perlu upload bukti transfer manual.</span>
                    </div>
                    <div class="flex items-center gap-2 text-[11px] text-slate-600 font-medium">
                        <i class="fas fa-info-circle text-blue-500"></i>
                        <span>Anda dapat memilih metode pembayaran digital yang tersedia saat checkout.</span>
                    </div>
                </div>
            </div>

            <div class="bg-slate-50 border border-slate-200 rounded-[2rem] p-6">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Alur Pembayaran</p>
                <ol class="space-y-2 text-sm text-slate-600 font-medium list-decimal pl-4">
                    <li>Klik tombol konfirmasi pembayaran.</li>
                    <li>Selesaikan pembayaran di halaman checkout.</li>
                    <li>Kembali ke dashboard, status transaksi akan sinkron otomatis.</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="mt-12 flex flex-col md:flex-row gap-4">
        <button type="button" @click="goToStep(2)" class="flex-1 bg-slate-100 text-slate-500 py-6 rounded-2xl font-bold uppercase">KEMBALI</button>
        <button type="button" @click="konfirmasiKirim()"
                class="flex-[2] bg-orange-500 hover:bg-orange-400 text-white py-6 rounded-2xl font-bold uppercase tracking-[0.2em] shadow-xl transition-all">
            BAYAR SEKARANG
        </button>
    </div>
</div>

</form> {{-- PENUTUP FORM --}}
</div> {{-- PENUTUP WHITE CARD --}}
</div> {{-- PENUTUP MAX-W-4XL --}}
</div> {{-- PENUTUP CONTAINER --}}
</div> {{-- PENUTUP X-DATA --}}

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    window.konfirmasiKirim = function() {
        const theForm = document.getElementById('formPendaftaran');

        Swal.fire({
            title: 'Lanjutkan Pembayaran?',
            text: 'Anda akan diarahkan ke halaman checkout aman untuk menyelesaikan pembayaran.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'YA, LANJUTKAN!',
            cancelButtonText: 'CEK LAGI',
            customClass: { popup: 'rounded-[2rem]' }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Menyiapkan transaksi pembayaran',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => { Swal.showLoading() }
                });
                theForm.submit();
            }
        });
    }

    @if(session('error'))
        Swal.fire({
            title: 'Pembayaran Belum Berhasil',
            text: "{{ session('error') }}",
            icon: 'error',
            confirmButtonColor: '#2563eb',
            customClass: { popup: 'rounded-[2rem]' }
        });
    @endif

    @if($errors->any())
        Swal.fire({
            title: 'Data Belum Lengkap',
            text: "{{ $errors->first() }}",
            icon: 'error',
            confirmButtonColor: '#2563eb',
            customClass: { popup: 'rounded-[2rem]' }
        });
    @endif

    @if(session('success'))
        Object.keys(sessionStorage).forEach((key) => {
            if (key.startsWith('reg_')) {
                sessionStorage.removeItem(key);
            }
        });
        Swal.fire({
            title: 'PENDAFTARAN TERKIRIM!',
            text: "{{ session('success') }}",
            icon: 'success',
            timer: 2000,
            showConfirmButton: false,
            customClass: { popup: 'rounded-[2.5rem]' }
        }).then(() => {
            window.location.href = "{{ route('siswa.overview') }}"; 
        });
    @endif
</script>

<style>
    [x-cloak] { display: none !important; }
    .swal2-popup { animation: swal-show 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important; }
</style>
@endsection
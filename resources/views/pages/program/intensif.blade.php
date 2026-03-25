@extends('layouts.app')
@section('title', 'Pendaftaran Program Intensif - Mandala')

@section('content')
<div class="min-h-screen bg-slate-50 pb-20" x-data="{ 
    // 1. LOGIKA POSISI STEP
    step: parseInt(sessionStorage.getItem('int_step')) || {{ request('step') ?? 1 }}, 
    
    // 2. LOGIKA DATA
    metode: sessionStorage.getItem('int_metode') || 'online', 
    kategori: sessionStorage.getItem('int_kategori') || '{{ $programs->where('type', 'intensif')->first()->name ?? 'UTBK-SAINTEK' }}',
    batch: sessionStorage.getItem('int_batch') || 'Batch 1 (Februari - Mei)',
    buktiTransfer: null,

    // Data Harga & ID dinamis
    listHarga: {
        @foreach($programs->where('type', 'intensif') as $prog)
            '{{ $prog->name }}': {{ $prog->price ?? 0 }},
        @endforeach
    },
    listId: {
        @foreach($programs->where('type', 'intensif') as $prog)
            '{{ $prog->name }}': {{ $prog->id }},
        @endforeach
    },
    paketMateri: {
        @foreach($programs->where('type', 'intensif') as $prog)
            '{{ $prog->name }}': `{!! addslashes($prog->description ?? 'Detail materi akan diupdate segera.') !!}`,
        @endforeach
    },

    goToStep(target) {
        this.step = target;
        this.saveToSession();
        window.scrollTo(0,0);
    },

    saveToSession() {
        sessionStorage.setItem('int_step', this.step);
        sessionStorage.setItem('int_metode', this.metode);
        sessionStorage.setItem('int_kategori', this.kategori);
        sessionStorage.setItem('int_batch', this.batch);
    },

    get totalPrice() {
        return this.listHarga[this.kategori] || 0;
    },

    handleFileUpload(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.buktiTransfer = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }
}" x-init="$watch('step', () => saveToSession()); $watch('batch', () => saveToSession());">

    {{-- Header Section --}}
    <div class="bg-slate-900 pt-20 pb-32 px-6 relative overflow-hidden text-center">
        <div class="absolute top-0 right-0 w-96 h-96 bg-orange-600/10 rounded-full -mr-20 -mt-20 blur-3xl"></div>
        <div class="relative z-10 container mx-auto transform scale-[0.85] origin-top transition-all duration-500">
            <span class="text-orange-400 font-bold tracking-widest text-xs uppercase">Mandala Intensive Academy</span>
            <h1 class="text-5xl md:text-6xl font-black text-white uppercase tracking-tighter mt-2">
                Program <span class="text-orange-500">Intensif</span>
            </h1>
            <p class="text-slate-400 mt-4 max-w-xl mx-auto font-medium">
                Akselerasi pemahaman materi dengan metode taktis dan efisien untuk mencapai target impianmu.
            </p>
        </div>
    </div>

    <div class="container mx-auto px-6 -mt-20 relative z-20">
        <div class="max-w-4xl mx-auto">
            
            {{-- Stepper Progress --}}
            <div class="flex items-center justify-between mb-10 px-4 md:px-10">
                <template x-for="(label, i) in ['Program', 'Materi', 'Pembayaran']">
                    <div class="flex items-center flex-1 last:flex-none">
                        <div class="flex flex-col items-center">
                            <div :class="step >= (i+1) ? 'bg-orange-600 text-white shadow-lg shadow-orange-500/40' : 'bg-white text-slate-300'" 
                                 class="w-12 h-12 rounded-2xl flex items-center justify-center font-bold transition-all duration-300 mb-2 border-2 border-transparent"
                                 x-text="i+1"></div>
                            <span class="text-[10px] uppercase font-bold tracking-wider" :class="step >= (i+1) ? 'text-orange-600' : 'text-slate-400'" x-text="label"></span>
                        </div>
                        <div x-show="i < 2" :class="step > (i+1) ? 'bg-orange-600' : 'bg-slate-200'" class="h-[2px] flex-grow mx-4 -mt-6 rounded-full transition-all"></div>
                    </div>
                </template>
            </div>

            <div class="bg-white rounded-[2rem] shadow-2xl p-8 md:p-14 border border-slate-100">
                <form id="formIntensif" action="{{ route('enroll.program') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    {{-- REVISI INPUT HIDDEN: Sinkronisasi dengan Controller --}}
                    <input type="hidden" name="program_id" :value="listId[kategori]">
                    <input type="hidden" name="total_harga" :value="totalPrice">
                    <input type="hidden" name="jenjang" value="Intensif">
                    <input type="hidden" name="tipe_paket" value="intensif">
                    <input type="hidden" name="per_minggu" value="1">
                    <input type="hidden" name="jadwal_detail" :value="batch">
                    <input type="hidden" name="lokasi_cabang" :value="metode === 'online' ? 'ONLINE (VIA ZOOM)' : 'OFFLINE CAMP SEMARANG'">
                    <input type="hidden" name="alamat_siswa" value="-"> 
                    <input type="hidden" name="mapel" :value="kategori">

                    {{-- STEP 1: PILIH PROGRAM --}}
                    <div x-show="step === 1" x-transition>
                        <h3 class="text-xl font-bold text-slate-800 mb-8 flex items-center gap-2 uppercase tracking-tight">
                            <span class="w-8 h-8 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center text-sm">1</span>
                            PILIH FOKUS & METODE
                        </h3>

                        <div class="grid md:grid-cols-2 gap-8">
                            <div class="space-y-4">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Fokus Program</label>
                                <template x-for="(harga, nama) in listHarga" :key="nama">
                                    <div @click="kategori = nama" 
                                         :class="kategori === nama ? 'border-orange-600 bg-orange-50 shadow-md' : 'border-slate-100 bg-slate-50'"
                                         class="p-5 border-2 rounded-2xl cursor-pointer transition-all flex justify-between items-center group">
                                        <div>
                                            <span class="block font-bold text-slate-800 uppercase text-xs" x-text="nama"></span>
                                            <span class="text-[10px] font-bold text-orange-600" x-text="'Rp ' + harga.toLocaleString('id-ID')"></span>
                                        </div>
                                        <div :class="kategori === nama ? 'bg-orange-600 border-orange-600' : 'bg-white border-slate-200'" class="w-5 h-5 rounded-full border-2 flex items-center justify-center">
                                            <div class="w-1.5 h-1.5 bg-white rounded-full" x-show="kategori === nama"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div class="space-y-4">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Metode Belajar</label>
                                <div @click="metode = 'online'" :class="metode === 'online' ? 'border-blue-600 bg-blue-50' : 'border-slate-100 bg-slate-50'" class="p-5 border-2 rounded-2xl cursor-pointer transition-all">
                                    <span class="font-bold text-slate-800 uppercase text-xs">Online Intensive (Zoom)</span>
                                </div>
                                <div @click="metode = 'offline'" :class="metode === 'offline' ? 'border-blue-600 bg-blue-50' : 'border-slate-100 bg-slate-50'" class="p-5 border-2 rounded-2xl cursor-pointer transition-all">
                                    <div class="flex justify-between items-center">
                                        <span class="font-bold text-slate-800 uppercase text-xs">Offline Camp Semarang</span>
                                        <span class="bg-green-500 text-white text-[8px] px-2 py-0.5 rounded font-black uppercase">Harga Sama</span>
                                    </div>
                                    <div x-show="metode === 'offline'" class="mt-3 p-3 bg-white rounded-xl text-[9px] text-slate-500 italic border border-blue-100">
                                        Lokasi: Tamansari Hills Residence, Tembalang.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-14 p-8 bg-slate-900 rounded-[2rem] flex flex-col md:flex-row justify-between items-center gap-6">
                            <div>
                                <span class="text-[10px] font-bold text-orange-400 uppercase tracking-widest block">Total Investasi Program:</span>
                                <h3 class="text-4xl font-black text-white" x-text="'Rp ' + totalPrice.toLocaleString('id-ID')"></h3>
                            </div>
                            <button type="button" @click="goToStep(2)" class="w-full md:w-auto px-12 py-5 bg-orange-600 hover:bg-orange-500 text-white rounded-2xl font-bold uppercase text-xs tracking-widest shadow-lg transition-all">
                                LANJUT JADWAL & MATERI
                            </button>
                        </div>
                    </div>

                    {{-- STEP 2: JADWAL & MATERI --}}
                    <div x-show="step === 2" x-transition x-cloak>
                        <div class="space-y-12">
                            <template x-if="kategori.includes('UTBK')">
                                <section>
                                    <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2 uppercase tracking-tight">
                                        <span class="w-7 h-7 bg-orange-600 text-white rounded flex items-center justify-center text-xs font-black">1</span>
                                        PILIH BATCH BELAJAR
                                    </h3>
                                    <div class="grid grid-cols-2 gap-4">
                                        <template x-for="b in ['Batch 1 (Februari - Mei)', 'Batch 2 (Juni - Agustus)']">
                                            <button type="button" @click="batch = b" 
                                                    :class="batch === b ? 'bg-slate-900 text-white border-slate-900 shadow-lg' : 'bg-white text-slate-500 border-2 border-slate-100 hover:border-orange-300'"
                                                    class="py-6 rounded-2xl font-bold uppercase text-[10px] tracking-widest transition-all" x-text="b"></button>
                                        </template>
                                    </div>
                                </section>
                            </template>

                            <section>
                                <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2 uppercase tracking-tight">
                                    <span class="w-7 h-7 bg-orange-600 text-white rounded flex items-center justify-center text-xs font-black">2</span>
                                    DETAIL KURIKULUM
                                </h3>
                                <div class="bg-slate-900 rounded-[2.5rem] p-10 text-white relative overflow-hidden shadow-xl">
                                    <div class="relative z-10">
                                        <h4 class="text-2xl font-black uppercase mb-4 text-orange-500" x-text="kategori"></h4>
                                        <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/10">
                                            <p class="text-slate-300 font-medium leading-relaxed text-sm" x-text="paketMateri[kategori]"></p>
                                        </div>
                                    </div>
                                    <i class="fas fa-graduation-cap absolute bottom-[-20px] right-[-20px] text-white/5 text-[150px] rotate-12"></i>
                                </div>
                            </section>

                            <div class="flex flex-col md:flex-row gap-4 pt-6">
                                <button type="button" @click="goToStep(1)" class="flex-1 bg-slate-100 text-slate-500 py-6 rounded-2xl font-bold uppercase tracking-widest hover:bg-slate-200 transition-all">KEMBALI</button>
                                @auth
                                    <button type="button" @click="goToStep(3)" class="flex-[2] bg-orange-600 text-white py-6 rounded-2xl font-bold uppercase tracking-widest shadow-xl shadow-orange-200 hover:bg-orange-700 transition-all">CEK RINGKASAN BAYAR</button>
                                @else
                                    <a href="{{ route('pendaftaran.lanjut', ['type' => 'intensif', 'step' => 3]) }}" class="flex-[2] bg-blue-600 text-white py-6 rounded-2xl font-bold uppercase tracking-widest text-center shadow-xl hover:bg-blue-700 transition-all flex items-center justify-center">LOGIN UNTUK BAYAR</a>
                                @endauth
                            </div>
                        </div>
                    </div>

                    {{-- STEP 3: PEMBAYARAN --}}
                    <div x-show="step === 3" x-transition x-cloak>
                        <div class="grid md:grid-cols-2 gap-10">
                            <div class="space-y-6">
                                <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">RINGKASAN PESANAN</h3>
                                <div class="bg-slate-900 rounded-[2rem] p-8 text-white shadow-xl relative overflow-hidden">
                                    <div class="space-y-4 relative z-10">
                                        <div class="flex justify-between border-b border-white/10 pb-3">
                                            <span class="text-[10px] text-white/50 font-bold uppercase tracking-widest">PROGRAM</span>
                                            <span class="font-bold text-sm uppercase text-orange-500" x-text="kategori"></span>
                                        </div>
                                        <div class="pt-4 bg-white/5 p-6 rounded-2xl border border-white/10">
                                            <p class="text-[10px] text-blue-400 font-black uppercase tracking-widest mb-1">TOTAL TRANSFER (+ KODE UNIK)</p>
                                            <h3 class="text-4xl font-black tracking-tighter text-orange-500" 
                                                x-text="'Rp ' + (totalPrice + parseInt('{{ substr(preg_replace('/[^0-9]/', '', Auth::user()->whatsapp ?? '000'), -3) }}')).toLocaleString('id-ID')">
                                            </h3>
                                            <p class="text-[9px] text-white/60 mt-2 italic">*Mohon transfer tepat sesuai nominal hingga 3 digit terakhir.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">TRANSFER PEMBAYARAN</h3>
                                <div class="bg-blue-50 border-2 border-blue-100 rounded-[2rem] p-6 text-center">
                                    <span class="text-blue-600 font-black text-[10px] px-3 py-1 bg-white rounded-lg border border-blue-200 uppercase">Mandiri - Mandala Academy</span>
                                    <div class="text-3xl font-black text-slate-800 tracking-wider py-2">900 1234 5678 00</div>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase">A.N MANDALA ACADEMY</p>
                                </div>

                                <div class="space-y-3">
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">UPLOAD BUKTI TRANSFER</span>
                                    <label class="block border-2 border-dashed border-slate-200 rounded-[2rem] p-8 text-center cursor-pointer hover:bg-slate-50 relative h-40 flex items-center justify-center overflow-hidden">
                                        <input type="file" name="bukti_pembayaran" id="file_bukti_int" @change="handleFileUpload" class="hidden" accept="image/*" required>
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
                            <button type="button" @click="goToStep(2)" class="flex-1 bg-slate-100 text-slate-500 py-6 rounded-2xl font-bold uppercase tracking-widest hover:bg-slate-200 transition-all">KEMBALI</button>
                            <button type="button" @click="konfirmasiKirimIntensif()" :disabled="!buktiTransfer" 
                                    class="flex-[2] bg-orange-600 text-white py-6 rounded-2xl font-bold uppercase tracking-[0.2em] shadow-xl disabled:opacity-50 hover:bg-orange-700 transition-all">
                                KONFIRMASI SEKARANG
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    window.konfirmasiKirimIntensif = function() {
        const theForm = document.getElementById('formIntensif');
        const fileInput = document.getElementById('file_bukti_int');

        if (!fileInput.files || fileInput.files.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Bukti Belum Diupload',
                text: 'Silakan upload foto bukti transfer terlebih dahulu.',
                confirmButtonColor: '#ea580c',
                customClass: { popup: 'rounded-[2rem]' }
            });
            return;
        }

        Swal.fire({
            title: 'Kirim Pendaftaran?',
            text: "Pastikan nominal transfer sudah sesuai dengan kode unik!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ea580c',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'YA, KIRIM!',
            cancelButtonText: 'BATAL',
            customClass: { popup: 'rounded-[2rem]' }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Sedang mengirim data ke admin',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => { Swal.showLoading() }
                });
                sessionStorage.clear();
                theForm.submit();
            }
        });
    }

    @if(session('success'))
        sessionStorage.clear(); 
        Swal.fire({
            title: 'BERHASIL TERKIRIM!',
            text: "{{ session('success') }}",
            icon: 'success',
            timer: 2500,
            showConfirmButton: false,
            customClass: { popup: 'rounded-[2.5rem]' }
        }).then(() => {
            window.location.href = "{{ route('siswa.overview') }}"; 
        });
    @endif
</script>

<style>
    [x-cloak] { display: none !important; }
    * { font-style: normal !important; font-family: 'Plus Jakarta Sans', sans-serif; }
</style>
@endsection
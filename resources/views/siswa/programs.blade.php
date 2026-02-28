@extends('siswa.dashboard_siswa_layout')
@section('siswa_content')
<div x-data="{ openModal: false, selectedProgram: null }" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4">
    <div class="mb-8">
        <h2 class="text-3xl font-black text-slate-800 tracking-tighter">Kelas Saya</h2>
        <p class="text-sm text-slate-500 mt-1 font-medium">Kelola semua program pembelajaran Anda</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @if($programs && count($programs) > 0)
            @foreach($programs as $program)
                <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-lg transition">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <p class="font-black text-slate-800 text-lg">{{ $program->name }}</p>
                            <p class="text-xs text-slate-500 mt-1">{{ $program->jenjang ?? 'Program' }}</p>
                        </div>
                        <div class="{{ $program->status_pembayaran === 'verified' ? 'bg-green-100 text-green-600' : 'bg-blue-100 text-blue-600' }} px-3 py-1 rounded-lg text-xs font-black">
                            {{ $program->status_pembayaran === 'verified' ? 'Aktif' : 'Pending' }}
                        </div>
                    </div>

                    <div class="py-4 border-y border-slate-100 my-4">
                        <p class="text-sm text-slate-600 font-bold">
                            <i class="fas fa-chalkboard-teacher mr-2 text-orange-500"></i>
                            {{ $program->mentor_name ?? 'Mentor Mandala' }}
                        </p>
                    </div>

                    <div class="mb-4">
                        <p class="text-sm font-bold text-slate-600 mb-2">Progress Belajar</p>
                        <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden">
                            <div class="bg-blue-500 h-full w-[45%]"></div>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Sesi Berjalan</p>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-center py-3 border-t border-slate-100">
                        <div>
                            <p class="text-2xl font-black text-blue-600">12</p>
                            <p class="text-[10px] text-slate-500 font-bold uppercase">Total Sesi</p>
                        </div>
                        <div>
                            <p class="text-2xl font-black text-green-600">
                                @if(isset($program->price))
                                    Rp {{ number_format($program->price, 0, ',', '.') }}
                                @else
                                    Lunas
                                @endif
                            </p>
                            <p class="text-[10px] text-slate-500 font-bold uppercase">Investasi</p>
                        </div>
                    </div>

                    @if($program->status_pembayaran === 'verified')
                        <button @click="openModal = true; selectedProgram = {{ json_encode($program) }}" 
                                class="w-full mt-4 bg-blue-600 text-white px-4 py-3 rounded-xl font-bold hover:bg-blue-700 transition shadow-md shadow-blue-100">
                            Buka Materi & Video
                        </button>
                    @else
                        <button class="w-full mt-4 bg-slate-100 text-slate-400 px-4 py-3 rounded-xl font-bold cursor-not-allowed">
                            Menunggu Verifikasi
                        </button>
                    @endif
                </div>
            @endforeach
        @else
            <div class="col-span-full text-center py-12">
                <i class="fas fa-inbox text-slate-300 text-4xl mb-4"></i>
                <p class="text-slate-500 font-semibold mb-4">Belum ada program aktif</p>
                <a href="{{ route('home') }}" class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700">
                    Cari Program
                </a>
            </div>
        @endif
    </div>

    <div x-show="openModal" 
         class="fixed inset-0 z-[99] overflow-y-auto" 
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div @click="openModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
            
            <div class="relative bg-slate-50 w-full max-w-4xl rounded-[2.5rem] shadow-2xl overflow-hidden" 
                 x-show="openModal"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-end="opacity-0 scale-95">
                
                <div class="bg-white px-8 py-6 border-b border-slate-100 flex justify-between items-center">
                    <div>
                        <h3 class="text-2xl font-black text-slate-800" x-text="selectedProgram ? selectedProgram.name : ''"></h3>
                        <p class="text-blue-600 font-bold text-sm">Kurikulum & Materi Pembelajaran</p>
                    </div>
                    <button @click="openModal = false" class="text-slate-400 hover:text-slate-600 transition">
                        <i class="fas fa-times-circle text-2xl"></i>
                    </button>
                </div>

                <div class="p-8 max-h-[70vh] overflow-y-auto">
                    <div class="space-y-6">
                        @php
                            // Catatan: Secara ideal materi dipassing dari controller ke view ini.
                            // Jika data materi sudah ada di object $programs, kita bisa meloopingnya.
                        @endphp
                        
                        <template x-if="selectedProgram">
                            <div class="grid gap-6">
                                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                                    <div class="flex items-center gap-4 mb-4">
                                        <div class="w-12 h-12 bg-orange-100 rounded-2xl flex items-center justify-center text-orange-600">
                                            <i class="fas fa-play"></i>
                                        </div>
                                        <div>
                                            <p class="font-black text-slate-800">Video Pembelajaran</p>
                                            <p class="text-xs text-slate-500">Tonton materi sesi terbaru</p>
                                        </div>
                                    </div>
                                    
                                    <div class="aspect-video rounded-2xl overflow-hidden bg-slate-800 shadow-inner">
                                        <iframe class="w-full h-full" 
                                                :src="selectedProgram.video_url" 
                                                frameborder="0" 
                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                                allowfullscreen></iframe>
                                    </div>
                                </div>

                                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-blue-100 rounded-2xl flex items-center justify-center text-blue-600">
                                            <i class="fas fa-file-pdf"></i>
                                        </div>
                                        <div>
                                            <p class="font-black text-slate-800">Modul PDF</p>
                                            <p class="text-xs text-slate-500">Download materi bacaan sesi ini</p>
                                        </div>
                                    </div>
                                    <a :href="'/storage/' + selectedProgram.file_path" 
                                       target="_blank"
                                       class="bg-slate-100 hover:bg-blue-600 hover:text-white px-6 py-2 rounded-xl font-bold transition">
                                        Download
                                    </a>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        
        if (urlParams.get('pendaftaran') === 'berhasil') {
            Swal.close();

            Swal.fire({
                title: 'PENDAFTARAN TERKIRIM!',
                html: `
                    <div class="text-center">
                        <p style="margin-bottom: 20px; color: #666;">Data kamu sudah kami terima. Silakan konfirmasi ke WhatsApp Admin untuk aktivasi akun.</p>
                        <a href="https://wa.me/628123456789?text=Halo%20Admin,%20saya%20sudah%20daftar%20program%20reguler" 
                           target="_blank" 
                           style="background-color: #25D366; color: white; padding: 12px 25px; border-radius: 10px; text-decoration: none; font-weight: bold; display: inline-block;">
                            KONFIRMASI VIA WHATSAPP
                        </a>
                    </div>
                `,
                icon: 'success',
                confirmButtonText: 'OKE, SAYA PAHAM',
                confirmButtonColor: '#2563eb',
                allowOutsideClick: false,
                customClass: {
                    popup: 'rounded-[2rem]'
                }
            }).then(() => {
                window.history.replaceState({}, document.title, window.location.pathname);
            });
        }
    });
</script>
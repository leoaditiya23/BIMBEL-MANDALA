@extends('siswa.dashboard_siswa_layout')

@section('siswa_content')
<div x-data="{ 
    openModal: false, 
    selectedProgram: null,
    submitTask(materialId) {
        const link = document.getElementById('task_link_' + materialId).value;
        if(!link) return Swal.fire('Error', 'Link tugas tidak boleh kosong', 'error');
        
        fetch('{{ route('siswa.submitTask') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ material_id: materialId, link: link })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                Swal.fire('Berhasil', data.message, 'success');
            }
        });
    },
    doAbsen(materialId) {
        fetch('{{ route('siswa.absen') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ material_id: materialId })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                Swal.fire('Berhasil', data.message, 'success').then(() => location.reload());
            } else {
                Swal.fire('Gagal', data.message, 'error');
            }
        });
    }
}" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4">
    
    <div class="mb-8">
        <h2 class="text-3xl font-black text-slate-800 tracking-tighter">Kelas Saya</h2>
        <p class="text-sm text-slate-500 mt-1 font-medium">Kelola semua program pembelajaran Anda</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {{-- Menggunakan variabel my_programs agar cocok dengan Controller --}}
        @if(isset($my_programs) && count($my_programs) > 0)
            @foreach($my_programs as $program)
                <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-lg transition group">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            {{-- REVISI: Menggunakan display_mapel agar muncul nama pelajaran, bukan ID --}}
                            <p class="font-black text-slate-800 text-lg uppercase tracking-tight group-hover:text-blue-600 transition-colors">
                                {{ $program->display_mapel ?? $program->base_name }}
                            </p>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">PROGRAM {{ $program->jenjang ?? 'PILIHAN' }}</p>
                        </div>
                        <div class="{{ $program->status_pembayaran === 'verified' ? 'bg-emerald-100 text-emerald-600' : 'bg-blue-100 text-blue-600' }} px-3 py-1 rounded-lg text-[9px] font-black uppercase italic">
                            {{ $program->status_pembayaran === 'verified' ? 'Aktif' : 'Pending' }}
                        </div>
                    </div>

                    <div class="py-4 border-y border-slate-50 my-4 space-y-3">
                        {{-- Detail Mentor --}}
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 bg-orange-50 rounded-lg flex items-center justify-center text-orange-500 text-xs">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <p class="text-xs font-bold text-slate-700">{{ $program->mentor_name ?? 'Mentor Mandala Academy' }}</p>
                        </div>
                        
                        {{-- Detail Lokasi & Metode --}}
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 bg-rose-50 rounded-lg flex items-center justify-center text-rose-500 text-xs">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="flex flex-col">
                                <p class="text-[10px] text-blue-600 font-black uppercase leading-tight">
                                    {{ $program->lokasi_style ?? 'ONLINE' }}
                                </p>
                                <p class="text-[9px] text-slate-500 font-medium italic leading-tight">
                                    {{ $program->detail_lokasi ?? 'Interactive Virtual Class' }}
                                </p>
                            </div>
                        </div>

                        {{-- Detail Jadwal --}}
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 bg-indigo-50 rounded-lg flex items-center justify-center text-indigo-500 text-xs">
                                <i class="fas fa-clock"></i>
                            </div>
                            <p class="text-[9px] font-bold text-slate-500 italic">{{ $program->jadwal_detail ?? 'Jadwal rutin belum diatur' }}</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        @php
                            $selesai = $program->pertemuan_selesai ?? 0;
                            $total = ($program->jumlah_pertemuan > 0) ? $program->jumlah_pertemuan : 8;
                            $percent = ($selesai / $total) * 100;
                        @endphp
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Progress Belajar</p>
                        <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-blue-500 h-full transition-all duration-1000" style="width: {{ $percent }}%"></div>
                        </div>
                        <div class="flex justify-between mt-1">
                            <p class="text-[9px] text-slate-400 font-medium italic">Sesi {{ $selesai }} dari {{ $total }}</p>
                            <p class="text-[9px] font-black text-blue-600">{{ round($percent) }}%</p>
                        </div>
                    </div>

                    @if($program->status_pembayaran === 'verified')
                        <button @click="selectedProgram = @js($program); openModal = true" 
                                class="w-full mt-4 bg-slate-900 text-white px-4 py-3.5 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-600 transition shadow-lg shadow-slate-100 group-hover:shadow-blue-100">
                            Buka Materi & Absen
                        </button>
                    @else
                        <button disabled class="w-full mt-4 bg-slate-50 text-slate-300 px-4 py-3.5 rounded-2xl text-[10px] font-black uppercase tracking-widest cursor-not-allowed border border-slate-100">
                            Menunggu Verifikasi
                        </button>
                    @endif
                </div>
            @endforeach
        @else
            <div class="col-span-full text-center py-20 bg-white rounded-[3rem] border border-dashed border-slate-200">
                <i class="fas fa-inbox text-slate-200 text-5xl mb-4"></i>
                <p class="text-slate-500 font-bold uppercase tracking-widest text-xs">Belum ada program aktif terdaftar</p>
                <a href="{{ route('home') }}" class="inline-block mt-6 bg-blue-600 text-white px-10 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-700 transition shadow-xl shadow-blue-100">
                    Cari Program
                </a>
            </div>
        @endif
    </div>

    <template x-if="openModal">
        <div class="fixed inset-0 z-[99] overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div @click="openModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
                
                <div class="relative bg-slate-50 w-full max-w-4xl rounded-[2.5rem] shadow-2xl overflow-hidden"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95">
                    
                    <div class="bg-white px-8 py-6 border-b border-slate-100 flex justify-between items-center">
                        <div>
                            <h3 class="text-2xl font-black text-slate-800 uppercase tracking-tight" x-text="selectedProgram.display_mapel || selectedProgram.base_name"></h3>
                            <p class="text-blue-600 font-bold text-[10px] uppercase tracking-[0.2em]">Kurikulum & Presensi Siswa</p>
                        </div>
                        <button @click="openModal = false" class="text-slate-300 hover:text-rose-500 transition-colors">
                            <i class="fas fa-times-circle text-2xl"></i>
                        </button>
                    </div>

                    <div class="p-8 max-h-[70vh] overflow-y-auto">
                        <div class="space-y-6">
                            <template x-for="(material, index) in selectedProgram.materials" :key="material.id">
                                <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:border-blue-100 transition-colors">
                                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 font-black text-lg shadow-inner" x-text="material.session_number"></div>
                                            <div>
                                                <p class="font-black text-slate-800 uppercase text-sm tracking-tight" x-text="material.title"></p>
                                                <p class="text-[10px] font-bold text-slate-400 mt-0.5" x-text="'Rilis pada: ' + new Date(material.created_at).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'})"></p>
                                            </div>
                                        </div>
                                        <div class="flex gap-2 w-full md:w-auto">
                                            <button @click="doAbsen(material.id)" class="flex-1 md:flex-none bg-emerald-500 text-white px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-600 transition shadow-md shadow-emerald-50">
                                                <i class="fas fa-check-circle mr-1"></i> Absen
                                            </button>
                                            <a :href="material.video_url" target="_blank" class="flex-1 md:flex-none bg-orange-100 text-orange-600 px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest text-center">
                                                <i class="fas fa-play mr-1"></i> Video
                                            </a>
                                            <a :href="'/storage/' + material.file_path" target="_blank" class="flex-1 md:flex-none bg-blue-600 text-white px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest text-center shadow-md shadow-blue-50">
                                                <i class="fas fa-file-pdf mr-1"></i> Modul
                                            </a>
                                        </div>
                                    </div>

                                    <div class="mt-5 pt-5 border-t border-dashed border-slate-100">
                                        <label class="text-[9px] font-black text-slate-400 uppercase mb-2 block tracking-widest">Pengumpulan Link Tugas (Cloud/Drive)</label>
                                        <div class="flex gap-2">
                                            <input type="url" :id="'task_link_' + material.id" placeholder="https://drive.google.com/..." class="flex-1 bg-slate-50 border-none rounded-xl text-xs font-bold focus:ring-2 focus:ring-blue-500 px-4 py-3 placeholder:text-slate-300">
                                            <button @click="submitTask(material.id)" class="bg-slate-800 text-white px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-black transition">Kirim</button>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <template x-if="!selectedProgram.materials || selectedProgram.materials.length === 0">
                                <div class="text-center py-20 bg-white rounded-[2rem] border border-slate-50">
                                    <i class="fas fa-hourglass-half text-slate-200 text-4xl mb-4"></i>
                                    <p class="text-slate-400 font-bold uppercase text-[10px] tracking-widest">Materi kurikulum belum diunggah</p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('pendaftaran') === 'berhasil') {
            Swal.fire({
                title: 'PENDAFTARAN TERKIRIM!',
                html: `<div class="text-center"><p style="margin-bottom: 20px; color: #666; font-size: 14px;">Silakan konfirmasi ke WhatsApp Admin untuk aktivasi akun Anda segera.</p><a href="https://wa.me/628123456789?text=Konfirmasi%20Pendaftaran%20Bimbel" target="_blank" style="background-color: #25D366; color: white; padding: 15px 30px; border-radius: 12px; text-decoration: none; font-weight: 900; font-size: 12px; display: inline-block; letter-spacing: 1px;">KONFIRMASI VIA WHATSAPP</a></div>`,
                icon: 'success',
                confirmButtonText: 'OKE',
                confirmButtonColor: '#2563eb',
                customClass: { popup: 'rounded-[2.5rem]' }
            }).then(() => {
                window.history.replaceState({}, document.title, window.location.pathname);
            });
        }
    });
</script>
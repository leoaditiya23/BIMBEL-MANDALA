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
                        @php
                            $total_materi = isset($program->materials) ? count($program->materials) : 0;
                            $target_sesi = 12; 
                            $percent = ($total_materi / $target_sesi) * 100;
                        @endphp
                        <p class="text-sm font-bold text-slate-600 mb-2">Progress Belajar</p>
                        <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden">
                            <div class="bg-blue-500 h-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Sesi {{ $total_materi }} berjalan dari {{ $target_sesi }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-center py-3 border-t border-slate-100">
                        <div>
                            <p class="text-2xl font-black text-blue-600">{{ $target_sesi }}</p>
                            <p class="text-[10px] text-slate-500 font-bold uppercase">Total Sesi</p>
                        </div>
                        <div>
                            <p class="text-lg font-black text-green-600">
                                {{ $program->status_pembayaran === 'verified' ? 'LUNAS' : 'PENDING' }}
                            </p>
                            <p class="text-[10px] text-slate-500 font-bold uppercase">Pembayaran</p>
                        </div>
                    </div>

                    @if($program->status_pembayaran === 'verified')
                        <button @click="selectedProgram = @js($program); openModal = true" 
                                class="w-full mt-4 bg-blue-600 text-white px-4 py-3 rounded-xl font-bold hover:bg-blue-700 transition shadow-md shadow-blue-100">
                            Buka Materi & Absen
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

    <template x-if="openModal">
        <div class="fixed inset-0 z-[99] overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div @click="openModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
                
                <div class="relative bg-slate-50 w-full max-w-4xl rounded-[2.5rem] shadow-2xl overflow-hidden"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95">
                    
                    <div class="bg-white px-8 py-6 border-b border-slate-100 flex justify-between items-center">
                        <div>
                            <h3 class="text-2xl font-black text-slate-800" x-text="selectedProgram.name"></h3>
                            <p class="text-blue-600 font-bold text-sm">Kurikulum & Presensi Siswa</p>
                        </div>
                        <button @click="openModal = false" class="text-slate-400 hover:text-slate-600 transition">
                            <i class="fas fa-times-circle text-2xl"></i>
                        </button>
                    </div>

                    <div class="p-8 max-h-[70vh] overflow-y-auto">
                        <div class="space-y-6">
                            <template x-for="(material, index) in selectedProgram.materials" :key="material.id">
                                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 bg-blue-100 rounded-2xl flex items-center justify-center text-blue-600 font-black" x-text="material.session_number"></div>
                                            <div>
                                                <p class="font-black text-slate-800" x-text="material.title"></p>
                                                <p class="text-xs text-slate-500" x-text="'Tersedia sejak: ' + new Date(material.created_at).toLocaleDateString('id-ID')"></p>
                                            </div>
                                        </div>
                                        <div class="flex gap-2 w-full md:w-auto">
                                            <button @click="doAbsen(material.id)" class="flex-1 md:flex-none bg-green-500 text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-green-600 transition">
                                                <i class="fas fa-check-circle mr-1"></i> Absen
                                            </button>
                                            <a :href="material.video_url" target="_blank" class="flex-1 md:flex-none bg-orange-100 text-orange-600 px-4 py-2 rounded-xl text-xs font-bold text-center">
                                                <i class="fas fa-play mr-1"></i> Video
                                            </a>
                                            <a :href="'/storage/' + material.file_path" target="_blank" class="flex-1 md:flex-none bg-blue-600 text-white px-4 py-2 rounded-xl text-xs font-bold text-center">
                                                <i class="fas fa-file-pdf mr-1"></i> Modul
                                            </a>
                                        </div>
                                    </div>

                                    <div class="mt-4 pt-4 border-t border-dashed border-slate-100">
                                        <label class="text-[10px] font-black text-slate-400 uppercase mb-2 block">Link Tugas (Drive/Github)</label>
                                        <div class="flex gap-2">
                                            <input type="url" :id="'task_link_' + material.id" placeholder="https://..." class="flex-1 bg-slate-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500 px-4 py-2">
                                            <button @click="submitTask(material.id)" class="bg-slate-800 text-white px-6 py-2 rounded-xl text-xs font-bold hover:bg-black transition">Kirim</button>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <template x-if="!selectedProgram.materials || selectedProgram.materials.length === 0">
                                <div class="text-center py-10">
                                    <i class="fas fa-hourglass-half text-slate-300 text-3xl mb-3"></i>
                                    <p class="text-slate-500 font-medium">Belum ada materi untuk program ini.</p>
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
                html: `<div class="text-center"><p style="margin-bottom: 20px; color: #666;">Silakan konfirmasi ke WhatsApp Admin untuk aktivasi.</p><a href="https://wa.me/628123456789?text=Konfirmasi%20Pendaftaran" target="_blank" style="background-color: #25D366; color: white; padding: 12px 25px; border-radius: 10px; text-decoration: none; font-weight: bold; display: inline-block;">KONFIRMASI VIA WHATSAPP</a></div>`,
                icon: 'success',
                confirmButtonText: 'OKE',
                confirmButtonColor: '#2563eb',
                customClass: { popup: 'rounded-[2rem]' }
            }).then(() => {
                window.history.replaceState({}, document.title, window.location.pathname);
            });
        }
    });
</script>
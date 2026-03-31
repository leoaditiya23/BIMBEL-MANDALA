@extends('siswa.dashboard_siswa_layout')

@section('siswa_content')
<div x-data="{ 
    openModal: false, 
    selectedProgram: null,
    fileNameTask: '', {{-- Variabel untuk menyimpan nama file PDF yang dipilih --}}

    {{-- Fungsi Kirim Tugas (Mendukung Link & PDF) --}}
    submitTask(materialId) {
        const linkInput = document.getElementById('task_link_' + materialId);
        const fileInput = document.getElementById('task_file_' + materialId);
        
        const linkValue = linkInput ? linkInput.value.trim() : '';
        const hasFile = fileInput && fileInput.files && fileInput.files.length > 0;
        
        if (!linkValue && !hasFile) {
            return Swal.fire({title: 'Info', text: 'Mohon isi link atau lampirkan PDF', icon: 'warning'});
        }
        
        let formData = new FormData();
        formData.append('material_id', materialId);
        if (linkValue) formData.append('link', linkValue);
        if (hasFile) formData.append('file', fileInput.files[0]);

        // Berikan loading agar siswa tahu proses sedang berjalan
        Swal.fire({title: 'Sedang Mengirim...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); }});

        fetch('{{ route('siswa.submitTask') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: formData
        })
        .then(async res => {
            const data = await res.json();
            if(!res.ok) throw new Error(data.message || 'Kesalahan Server');
            return data;
        })
        .then(data => {
            Swal.fire({title: 'Berhasil', text: data.message, icon: 'success', customClass: {popup: 'rounded-[2rem]'}});
            this.fileNameTask = '';
            if(linkInput) linkInput.value = '';
            if(fileInput) fileInput.value = '';
        })
        .catch(err => {
            Swal.fire({title: 'Gagal', text: err.message, icon: 'error'});
        });
    },

    {{-- Fungsi Absensi --}}
    doAbsen(status) {
        if(!this.selectedProgram) return;
        
        fetch('{{ route('siswa.absen') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ 
                material_id: this.selectedProgram.enrollment_id, 
                status: status 
            })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                Swal.fire({title: 'Berhasil!', text: 'Kehadiran ditandai sebagai ' + status, icon: 'success', customClass: {popup: 'rounded-[2rem]'}}).then(() => location.reload());
            } else {
                Swal.fire({title: 'Gagal', text: data.message, icon: 'error', customClass: {popup: 'rounded-[2rem]'}});
            }
        });
    }
}" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4">
    
    <div class="mb-8">
        <h2 class="text-3xl font-black text-slate-800 tracking-tighter">Kelas Saya</h2>
        <p class="text-sm text-slate-500 mt-1 font-medium">Kelola semua program pembelajaran Anda</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @if(isset($my_programs) && count($my_programs) > 0)
            @foreach($my_programs as $program)
                <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-lg transition group">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <p class="font-black text-slate-800 text-lg uppercase tracking-tight group-hover:text-blue-600 transition-colors">
                                {{ $program->display_mapel ?? $program->base_name }}
                            </p>
                            {{-- REVISI: MENAMPILKAN KELAS PADA KARTU --}}
                            <p class="text-[10px] font-bold text-blue-600 uppercase tracking-widest mt-1">PROGRAM {{ $program->kelas ?? '-' }}</p>
                        </div>
                        <div class="{{ $program->status_pembayaran === 'verified' ? 'bg-emerald-100 text-emerald-600' : 'bg-blue-100 text-blue-600' }} px-3 py-1 rounded-lg text-[9px] font-black uppercase italic">
                            {{ $program->status_pembayaran === 'verified' ? 'Aktif' : 'Pending' }}
                        </div>
                    </div>

                    <div class="py-4 border-y border-slate-50 my-4 space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 bg-orange-50 rounded-lg flex items-center justify-center text-orange-500 text-xs">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <p class="text-xs font-bold text-slate-700">{{ $program->mentor_name ?? 'Mentor Mandala Academy' }}</p>
                        </div>
                        
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 bg-rose-50 rounded-lg flex items-center justify-center text-rose-500 text-xs">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="flex flex-col">
                                <p class="text-[10px] text-blue-600 font-black uppercase leading-tight">
                                    {{ str_contains(strtolower($program->lokasi_cabang ?? ''), 'online') ? 'ONLINE' : 'OFFLINE' }}
                                </p>
                                {{-- REVISI: Penambahan Null Coalescing agar tidak error 500 jika property kosong --}}
                                <p class="text-[9px] text-slate-500 font-medium italic leading-tight">
                                    {{ ($program->alamat_siswa ?? '') ?: 'Interactive Virtual Class' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        @php
                            $selesai = $program->pertemuan_selesai ?? 0;
                            $total = ($program->jumlah_pertemuan > 0) ? $program->jumlah_pertemuan : 8;
                            $percent = ($total > 0) ? ($selesai / $total) * 100 : 0;
                        @endphp
                        <div class="flex justify-between mb-2">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Progress Sesi</p>
                            <p class="text-[10px] font-black text-blue-600">{{ round($percent) }}%</p>
                        </div>
                        <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-blue-500 h-full transition-all duration-1000" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>

                    @if($program->status_pembayaran === 'verified')
                        <button @click="selectedProgram = @js($program); openModal = true" 
                                class="w-full mt-4 bg-slate-900 text-white px-4 py-3.5 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-600 transition shadow-lg shadow-slate-100">
                            Buka Ruang Kelas
                        </button>
                    @else
                        <button disabled class="w-full mt-4 bg-slate-50 text-slate-300 px-4 py-3.5 rounded-2xl text-[10px] font-black uppercase tracking-widest cursor-not-allowed border border-slate-100">
                            Menunggu Verifikasi
                        </button>
                    @endif
                </div>
            @endforeach
        @endif
    </div>

    {{-- MODAL DETAIL --}}
    <template x-if="openModal">
        <div class="fixed inset-0 z-[99] overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div @click="openModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
                
                <div class="relative bg-white w-full max-w-4xl rounded-[2.5rem] shadow-2xl overflow-hidden flex flex-col max-h-[90vh]"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95">
                    
                    <div class="bg-white px-8 py-6 border-b border-slate-100 flex justify-between items-center">
                        <div>
                            <h3 class="text-2xl font-black text-slate-800 uppercase tracking-tight" x-text="selectedProgram.display_mapel || selectedProgram.base_name"></h3>
                            {{-- REVISI: MENAMPILKAN KELAS DI HEADER MODAL --}}
                            <p class="text-blue-600 font-bold text-[10px] uppercase tracking-[0.2em]" x-text="'Tingkat: ' + (selectedProgram.kelas || '-') + ' | Kurikulum & Presensi'"></p>
                        </div>
                        <button @click="openModal = false" class="text-slate-300 hover:text-rose-500 transition-colors">
                            <i class="fas fa-times-circle text-2xl"></i>
                        </button>
                    </div>

                    <div class="p-8 max-h-[70vh] overflow-y-auto space-y-8 custom-scrollbar">
                        
                        {{-- CARD ABSENSI & REKAP --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="md:col-span-1 bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm text-center flex flex-col justify-center">
                                <div class="w-16 h-16 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-4 text-emerald-500">
                                    <i class="fas fa-fingerprint text-2xl"></i>
                                </div>
                                <h4 class="font-black text-slate-800 uppercase text-xs mb-1">Presensi Hari Ini</h4>
                                
                                <template x-if="selectedProgram.is_absen_active">
                                    <div>
                                        <p class="text-[9px] text-emerald-600 font-black uppercase tracking-widest mb-4">Absensi telah dibuka!</p>
                                        <div class="grid grid-cols-1 gap-2">
                                            <button @click="doAbsen('Hadir')" class="bg-emerald-500 text-white py-3 rounded-xl text-[10px] font-black uppercase hover:bg-emerald-600 transition shadow-lg">HADIR</button>
                                            <div class="grid grid-cols-2 gap-2">
                                                <button @click="doAbsen('Izin')" class="bg-amber-500 text-white py-2 rounded-xl text-[9px] font-black uppercase hover:bg-amber-600 transition">IZIN</button>
                                                <button @click="doAbsen('Sakit')" class="bg-rose-500 text-white py-2 rounded-xl text-[9px] font-black uppercase hover:bg-rose-600 transition">SAKIT</button>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!selectedProgram.is_absen_active">
                                    <div class="bg-slate-50 py-4 rounded-xl border border-dashed border-slate-200">
                                        <p class="text-[9px] text-slate-400 font-black uppercase tracking-widest leading-none">Menunggu Mentor</p>
                                    </div>
                                </template>
                            </div>

                            <div class="md:col-span-2 bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
                                <h4 class="font-black text-slate-800 uppercase text-xs mb-4 flex items-center gap-2">
                                    <i class="fas fa-history text-blue-500"></i> Rekapitulasi Sesi
                                </h4>
                                <div class="grid grid-cols-4 sm:grid-cols-6 gap-3">
                                    <template x-for="(i, index) in Array.from({length: (selectedProgram.jumlah_pertemuan || 8)})" :key="index">
                                        <div :class="index < selectedProgram.pertemuan_selesai ? 'bg-emerald-500 border-emerald-500' : 'bg-white border-slate-100 shadow-sm'" 
                                             class="aspect-square rounded-2xl border-2 flex flex-col items-center justify-center transition-all duration-500 relative group">
                                            <span :class="index < selectedProgram.pertemuan_selesai ? 'text-white' : 'text-slate-300'" class="text-[10px] font-black" x-text="index + 1"></span>
                                        </div>
                                    </template>
                                </div>
                                <div class="mt-4 flex justify-between items-center bg-blue-50/50 p-3 rounded-xl border border-blue-100">
                                    <span class="text-[9px] font-black text-blue-600 uppercase tracking-widest">Progress</span>
                                    <span class="text-xs font-black text-slate-700" x-text="selectedProgram.pertemuan_selesai + '/' + (selectedProgram.jumlah_pertemuan || 8) + ' Sesi Selesai'"></span>
                                </div>
                            </div>
                        </div>

                        {{-- DAFTAR MATERI --}}
                        <div class="space-y-6">
                            <h4 class="font-black text-slate-400 uppercase text-[10px] tracking-[0.2em] px-2 italic">Modul & Penilaian</h4>
                            <template x-for="(material, index) in selectedProgram.materials" :key="material.id">
                                <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:border-blue-100 transition-colors group">
                                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                                        <div class="flex items-center gap-5">
                                            <div class="w-14 h-14 bg-slate-900 rounded-2xl flex items-center justify-center text-white font-black text-xl shadow-lg group-hover:bg-blue-600 transition-colors" x-text="material.session_number"></div>
                                            <div>
                                                <p class="font-black text-slate-800 uppercase text-sm tracking-tight mb-1" x-text="material.title"></p>
                                                <div class="flex gap-2">
                                                    <span class="text-[9px] font-bold text-blue-500 bg-blue-50 px-2 py-0.5 rounded uppercase tracking-tighter" x-text="'Sesi Ke-' + material.session_number"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex flex-wrap gap-2 w-full md:w-auto">
                                            <template x-if="material.video_url">
                                                <a :href="material.video_url" target="_blank" class="flex-1 md:flex-none bg-orange-100 text-orange-600 px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest text-center hover:bg-orange-500 hover:text-white transition-all">
                                                    <i class="fas fa-play mr-1"></i> Video
                                                </a>
                                            </template>
                                            <template x-if="material.file_path">
                                                <a :href="'/storage/' + material.file_path" target="_blank" class="flex-1 md:flex-none bg-blue-600 text-white px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest text-center shadow-md shadow-blue-50 hover:bg-slate-900 transition-all">
                                                    <i class="fas fa-file-pdf mr-1"></i> Modul
                                                </a>
                                            </template>
                                            <template x-if="material.quiz_url">
                                                <a :href="material.quiz_url" target="_blank" class="flex-1 md:flex-none bg-emerald-100 text-emerald-600 px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest text-center hover:bg-emerald-500 hover:text-white transition-all border border-emerald-200">
                                                    <i class="fas fa-tasks mr-1"></i> Kuis
                                                </a>
                                            </template>
                                        </div>
                                    </div>

                                    {{-- BLOK REVIEW & NILAI --}}
                                    <template x-if="material.grade">
                                        <div class="mt-8 p-5 bg-indigo-50/50 rounded-2xl border border-indigo-100/50 flex justify-between items-center group/grade hover:bg-indigo-50 transition-all">
                                            <div class="flex items-center gap-4">
                                                <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-indigo-500 shadow-sm border border-indigo-50">
                                                    <i class="fas fa-star text-lg"></i>
                                                </div>
                                                <div>
                                                    <div class="flex items-center gap-2 mb-1">
                                                        <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest leading-none">Feedback Mentor</p>
                                                    </div>
                                                    <p class="text-xs font-bold text-slate-700 italic leading-relaxed" x-text="material.grade.note || 'Selesai dinilai'"></p>
                                                </div>
                                            </div>
                                            <div class="flex flex-col items-end bg-white px-5 py-3 rounded-2xl border border-indigo-50 shadow-sm min-w-[80px]">
                                                <span class="text-2xl font-black text-indigo-600 leading-none mb-0.5" x-text="material.grade.score"></span>
                                                <span class="text-[8px] font-black text-indigo-300 uppercase block tracking-tighter">Score</span>
                                            </div>
                                        </div>
                                    </template>

                                    {{-- PENGUMPULAN TUGAS --}}
                                    <div class="mt-6 pt-6 border-t border-dashed border-slate-100">
                                        <label class="text-[9px] font-black text-slate-400 uppercase mb-3 block tracking-[0.1em]">Pengumpulan Tugas :</label>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <input type="url" :id="'task_link_' + material.id" placeholder="Link G-Drive / Cloud..." class="bg-slate-50 border-none rounded-xl text-xs font-bold focus:ring-2 focus:ring-blue-500 px-4 py-3 placeholder:text-slate-300 shadow-inner">
                                            <div class="relative">
                                                <input type="file" :id="'task_file_' + material.id" accept="application/pdf" class="hidden" @change="fileNameTask = $event.target.files[0].name">
                                                <label :for="'task_file_' + material.id" class="flex items-center justify-between bg-slate-50 rounded-xl px-4 py-3 cursor-pointer hover:bg-slate-100 transition border border-dashed border-slate-200">
                                                    <span class="text-xs font-bold text-slate-500" x-text="fileNameTask || 'Unggah PDF Tugas'"></span>
                                                    <i class="fas fa-cloud-upload-alt text-blue-500"></i>
                                                </label>
                                            </div>
                                        </div>
                                        <button @click="submitTask(material.id)" class="w-full mt-4 bg-slate-900 text-white py-3.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-600 transition shadow-lg active:scale-95">KIRIM TUGAS KE MENTOR</button>
                                    </div>
                                </div>
                            </template>

                            <template x-if="!selectedProgram.materials || selectedProgram.materials.length === 0">
                                <div class="text-center py-20 bg-slate-50 rounded-[3rem] border border-dashed border-slate-200">
                                    <i class="fas fa-layer-group text-slate-200 text-5xl mb-4"></i>
                                    <p class="text-slate-400 font-bold uppercase text-[10px] tracking-widest">Materi sedang disiapkan oleh Mentor</p>
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

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 5px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>
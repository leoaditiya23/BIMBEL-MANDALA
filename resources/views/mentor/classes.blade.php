@extends('mentor.dashboard_mentor_layout')

@section('mentor_content')
<div x-data="{ 
    activeTab: 'semua', 
    modalMateri: false, 
    modalAbsen: false,
    modalTugas: false, {{-- Sekarang menjadi Modal Tugas & Nilai --}}
    selectedSubmissions: [],
    isAbsenOpen: false, 
    selectedClass: {id: '', name: '', students: [], materials: []},
    fileName: '',
    editingMaterial: null,

    toggleAbsen(status) {
        this.isAbsenOpen = status;
        fetch('{{ route('mentor.toggleAbsen') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                class_id: this.selectedClass.id,
                is_active: status
            })
        })
        .then(res => res.json())
        .then(data => {
            console.log('Status updated:', data);
        }).catch(err => console.error(err));
    }
}" x-transition:enter="transition ease-out duration-300">
    
    <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-3xl font-black text-slate-800">Manajemen Kelas</h2>
            <p class="text-sm text-slate-500">Pantau progres kurikulum dan penilaian siswa secara mendalam</p>
        </div>
        <div class="flex bg-slate-100 p-1 rounded-[20px]">
            <button @click="activeTab = 'semua'" :class="activeTab === 'semua' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500'" class="px-6 py-2 rounded-[15px] text-xs font-bold transition-all duration-200">Semua Kelas</button>
            <button @click="activeTab = 'aktif'" :class="activeTab === 'aktif' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500'" class="px-6 py-2 rounded-[15px] text-xs font-bold transition-all duration-200">Sesi Berjalan</button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($classes as $class)
            <div class="bg-white rounded-[20px] border border-slate-100 shadow-sm hover:shadow-xl transition-all duration-300 group overflow-hidden">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="p-3 bg-indigo-50 rounded-[15px] transition-colors duration-300">
                            <i class="fas fa-book-open text-indigo-600"></i>
                        </div>
                        <span class="text-[10px] font-black px-3 py-1 bg-green-100 text-green-600 rounded-full uppercase tracking-wider">Terverifikasi</span>
                    </div>
                    
                    <h3 class="font-black text-slate-800 text-lg leading-tight mb-1">{{ $class->name }}</h3>
                    <p class="text-xs font-bold text-indigo-500 uppercase tracking-wider mb-4">{{ $class->jenjang }} - {{ $class->type }}</p>

                    <div class="grid grid-cols-2 gap-3 mb-6">
                        <div class="bg-slate-50 p-3 rounded-[15px] border border-transparent hover:border-indigo-100 transition-all">
                            <p class="text-[9px] font-black text-slate-400 uppercase">Siswa Aktif</p>
                            <p class="font-black text-slate-700 text-sm">{{ $class->student_count }} Orang</p>
                        </div>
                        <div class="bg-slate-50 p-3 rounded-[15px] border border-transparent hover:border-indigo-100 transition-all">
                            <p class="text-[9px] font-black text-slate-400 uppercase">Materi Sesi</p>
                            <p class="font-black text-slate-700 text-sm">{{ count($class->materials) }} Sesi</p>
                        </div>
                    </div>

                    <div class="mb-6">
                        @php
                            $progress = ($class->total_sessions > 0) ? (count($class->materials) / $class->total_sessions) * 100 : 0;
                        @endphp
                        <div class="flex justify-between text-[10px] font-black mb-1">
                            <span class="text-slate-400 uppercase tracking-widest">Progress Kurikulum</span>
                            <span class="text-indigo-600">{{ round($progress) }}%</span>
                        </div>
                        <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden shadow-inner">
                            <div class="bg-indigo-500 h-full rounded-full shadow-[0_0_10px_rgba(79,70,229,0.4)] transition-all duration-1000" style="width: {{ $progress }}%"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <button @click="selectedClass = {id: '{{ $class->id }}', name: '{{ $class->name }}', students: {{ json_encode($class->students) }}}; modalAbsen = true" 
                                class="py-3 bg-slate-100 text-slate-700 rounded-[15px] text-[9px] font-black uppercase tracking-widest hover:bg-slate-800 hover:text-white transition-all duration-200">
                            <i class="fas fa-list-ol mr-1"></i> Absensi
                        </button>
                        {{-- Tombol Nilai & Tugas Disatukan --}}
                        <button @click="selectedClass = {id: '{{ $class->id }}', name: '{{ $class->name }}', students: {{ json_encode($class->students) }} }; selectedSubmissions = {{ json_encode($class->submissions ?? []) }}; modalTugas = true" 
                                class="py-3 bg-indigo-50 text-indigo-600 rounded-[15px] text-[9px] font-black uppercase tracking-widest hover:bg-indigo-600 hover:text-white transition-all duration-200">
                            <i class="fas fa-tasks mr-1"></i> Tugas & Nilai
                        </button>
                    </div>
                </div>

                <button @click="selectedClass = {id: '{{ $class->id }}', name: '{{ $class->name }}', materials: {{ json_encode($class->materials) }} }; modalMateri = true; fileName = ''" 
                        class="w-full px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-between items-center hover:bg-indigo-50 transition-colors group/btn">
                    <span class="text-[10px] font-black text-slate-500 group-hover/btn:text-indigo-600 transition uppercase tracking-widest">
                        <i class="fas fa-folder-plus mr-1"></i> Kelola Materi Sesi
                    </span>
                    <i class="fas fa-chevron-right text-slate-300 group-hover/btn:text-indigo-600 group-hover/btn:translate-x-1 transition-all"></i>
                </button>
            </div>
        @empty
            <div class="col-span-full bg-white p-12 rounded-[30px] border-2 border-dashed border-slate-200 text-center shadow-inner">
                <img src="https://illustrations.popsy.co/slate/empty-folder.svg" class="w-40 mx-auto mb-4 opacity-50" alt="empty">
                <p class="text-slate-400 font-bold">Belum ada kelas yang ditugaskan kepada Anda.</p>
            </div>
        @endforelse
    </div>

    {{-- MODAL ABSENSI --}}
    <div x-show="modalAbsen" class="fixed inset-0 z-[70] flex items-start justify-center p-4 bg-slate-900/60 backdrop-blur-md overflow-y-auto pt-10" x-cloak>
        <div @click.away="modalAbsen = false" class="bg-white rounded-[35px] shadow-2xl w-full max-w-md overflow-hidden border border-white/20 my-auto">
            <div class="p-8">
                <div class="flex justify-between items-center mb-8 mt-6">
                    <div>
                        <h3 class="text-2xl font-black text-slate-800 leading-tight">Sesi Presensi</h3>
                        <p class="text-sm font-bold text-indigo-500 mt-1" x-text="selectedClass.name"></p>
                    </div>
                    <div :class="isAbsenOpen ? 'bg-rose-50 text-rose-600 animate-pulse' : 'bg-slate-50 text-slate-400'" 
                         class="w-14 h-14 rounded-2xl flex items-center justify-center transition-colors duration-500 shadow-sm">
                        <i class="fas fa-tower-broadcast text-xl"></i>
                    </div>
                </div>

                <div class="bg-slate-50 p-5 rounded-[25px] border border-slate-100 mb-6">
                    <p class="text-[10px] font-black uppercase text-slate-400 mb-3 tracking-widest text-center">Kontrol Sesi Hari Ini</p>
                    <div class="flex gap-2">
                        <button type="button" @click="toggleAbsen(true)" :disabled="isAbsenOpen" :class="isAbsenOpen ? 'bg-slate-200 text-slate-400 cursor-not-allowed' : 'bg-white text-indigo-600 border border-indigo-100 hover:bg-indigo-50'" class="flex-1 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm">Buka Sesi</button>
                        <button type="button" @click="toggleAbsen(false)" :disabled="!isAbsenOpen" :class="!isAbsenOpen ? 'bg-slate-200 text-slate-400 cursor-not-allowed' : 'bg-white text-rose-500 border border-rose-100 hover:bg-rose-50'" class="flex-1 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm">Tutup Sesi</button>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex justify-between items-end px-1">
                        <label class="text-[10px] font-black uppercase text-slate-400">Kehadiran :</label>
                        <span class="text-[10px] font-black text-indigo-600">
                            <span x-text="selectedClass.students ? selectedClass.students.filter(s => s.status === 'Hadir').length : 0"></span> / <span x-text="selectedClass.students ? selectedClass.students.length : 0"></span> Siswa
                        </span>
                    </div>
                    <div class="max-h-52 overflow-y-auto pr-2 space-y-2 custom-scrollbar">
                        <template x-for="student in selectedClass.students" :key="student.id">
                            <div class="flex items-center justify-between p-3 bg-white rounded-xl border border-slate-100 transition-all hover:border-indigo-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center text-[10px] font-black text-slate-400" x-text="student.name.charAt(0)"></div>
                                    <span class="text-xs font-bold text-slate-700" x-text="student.name"></span>
                                </div>
                                <span :class="student.status === 'Hadir' ? 'bg-green-100 text-green-600' : 'bg-slate-50 text-slate-300'" class="text-[9px] font-black px-2 py-1 rounded-lg uppercase tracking-tighter" x-text="student.status || 'Menunggu...'"></span>
                            </div>
                        </template>
                    </div>
                    <button type="button" @click="modalAbsen = false" class="w-full mt-4 py-4 text-[10px] font-black uppercase bg-slate-100 text-slate-500 rounded-[18px]">Kembali</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL MATERI (TETAP) --}}
    <div x-show="modalMateri" class="fixed inset-0 z-[70] flex items-start justify-center p-4 bg-slate-900/60 backdrop-blur-md overflow-y-auto" x-cloak>
        <div @click.away="modalMateri = false; editingMaterial = null" 
             class="bg-white rounded-[35px] shadow-2xl w-full max-w-5xl overflow-hidden border border-white/20 flex flex-col md:flex-row min-h-[500px] my-10 relative">
            
            <div class="w-full md:w-1/2 bg-slate-50 border-r border-slate-100 flex flex-col h-full">
                <div class="p-8 border-b border-slate-200 bg-white pt-20">
                    <h3 class="text-xl font-black text-slate-800 tracking-tight">Riwayat Sesi</h3>
                    <p class="text-[10px] font-bold text-indigo-500 uppercase">Klik ikon edit untuk mengubah data</p>
                </div>
                
                <div class="flex-1 overflow-y-auto p-6 space-y-3 custom-scrollbar">
                    <template x-if="selectedClass.materials && selectedClass.materials.length > 0">
                        <template x-for="material in selectedClass.materials" :key="material.id">
                            <button @click="editingMaterial = material" 
                                    class="w-full text-left bg-white p-4 rounded-[20px] border border-slate-200 flex items-center justify-between group hover:border-indigo-500 transition-all">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400 font-black text-xs group-hover:bg-indigo-600 group-hover:text-white transition-colors" x-text="material.session_number"></div>
                                    <h4 class="font-bold text-slate-800 text-sm" x-text="material.title"></h4>
                                </div>
                                <i class="fas fa-edit text-slate-300 group-hover:text-indigo-500 transition-colors text-xs"></i>
                            </button>
                        </template>
                    </template>
                </div>
            </div>

            <div class="w-full md:w-1/2 p-8 bg-white flex flex-col relative">
                <div class="absolute top-6 right-8 flex gap-2">
                    <button x-show="editingMaterial" @click="editingMaterial = null; fileName = ''" class="w-10 h-10 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center hover:bg-amber-100 transition-colors">
                        <i class="fas fa-plus"></i>
                    </button>
                    <button @click="modalMateri = false; editingMaterial = null" class="w-10 h-10 bg-slate-50 text-slate-400 rounded-xl flex items-center justify-center hover:bg-slate-100 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="pt-10 mb-8">
                    <h3 class="text-2xl font-black text-slate-800 leading-tight" x-text="editingMaterial ? 'Edit Sesi' : 'Tambah Sesi'"></h3>
                    <p class="text-sm font-bold text-indigo-500" x-text="selectedClass.name"></p>
                </div>
                
                <form :action="editingMaterial ? `{{ url('mentor/materials/update') }}/${editingMaterial.id}` : '{{ route('mentor.storeMaterial') }}'" 
                      method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    <template x-if="editingMaterial"><input type="hidden" name="_method" value="PUT"></template>
                    <input type="hidden" name="program_id" :value="selectedClass.id">
                    
                    <div class="grid grid-cols-4 gap-4">
                        <div class="col-span-1">
                            <label class="text-[10px] font-black uppercase text-slate-400 ml-1">Sesi</label>
                            <input type="number" name="session_number" :value="editingMaterial ? editingMaterial.session_number : (selectedClass.materials ? selectedClass.materials.length + 1 : 1)" class="w-full mt-2 bg-slate-50 border-none rounded-[15px] text-sm font-bold">
                        </div>
                        <div class="col-span-3">
                            <label class="text-[10px] font-black uppercase text-slate-400 ml-1">Judul Pembahasan</label>
                            <input type="text" name="title" :value="editingMaterial ? editingMaterial.title : ''" placeholder="Judul" class="w-full mt-2 bg-slate-50 border-none rounded-[15px] text-sm font-bold">
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400 ml-1">Link Video / Modul</label>
                        <input type="url" name="video_url" :value="editingMaterial ? editingMaterial.video_url : ''" placeholder="https://..." class="w-full mt-2 bg-slate-50 border-none rounded-[15px] text-sm font-bold">
                        <div class="mt-4 p-6 border-2 border-dashed border-slate-100 rounded-[20px] text-center">
                            <input type="file" name="file" class="hidden" id="fileInp" @change="fileName = $event.target.files[0].name">
                            <label for="fileInp" class="cursor-pointer text-[10px] font-black text-slate-400 uppercase tracking-widest" x-text="fileName || 'Klik untuk Upload PDF'"></label>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-4 bg-indigo-600 text-white rounded-[18px] text-[10px] font-black uppercase">Simpan Sesi</button>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL ALL-IN-ONE: TUGAS & PENILAIAN --}}
    <div x-show="modalTugas" class="fixed inset-0 z-[70] flex items-start justify-center p-4 bg-slate-900/60 backdrop-blur-md overflow-y-auto pt-10" x-cloak>
        <div @click.away="modalTugas = false" class="bg-white rounded-[40px] shadow-2xl w-full max-w-4xl overflow-hidden my-auto border border-white/20">
            <div class="flex flex-col md:flex-row h-full min-h-[550px]">
                
                {{-- Sisi Kiri: List Submisi --}}
                <div class="w-full md:w-3/5 p-8 border-r border-slate-100">
                    <div class="flex justify-between items-center mb-8">
                        <div>
                            <h3 class="text-2xl font-black text-slate-800 leading-tight">Submisi Siswa</h3>
                            <p class="text-sm font-bold text-indigo-500 mt-1" x-text="selectedClass.name"></p>
                        </div>
                        <div class="bg-indigo-50 text-indigo-600 px-4 py-2 rounded-xl text-[10px] font-black uppercase" x-text="selectedSubmissions.length + ' Submisi'"></div>
                    </div>

                    <div class="space-y-3 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                        <template x-for="item in selectedSubmissions" :key="item.id">
                            <div class="p-4 bg-slate-50 rounded-[22px] border border-transparent hover:border-indigo-100 hover:bg-white transition-all group flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-white shadow-sm rounded-xl flex items-center justify-center font-black text-xs text-indigo-600" x-text="item.student_name.charAt(0)"></div>
                                    <div class="flex flex-col">
                                        <span class="text-xs font-black text-slate-700" x-text="item.student_name"></span>
                                        <span class="text-[9px] font-bold text-slate-400 uppercase" x-text="item.assignment_title"></span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a :href="item.file_url" target="_blank" class="w-9 h-9 bg-indigo-600 text-white rounded-xl flex items-center justify-center hover:bg-indigo-700 transition-all">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    <span :class="item.is_graded ? 'bg-green-100 text-green-600' : 'bg-amber-100 text-amber-600'" 
                                          class="text-[8px] font-black px-3 py-2 rounded-lg uppercase"
                                          x-text="item.is_graded ? 'Tersimpan' : 'Pending'"></span>
                                </div>
                            </div>
                        </template>
                        <template x-if="selectedSubmissions.length === 0">
                            <div class="text-center py-20">
                                <i class="fas fa-inbox text-4xl text-slate-200 mb-4"></i>
                                <p class="text-slate-400 font-bold text-sm">Belum ada tugas masuk</p>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Sisi Kanan: Form Input Nilai --}}
                <div class="w-full md:w-2/5 p-8 bg-slate-50/50">
                    <div class="mb-8">
                        <h4 class="text-sm font-black text-slate-800 uppercase tracking-widest">Panel Penilaian</h4>
                        <p class="text-[10px] text-slate-400 font-bold">Input skor dan feedback di sini</p>
                    </div>

                    <form action="{{ route('mentor.storeGrade') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="program_id" :value="selectedClass.id">
                        
                        <div>
                            <label class="text-[10px] font-black uppercase text-slate-400 ml-1">Pilih Nama Siswa</label>
                            <select name="student_id" required class="w-full mt-1 bg-white border-none rounded-[15px] text-xs font-bold shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all">
                                <option value="">-- Pilih Siswa --</option>
                                <template x-for="student in selectedClass.students" :key="student.id">
                                    <option :value="student.id" x-text="student.name"></option>
                                </template>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-[10px] font-black uppercase text-slate-400 ml-1">Kategori</label>
                                <input type="text" name="title" placeholder="Tugas 1" required class="w-full mt-1 bg-white border-none rounded-[15px] text-xs font-bold shadow-sm">
                            </div>
                            <div>
                                <label class="text-[10px] font-black uppercase text-slate-400 ml-1">Skor</label>
                                <input type="number" name="score" max="100" placeholder="0-100" required class="w-full mt-1 bg-white border-none rounded-[15px] text-xs font-bold shadow-sm">
                            </div>
                        </div>

                        <div>
                            <label class="text-[10px] font-black uppercase text-slate-400 ml-1">Catatan Mentor</label>
                            <textarea name="note" rows="4" placeholder="Feedback untuk siswa..." class="w-full mt-1 bg-white border-none rounded-[15px] text-xs font-bold shadow-sm"></textarea>
                        </div>

                        <div class="pt-4 space-y-3">
                            <button type="submit" class="w-full py-4 bg-indigo-600 text-white rounded-[20px] text-[10px] font-black uppercase tracking-widest shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all">Submit Penilaian</button>
                            <button type="button" @click="modalTugas = false" class="w-full py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-600 transition-all">Tutup Halaman</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
3<style>
    body { background-color: #fcfcfd; }
    [x-cloak] { display: none !important; }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>
@endsection
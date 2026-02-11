@extends('mentor.dashboard_mentor_layout')

@section('mentor_content')
<div x-data="{ 
    activeTab: 'semua', 
    modalMateri: false, 
    modalNilai: false,
    modalAbsen: false,
    selectedClass: {id: '', name: '', students: []} 
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
                        <div class="p-3 bg-indigo-50 rounded-[15px] group-hover:bg-indigo-600 transition-colors duration-300">
                            <i class="fas fa-book-open text-indigo-600 group-hover:text-white"></i>
                        </div>
                        <span class="text-[10px] font-black px-3 py-1 bg-green-100 text-green-600 rounded-full uppercase italic tracking-wider">Terverifikasi</span>
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

                    <div class="grid grid-cols-2 gap-3">
                        <button @click="selectedClass = {id: '{{ $class->id }}', name: '{{ $class->name }}', students: {{ json_encode($class->students) }}}; modalAbsen = true" 
                                class="py-3 bg-slate-100 text-slate-700 rounded-[15px] text-[10px] font-black uppercase tracking-widest hover:bg-slate-800 hover:text-white transition-all duration-200">
                            <i class="fas fa-list-ol mr-1"></i> Absensi
                        </button>
                        <button @click="selectedClass = {id: '{{ $class->id }}', name: '{{ $class->name }}', students: {{ json_encode($class->students) }}}; modalNilai = true" 
                                class="py-3 bg-indigo-600 text-white rounded-[15px] text-[10px] font-black uppercase tracking-widest hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-200 transition-all duration-200">
                            <i class="fas fa-star mr-1"></i> Nilai
                        </button>
                    </div>
                </div>

                <button @click="selectedClass = {id: '{{ $class->id }}', name: '{{ $class->name }}'}; modalMateri = true" 
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
    <div x-show="modalAbsen" class="fixed inset-0 z-[70] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md" x-cloak>
        <div @click.away="modalAbsen = false" class="bg-white rounded-[35px] shadow-2xl w-full max-w-md overflow-hidden border border-white/20">
            <div class="p-8">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-2xl font-black text-slate-800">Presensi Harian</h3>
                        <p class="text-sm font-bold text-indigo-500" x-text="selectedClass.name"></p>
                    </div>
                    <div class="w-12 h-12 bg-green-50 rounded-2xl flex items-center justify-center text-green-600">
                        <i class="fas fa-calendar-check text-xl"></i>
                    </div>
                </div>
                
                <form action="{{ route('mentor.storeAttendance') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="program_id" :value="selectedClass.id">
                    <input type="hidden" name="date" value="{{ date('Y-m-d') }}">
                    
                    <div class="max-h-60 overflow-y-auto pr-2 space-y-3 custom-scrollbar">
                        <template x-for="student in selectedClass.students" :key="student.id">
                            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                <span class="text-xs font-black text-slate-700" x-text="student.name"></span>
                                <select :name="'attendance['+student.id+']'" class="text-[10px] font-black bg-white border-none rounded-lg shadow-sm focus:ring-indigo-500">
                                    <option value="hadir">HADIR</option>
                                    <option value="izin">IZIN</option>
                                    <option value="alfa">ALFA</option>
                                </select>
                            </div>
                        </template>
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button type="button" @click="modalAbsen = false" class="flex-1 py-4 text-[10px] font-black uppercase tracking-widest bg-slate-100 text-slate-500 rounded-[18px] hover:bg-slate-200 transition">Batal</button>
                        <button type="submit" class="flex-1 py-4 text-[10px] font-black uppercase tracking-widest bg-indigo-600 text-white rounded-[18px] shadow-xl shadow-indigo-200 hover:bg-indigo-700 transition">Simpan Absen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL MATERI SESI (ROADMAP KURIKULUM) --}}
    <div x-show="modalMateri" class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md" x-cloak>
        <div @click.away="modalMateri = false" class="bg-white rounded-[35px] shadow-2xl w-full max-w-lg overflow-hidden border border-white/20">
            <div class="p-8">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-2xl font-black text-slate-800">Kelola Sesi Belajar</h3>
                        <p class="text-sm font-bold text-indigo-500" x-text="selectedClass.name"></p>
                    </div>
                    <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600">
                        <i class="fas fa-layer-group text-xl"></i>
                    </div>
                </div>
                
                <form action="{{ route('mentor.storeMaterial') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    <input type="hidden" name="program_id" :value="selectedClass.id">
                    
                    <div class="grid grid-cols-4 gap-4">
                        <div class="col-span-1">
                            <label class="text-[10px] font-black uppercase text-slate-400 ml-1">Pertemuan</label>
                            <input type="number" name="session_number" placeholder="1" required class="w-full mt-1 bg-slate-50 border-2 border-slate-50 rounded-[15px] text-sm font-bold focus:ring-0 focus:border-indigo-500 transition-all">
                        </div>
                        <div class="col-span-3">
                            <label class="text-[10px] font-black uppercase text-slate-400 ml-1">Topik Utama Sesi</label>
                            <input type="text" name="title" placeholder="Misal: Pengenalan Dasar UI/UX" required class="w-full mt-1 bg-slate-50 border-2 border-slate-50 rounded-[15px] text-sm font-bold focus:ring-0 focus:border-indigo-500 transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400 ml-1">Video Tutorial (Youtube)</label>
                        <input type="url" name="video_url" placeholder="https://..." class="w-full mt-1 bg-slate-50 border-2 border-slate-50 rounded-[15px] text-sm font-bold focus:ring-0 focus:border-indigo-500 transition-all">
                    </div>

                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400 ml-1">Modul / Tugas PDF</label>
                        <label class="flex flex-col items-center justify-center w-full h-24 mt-1 border-2 border-dashed border-slate-200 rounded-[20px] cursor-pointer bg-slate-50 hover:bg-indigo-50 hover:border-indigo-300 transition-all group">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <i class="fas fa-cloud-upload-alt text-slate-300 group-hover:text-indigo-500 mb-1"></i>
                                <p class="text-[10px] text-slate-400 font-bold group-hover:text-indigo-500">Upload PDF/PPT Materi</p>
                            </div>
                            <input type="file" name="file" class="hidden" />
                        </label>
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button type="button" @click="modalMateri = false" class="flex-1 py-4 text-[10px] font-black uppercase tracking-widest bg-slate-100 text-slate-500 rounded-[18px] hover:bg-slate-200 transition">Batal</button>
                        <button type="submit" class="flex-1 py-4 text-[10px] font-black uppercase tracking-widest bg-indigo-600 text-white rounded-[18px] shadow-xl shadow-indigo-200 hover:bg-indigo-700 transition">Tambah Sesi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL NILAI --}}
    <div x-show="modalNilai" class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md" x-cloak>
        <div @click.away="modalNilai = false" class="bg-white rounded-[35px] shadow-2xl w-full max-w-md overflow-hidden">
            <div class="p-8">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-2xl font-black text-slate-800">Evaluasi Siswa</h3>
                        <p class="text-sm font-bold text-indigo-500" x-text="selectedClass.name"></p>
                    </div>
                    <div class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-500">
                        <i class="fas fa-award text-xl"></i>
                    </div>
                </div>
                
                <form action="{{ route('mentor.storeGrade') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="program_id" :value="selectedClass.id">
                    
                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400 ml-1">Pilih Siswa</label>
                        <select name="student_id" required class="w-full mt-1 bg-slate-50 border-2 border-slate-50 rounded-[15px] text-sm font-bold focus:ring-0 focus:border-indigo-500 transition-all">
                            <template x-for="student in selectedClass.students" :key="student.id">
                                <option :value="student.id" x-text="student.name"></option>
                            </template>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-black uppercase text-slate-400 ml-1">Kategori Nilai</label>
                            <input type="text" name="title" placeholder="Misal: Proyek Akhir" required class="w-full mt-1 bg-slate-50 border-2 border-slate-50 rounded-[15px] text-sm font-bold focus:ring-0 focus:border-indigo-500 transition-all">
                        </div>
                        <div>
                            <label class="text-[10px] font-black uppercase text-slate-400 ml-1">Skor (0-100)</label>
                            <input type="number" name="score" max="100" placeholder="100" required class="w-full mt-1 bg-slate-50 border-2 border-slate-50 rounded-[15px] text-sm font-bold focus:ring-0 focus:border-indigo-500 transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400 ml-1">Catatan Progress (Feedback)</label>
                        <textarea name="note" rows="3" placeholder="Sangat bagus dalam pemahaman logika..." class="w-full mt-1 bg-slate-50 border-2 border-slate-50 rounded-[15px] text-sm font-bold focus:ring-0 focus:border-indigo-500 transition-all"></textarea>
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button type="button" @click="modalNilai = false" class="flex-1 py-4 text-[10px] font-black uppercase tracking-widest bg-slate-100 text-slate-500 rounded-[18px] hover:bg-slate-200 transition">Batal</button>
                        <button type="submit" class="flex-1 py-4 text-[10px] font-black uppercase tracking-widest bg-indigo-600 text-white rounded-[18px] shadow-xl shadow-indigo-200 hover:bg-indigo-700 transition">Kirim Nilai</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    body { background-color: #fcfcfd; }
    [x-cloak] { display: none !important; }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>
@endsection
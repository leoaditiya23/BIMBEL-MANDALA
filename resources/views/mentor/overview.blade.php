@extends('mentor.dashboard_mentor_layout')

@section('mentor_content')
<div x-data="{ openModal: false }" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4">
    
    <div class="mb-8 flex justify-between items-end">
        <div>
            <h2 class="text-3xl font-black text-slate-800 tracking-tighter">Halo, {{ explode(' ', Auth::user()->name)[0] }}! 👋</h2>
            <p class="text-sm text-slate-500 mt-1 font-medium">Aktivitas mengajar Anda terpantau secara real-time</p>
        </div>
        <div class="hidden md:block">
            <a href="{{ route('mentor.schedule') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-black transition">
                <i class="fas fa-calendar-alt mr-2"></i> LIHAT SEMUA JADWAL
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-lg transition-all duration-300">
            <p class="text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest italic">Siswa Terbimbing</p>
            <p class="text-3xl font-black text-indigo-600">{{ $stats['total_siswa'] ?? 0 }}</p>
        </div>
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-lg transition-all duration-300">
            <p class="text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest italic">Sesi Hari Ini</p>
            <p class="text-3xl font-black text-green-600">{{ count($today_schedule ?? []) }}</p>
        </div>
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-lg transition-all duration-300">
            <p class="text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest italic">Total Kelas Diampu</p>
            <div class="flex items-center justify-between">
                <p class="text-3xl font-black text-orange-500">{{ $stats['total_kelas'] ?? 0 }}</p>
                <a href="{{ route('mentor.classes') }}" class="text-[9px] font-black bg-orange-50 text-orange-600 px-3 py-1 rounded-full hover:bg-orange-100 transition">
                    DETAIL
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- BAGIAN 1: JADWAL --}}
        <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100">
            <h3 class="font-black text-slate-800 mb-6 uppercase flex items-center">
                <i class="fas fa-clock mr-3 text-indigo-500 animate-pulse"></i> Jadwal Mengajar Hari Ini
            </h3>
            
            <div class="space-y-4">
                @forelse($today_schedule as $jadwal)
                    @php
                        $jam_jadwal = $jadwal->jam_tampil;
                        $is_now = false;
                        
                        if ($jam_jadwal && $jam_jadwal !== 'SESUAI JADWAL' && $jam_jadwal !== 'CEK JADWAL') {
                            try {
                                $now = date('H:i');
                                $start = $jam_jadwal;
                                $end = \Carbon\Carbon::parse($jam_jadwal)->addHour()->format('H:i');
                                $is_now = ($now >= $start && $now <= $end);
                            } catch (\Exception $e) { $is_now = false; }
                        }
                    @endphp
                    <div class="group relative p-6 {{ $is_now ? 'bg-indigo-50 border-indigo-200 shadow-indigo-100/50 shadow-lg' : 'bg-white border-slate-100' }} border-2 rounded-[1.8rem] transition-all hover:shadow-md">
                        @if($is_now)
                            <span class="absolute -left-1 top-1/2 -translate-y-1/2 w-2 h-12 bg-indigo-500 rounded-full"></span>
                        @endif
                        
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h4 class="font-black text-slate-800 text-base uppercase tracking-tight">{{ $jadwal->program_name }}</h4>
                                <div class="flex items-center gap-2 mt-1">
                                    <p class="text-[10px] text-slate-500 font-bold uppercase italic tracking-tighter">Siswa: {{ $jadwal->student_name }}</p>
                                    {{-- REVISI: Menampilkan label Kelas --}}
                                    <span class="px-2 py-0.5 bg-blue-50 text-blue-600 text-[8px] font-black rounded uppercase border border-blue-100">
                                        KELAS: {{ $jadwal->kelas ?? '-' }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-right flex flex-col items-end">
                                <span class="text-xs font-black {{ $is_now ? 'text-indigo-600 bg-white' : 'text-slate-500 bg-slate-50' }} px-3 py-1 rounded-full shadow-sm border border-slate-100">
                                    {{ $jam_jadwal }}
                                </span>
                                @if($is_now)
                                    <span class="text-[7px] font-black text-indigo-400 uppercase tracking-widest mt-1 animate-pulse">● Sesi Sedang Berlangsung</span>
                                @endif
                            </div>
                        </div>

                        <div class="mt-2 pt-4 border-t border-slate-100/80 flex flex-col gap-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 text-rose-500">
                                    <i class="fas fa-map-marker-alt text-[10px]"></i>
                                    <span class="text-[9px] font-black text-slate-600 uppercase tracking-widest">{{ $jadwal->lokasi_cabang ?? 'ONLINE' }}</span>
                                </div>
                                @if(isset($jadwal->alamat_siswa) && $jadwal->alamat_siswa && $jadwal->alamat_siswa !== '-')
                                    <a href="https://maps.google.com/?q={{ urlencode($jadwal->alamat_siswa) }}" target="_blank" class="text-[8px] font-black bg-white text-blue-600 px-3 py-1.5 rounded-lg border border-blue-100 hover:bg-blue-600 hover:text-white transition shadow-sm">
                                        BUKA MAPS <i class="fas fa-external-link-alt ml-1"></i>
                                    </a>
                                @endif
                            </div>
                            <p class="text-[10px] text-slate-400 font-medium italic leading-relaxed pr-4">
                                {{ ($jadwal->alamat_siswa && $jadwal->alamat_siswa !== '-') ? $jadwal->alamat_siswa : 'Koordinasi lokasi via WhatsApp Group.' }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16 bg-slate-50 rounded-[2rem] border-2 border-dashed border-slate-100">
                        <i class="fas fa-calendar-day text-slate-200 text-4xl mb-3"></i>
                        <p class="text-slate-400 text-xs font-bold uppercase tracking-widest italic">Santai sejenak, tidak ada jadwal untuk hari ini.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- BAGIAN 2: RIWAYAT TUGAS --}}
        <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-black text-slate-800 uppercase flex items-center text-sm tracking-tight">
                    <i class="fas fa-history mr-3 text-orange-500"></i> Riwayat Tugas Terbaru
                </h3>
                <button @click="openModal = true" style="background-color: #4f46e5 !important; color: white !important;" class="text-[10px] font-black px-5 py-2.5 rounded-full uppercase shadow-lg shadow-indigo-100 transition hover:scale-105 active:scale-95">
                    + Tugas Baru
                </button>
            </div>
            
            <div class="space-y-4 max-h-[450px] overflow-y-auto pr-2 custom-scrollbar">
                @forelse($assignments as $assignment)
                    <div class="p-5 border-l-4 border-slate-100 rounded-r-2xl bg-white hover:bg-slate-50 border border-slate-100 transition group">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[9px] font-black text-indigo-500 uppercase tracking-widest">Target: {{ $assignment->student_name ?? 'Umum' }}</span>
                                <h4 class="font-black text-slate-800 text-sm group-hover:text-indigo-600 transition-colors">{{ $assignment->title }}</h4>
                            </div>
                            <span class="text-[9px] font-bold text-slate-400 bg-slate-50 px-2 py-0.5 rounded">{{ $assignment->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="mt-4 flex gap-2">
                            @if($assignment->link)
                                <a href="{{ $assignment->link }}" target="_blank" class="text-[8px] font-black text-blue-600 bg-blue-50 px-3 py-1.5 rounded-lg border border-blue-100 hover:bg-blue-600 hover:text-white transition uppercase text-decoration-none">LIHAT LINK</a>
                            @endif
                            @if($assignment->file_path)
                                <a href="{{ asset('storage/' . $assignment->file_path) }}" target="_blank" class="text-[8px] font-black text-rose-600 bg-rose-50 px-3 py-1.5 rounded-lg border border-rose-100 hover:bg-rose-600 hover:text-white transition uppercase text-decoration-none">FILE PDF</a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-20 border-2 border-dashed border-slate-50 rounded-[2rem]">
                        <i class="fas fa-tasks text-slate-100 text-5xl mb-4"></i>
                        <p class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] italic">Belum ada riwayat penugasan.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- MODAL TAMBAH TUGAS --}}
    <div x-show="openModal" class="fixed inset-0 z-[999] overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="openModal = false"></div>
            
            <div class="bg-white rounded-[3rem] overflow-hidden shadow-2xl transform transition-all max-w-lg w-full p-8 z-10 border border-slate-100">
                <h3 class="text-2xl font-black text-slate-800 text-center mb-6 uppercase tracking-tighter">Tambah Penugasan</h3>
                
                <form action="{{ route('mentor.assignments.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-2 italic tracking-widest">Pilih Siswa Tujuan</label>
                        <select name="student_id" required 
                                style="color: #1e293b !important; background-color: #f8fafc !important; border: 1px solid #e2e8f0 !important;"
                                class="w-full px-6 py-4 rounded-2xl font-bold text-slate-800 focus:ring-2 focus:ring-indigo-500 appearance-none">
                            <option value="">-- Pilih Siswa --</option>
                            @php
                                $all_students = DB::table('enrollments')
                                    ->join('users', 'enrollments.user_id', '=', 'users.id')
                                    ->join('programs', 'enrollments.program_id', '=', 'programs.id')
                                    ->where(function($q) {
                                        $q->where('enrollments.mentor_id', Auth::id())
                                          ->orWhere('programs.mentor_id', Auth::id());
                                    })
                                    ->where('enrollments.status_pembayaran', 'verified')
                                    ->select('users.id as student_id', 'users.name as student_name', 'programs.name as program_name')
                                    ->distinct()
                                    ->get();
                            @endphp
                            @foreach($all_students as $mhs)
                                <option value="{{ $mhs->student_id }}">{{ $mhs->student_name }} ({{ $mhs->program_name }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-2 italic tracking-widest">Judul Tugas</label>
                        <input type="text" name="title" required placeholder="Contoh: Latihan Soal Bab 1" 
                               style="color: #1e293b !important; background-color: #f8fafc !important; border: 1px solid #e2e8f0 !important;"
                               class="w-full px-6 py-4 rounded-2xl font-bold text-slate-800 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-2 italic tracking-widest">Link Materi (Opsional)</label>
                        <input type="url" name="link" placeholder="https://cloud-storage.com/..." 
                               style="color: #1e293b !important; background-color: #f8fafc !important; border: 1px solid #e2e8f0 !important;"
                               class="w-full px-6 py-4 rounded-2xl font-bold text-slate-800 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-2 italic tracking-widest">Upload PDF (Opsional)</label>
                        <div class="relative">
                            <input type="file" name="file" accept="application/pdf" 
                                    class="w-full px-6 py-4 bg-slate-50 rounded-2xl font-bold text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 cursor-pointer">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-2 italic tracking-widest">Instruksi Tambahan</label>
                        <textarea name="description" rows="3" placeholder="Jelaskan detail pengerjaan tugas di sini..." 
                                  style="color: #1e293b !important; background-color: #f8fafc !important; border: 1px solid #e2e8f0 !important;"
                                  class="w-full px-6 py-4 rounded-2xl font-bold text-slate-800 focus:ring-2 focus:ring-indigo-500"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-4">
                        <button type="button" @click="openModal = false" class="py-4 bg-slate-100 text-slate-600 rounded-2xl font-black text-xs uppercase transition hover:bg-slate-200">Batal</button>
                        <button type="submit" style="background-color: #4f46e5 !important; color: white !important;" class="py-4 rounded-2xl font-black text-xs uppercase shadow-xl hover:opacity-90 transition">Kirim Tugas</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    [x-cloak] { display: none !important; }
</style>
@endsection
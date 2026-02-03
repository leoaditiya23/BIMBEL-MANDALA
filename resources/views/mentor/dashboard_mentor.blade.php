@extends('layouts.app')
@section('title', 'Mentor Panel - Mandala Bimbel')

@section('content')
<div class="min-h-screen bg-slate-50 flex" x-data="{ 
    menu: 'overview', 
    sidebarOpen: true,
    selectedClass: null, {{-- Menyimpan data kelas yang sedang dibuka --}}
    classTab: 'timeline', {{-- Tab aktif di dalam kelas: timeline, students, materials, quiz, forum --}}
    showMateriModal: false
}">
    
    <aside 
        :class="sidebarOpen ? 'w-72' : 'w-20'" 
        class="bg-blue-700 text-white flex flex-col shadow-2xl transition-all duration-300 relative z-50">
        
        <button 
            @click="sidebarOpen = !sidebarOpen" 
            class="absolute -right-4 top-10 bg-orange-500 text-white w-8 h-8 rounded-full flex items-center justify-center shadow-[0_0_15px_rgba(249,115,22,0.5)] hover:bg-orange-600 transition-all z-[60] border-2 border-white focus:outline-none">
            <i class="fas text-xs transition-transform duration-300" :class="sidebarOpen ? 'fa-chevron-left' : 'fa-chevron-right'"></i>
        </button>

        <div class="p-6 flex items-center h-20 border-b border-blue-600/50 flex-shrink-0">
            <div class="w-10 h-10 bg-white rounded-xl flex-shrink-0 flex items-center justify-center shadow-lg">
                <i class="fas fa-graduation-cap text-blue-700 text-xl"></i>
            </div>
            <div x-show="sidebarOpen" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 tracking-widest" x-transition:enter-end="opacity-100 tracking-normal" class="ml-4 whitespace-nowrap">
                <p class="font-black leading-none text-lg tracking-tight text-white uppercase">Mandala</p>
                <p class="text-[9px] text-blue-100 font-bold uppercase tracking-[0.2em] mt-1 opacity-70">Mentor Zone</p>
            </div>
        </div>

        <nav class="flex-grow p-4 space-y-2 overflow-y-auto overflow-x-hidden custom-scrollbar">
            <p x-show="sidebarOpen" class="px-3 text-[10px] font-black text-blue-200 uppercase tracking-[0.2em] mb-4 opacity-40">Main Menu</p>
            
            <button @click="menu = 'overview'; selectedClass = null" 
               :class="menu === 'overview' ? 'bg-white text-blue-700 shadow-xl' : 'hover:bg-blue-600 text-white'" 
               class="w-full flex items-center p-3.5 rounded-2xl transition-all duration-200 group">
                <div class="w-8 flex justify-center items-center">
                    <i class="fas fa-grid-2 text-lg"></i>
                </div>
                <span x-show="sidebarOpen" class="ml-3 font-bold text-sm">Dashboard</span>
            </button>

            <button @click="menu = 'my_classes'; selectedClass = null" 
               :class="menu === 'my_classes' ? 'bg-white text-blue-700 shadow-xl' : 'hover:bg-blue-600 text-white'" 
               class="w-full flex items-center p-3.5 rounded-2xl transition-all duration-200 group">
                <div class="w-8 flex justify-center items-center">
                    <i class="fas fa-chalkboard text-lg"></i>
                </div>
                <span x-show="sidebarOpen" class="ml-3 font-bold text-sm">Kelas Saya</span>
            </button>

            <button @click="menu = 'schedule'; selectedClass = null" 
               :class="menu === 'schedule' ? 'bg-white text-blue-700 shadow-xl' : 'hover:bg-blue-600 text-white'" 
               class="w-full flex items-center p-3.5 rounded-2xl transition-all duration-200 group">
                <div class="w-8 flex justify-center items-center">
                    <i class="fas fa-calendar-alt text-lg"></i>
                </div>
                <span x-show="sidebarOpen" class="ml-3 font-bold text-sm">Jadwal Mengajar</span>
            </button>
        </nav>

        <div class="p-4 border-t border-blue-600/50 flex-shrink-0">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="w-full flex items-center p-3.5 rounded-2xl text-blue-200 hover:bg-red-500 hover:text-white transition-all group">
                    <div class="w-8 flex justify-center items-center">
                        <i class="fas fa-power-off text-lg group-hover:rotate-90 transition-transform"></i>
                    </div>
                    <span x-show="sidebarOpen" class="ml-3 font-bold text-sm">Keluar Sesi</span>
                </button>
            </form>
        </div>
    </aside>

    <div class="flex-grow flex flex-col min-w-0 h-screen overflow-hidden">
        
        <header class="h-20 bg-white border-b border-slate-200 flex justify-between items-center px-8 flex-shrink-0 z-40 shadow-sm">
            <div class="flex flex-col">
                <h2 class="text-[10px] font-black text-blue-700 uppercase tracking-[0.2em] mb-1">Mandala Portal</h2>
                <h1 class="text-slate-900 font-extrabold text-xl tracking-tight">
                    <template x-if="menu === 'overview'">Dashboard Mentor</template>
                    <template x-if="menu === 'my_classes'">Kelas Saya</template>
                    <template x-if="menu === 'schedule'">Jadwal Mengajar</template>
                </h1>
            </div>

            <div class="flex items-center space-x-6">
                <div class="hidden md:flex flex-col text-right border-r border-slate-200 pr-6">
                    <p class="text-sm font-black text-slate-800 leading-none">{{ Auth::user()->name ?? 'Mentor' }}</p>
                    <p class="text-[10px] text-orange-500 font-bold uppercase mt-1">Professional Mentor</p>
                </div>
                <div class="relative">
                    <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'Mentor' }}&background=1d60af&color=fff&bold=true" 
                         class="w-11 h-11 rounded-2xl shadow-lg border-2 border-white" />
                    <span class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-white rounded-full shadow-sm"></span>
                </div>
            </div>
        </header>

        <main class="flex-grow overflow-y-auto bg-slate-100 p-8 custom-scrollbar">
            
            <div x-show="menu === 'overview' && !selectedClass" x-transition>
                <div class="mb-8">
                    <h2 class="text-2xl font-black text-slate-800 italic uppercase tracking-tighter">Halo, Mentor {{ explode(' ', Auth::user()->name)[0] }}! 👋</h2>
                    <p class="text-sm text-slate-400 font-bold italic uppercase tracking-widest">Berikut adalah jadwal dan progres mengajar Anda.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
                        <p class="text-[10px] font-black text-slate-400 uppercase mb-1 tracking-widest italic">Siswa Aktif</p>
                        <p class="text-3xl font-black text-slate-800">24 <span class="text-sm text-blue-700 uppercase italic">Siswa</span></p>
                    </div>
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
                        <p class="text-[10px] font-black text-slate-400 uppercase mb-1 tracking-widest italic">Sesi Hari Ini</p>
                        <p class="text-3xl font-black text-slate-800">4 <span class="text-sm text-orange-500 uppercase italic">Jadwal</span></p>
                    </div>
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
                        <p class="text-[10px] font-black text-slate-400 uppercase mb-1 tracking-widest italic">Kelas Diajar</p>
                        <p class="text-3xl font-black text-slate-800">3 <span class="text-sm text-green-500 uppercase italic">Kelas</span></p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="bg-white p-8 rounded-[3rem] shadow-sm border border-slate-100">
                        <h3 class="font-black text-slate-800 mb-6 uppercase italic tracking-tighter flex items-center">
                            <i class="fas fa-calendar-day mr-3 text-blue-700"></i> Jadwal Mengajar Hari Ini
                        </h3>
                        <div class="space-y-4">
                            @foreach([['time' => '14:00', 'class' => 'Matematika - SMA 12', 'type' => 'Online'], ['time' => '16:00', 'class' => 'Fisika - Intensif UTBK', 'type' => 'Offline']] as $jadwal)
                            <div class="flex items-center justify-between p-5 bg-slate-50 rounded-3xl border border-transparent hover:border-blue-200 transition">
                                <div class="flex items-center space-x-4">
                                    <div class="bg-white px-3 py-2 rounded-xl shadow-sm text-center">
                                        <p class="text-xs font-black text-blue-700">{{ $jadwal['time'] }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-slate-800">{{ $jadwal['class'] }}</p>
                                        <p class="text-[9px] text-slate-400 uppercase font-bold tracking-widest">{{ $jadwal['type'] }} Session</p>
                                    </div>
                                </div>
                                <button class="text-xs font-black text-blue-700 hover:underline">Detail</button>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-blue-700 p-8 rounded-[3rem] shadow-xl text-white relative overflow-hidden">
                        <i class="fas fa-bullhorn absolute -right-10 -bottom-10 text-9xl text-white/5 -rotate-12"></i>
                        <h3 class="font-black text-blue-200 mb-6 uppercase italic tracking-tighter">Info Akademik</h3>
                        <div class="space-y-4">
                            <div class="bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/10">
                                <p class="text-xs font-bold text-blue-200 mb-1 italic">22 Jan 2026</p>
                                <p class="text-sm font-medium">Input nilai tryout periode Januari paling lambat tanggal 25.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div x-show="menu === 'schedule'" x-transition x-cloak>
                <div class="mb-8">
                    <h2 class="text-2xl font-black text-slate-800 italic uppercase tracking-tighter">Kalender Mengajar</h2>
                    <p class="text-sm text-slate-400 font-bold italic uppercase tracking-widest">Manajemen waktu dan sesi bimbingan mingguan.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-7 gap-4">
                    @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $hari)
                    <div class="space-y-4">
                        <div class="bg-blue-700 p-3 rounded-2xl text-center shadow-lg shadow-blue-100">
                            <span class="text-[10px] font-black text-white uppercase tracking-tighter italic">{{ $hari }}</span>
                        </div>
                        
                        {{-- Contoh Item Jadwal --}}
                        @if($loop->first || $loop->iteration == 3)
                        <div class="bg-white p-4 rounded-3xl border border-slate-100 shadow-sm group hover:border-blue-700 transition cursor-pointer">
                            <p class="text-[8px] font-black text-blue-700 uppercase mb-1">14:00 - 16:00</p>
                            <p class="text-[11px] font-black text-slate-800 leading-tight mb-2 uppercase italic">Matematika Kls 12</p>
                            <div class="flex items-center text-[8px] font-bold text-slate-400 uppercase tracking-tighter">
                                <i class="fas fa-map-marker-alt mr-1 text-red-500"></i> Zoom Meet
                            </div>
                        </div>
                        @else
                        <div class="bg-slate-100/50 p-4 rounded-3xl border border-dashed border-slate-200 flex items-center justify-center min-h-[100px]">
                            <span class="text-[8px] font-bold text-slate-300 uppercase italic">Tidak ada sesi</span>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>

            <div x-show="menu === 'my_classes' && !selectedClass" x-transition x-cloak>
                <h2 class="text-2xl font-black text-slate-800 italic uppercase tracking-tighter mb-8">Kelas Yang Saya Ampu</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach(['Matematika Kls 12', 'Fisika Kls 11', 'Kimia UTBK'] as $kelas)
                    <div class="bg-white p-1 rounded-[3rem] border border-slate-100 shadow-sm hover:shadow-2xl transition-all duration-500 group overflow-hidden">
                        <div class="bg-slate-900 h-32 rounded-[2.5rem] flex items-center justify-center relative overflow-hidden">
                            <div class="absolute inset-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
                            <i class="fas fa-brain text-white/10 text-7xl absolute -right-4 -bottom-4"></i>
                            <span class="text-white font-black italic uppercase tracking-tighter z-10">Mandala Learning</span>
                        </div>
                        <div class="p-8">
                            <h4 class="font-black text-slate-800 italic text-xl mb-1">{{ $kelas }}</h4>
                            <p class="text-[10px] font-bold text-blue-700 uppercase tracking-widest mb-6">Reguler Online • 20 Pertemuan</p>
                            
                            <div class="flex items-center -space-x-2 mb-8">
                                @for($i=0; $i<4; $i++)
                                <img src="https://ui-avatars.com/api/?name=Student+{{$i}}&background=random" class="w-8 h-8 rounded-full border-2 border-white">
                                @endfor
                                <div class="w-8 h-8 rounded-full bg-slate-100 border-2 border-white flex items-center justify-center text-[10px] font-bold text-slate-400">+4</div>
                            </div>

                            <button @click="selectedClass = '{{ $kelas }}'; classTab = 'timeline'" class="w-full bg-slate-100 text-slate-800 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest group-hover:bg-blue-700 group-hover:text-white transition-all duration-300">
                                Masuk Kelas <i class="fas fa-arrow-right ml-2 opacity-0 group-hover:opacity-100 transition-all"></i>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div x-show="selectedClass" x-transition x-cloak>
                <div class="flex flex-wrap items-center space-x-2 bg-white p-2 rounded-3xl border border-slate-100 shadow-sm mb-10 inline-flex">
                    <button @click="classTab = 'timeline'" :class="classTab === 'timeline' ? 'bg-blue-700 text-white shadow-lg shadow-blue-200' : 'text-slate-400 hover:bg-slate-50'" class="px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition">Timeline</button>
                    <button @click="classTab = 'students'" :class="classTab === 'students' ? 'bg-blue-700 text-white shadow-lg' : 'text-slate-400 hover:bg-slate-50'" class="px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition">Siswa</button>
                    <button @click="classTab = 'materials'" :class="classTab === 'materials' ? 'bg-blue-700 text-white shadow-lg' : 'text-slate-400 hover:bg-slate-50'" class="px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition">Modul</button>
                    <button @click="classTab = 'quiz'" :class="classTab === 'quiz' ? 'bg-blue-700 text-white shadow-lg' : 'text-slate-400 hover:bg-slate-50'" class="px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition">Tugas & Kuis</button>
                    <button @click="classTab = 'forum'" :class="classTab === 'forum' ? 'bg-blue-700 text-white shadow-lg' : 'text-slate-400 hover:bg-slate-50'" class="px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition">Diskusi</button>
                </div>

                <div x-show="classTab === 'timeline'" class="max-w-4xl space-y-8 relative">
                    <div class="absolute left-8 top-0 bottom-0 w-0.5 bg-slate-200"></div>
                    @foreach(['Pertemuan 1: Dasar Logika', 'Pertemuan 2: Aljabar Linear', 'Pertemuan 3: Kalkulus Dasar'] as $index => $pertemuan)
                    <div class="relative pl-20 group">
                        <div class="absolute left-5 top-0 w-6 h-6 bg-white border-4 border-blue-700 rounded-full z-10 group-hover:scale-125 transition"></div>
                        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm hover:shadow-md transition">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <span class="text-[9px] font-black text-blue-700 uppercase tracking-widest italic">Sesi {{ $index + 1 }} • 23 Jan 2026</span>
                                    <h4 class="text-xl font-black text-slate-800 italic uppercase tracking-tighter mt-1">{{ $pertemuan }}</h4>
                                </div>
                                <span class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-[9px] font-black uppercase italic tracking-widest">Selesai</span>
                            </div>
                            <div class="flex space-x-3">
                                <button class="flex items-center space-x-2 bg-slate-50 px-4 py-2 rounded-xl text-[10px] font-bold text-slate-600 hover:bg-blue-50 transition">
                                    <i class="fas fa-file-alt text-blue-700"></i> <span>Materi Sesi</span>
                                </button>
                                <button class="flex items-center space-x-2 bg-slate-50 px-4 py-2 rounded-xl text-[10px] font-bold text-slate-600 hover:bg-blue-50 transition">
                                    <i class="fas fa-video text-red-500"></i> <span>Rekaman Zoom</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div x-show="classTab === 'students'" class="bg-white rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 border-b border-slate-100">
                            <tr>
                                <th class="px-8 py-5 text-[9px] font-black text-slate-400 uppercase italic tracking-widest">Siswa</th>
                                <th class="px-8 py-5 text-[9px] font-black text-slate-400 uppercase italic tracking-widest">Absensi</th>
                                <th class="px-8 py-5 text-[9px] font-black text-slate-400 uppercase italic tracking-widest text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach(['Ahmad Dani', 'Siti Rahma', 'Budi Doremi'] as $s)
                            <tr class="hover:bg-blue-50/20 transition group">
                                <td class="px-8 py-6 flex items-center space-x-3">
                                    <img src="https://ui-avatars.com/api/?name={{$s}}&background=random" class="w-10 h-10 rounded-xl">
                                    <span class="font-black text-slate-800 text-sm italic">{{ $s }}</span>
                                </td>
                                <td class="px-8 py-6 text-xs font-bold text-slate-600 italic">95% Hadir</td>
                                <td class="px-8 py-6 text-right">
                                    <button class="bg-slate-100 text-[9px] font-black uppercase px-4 py-2 rounded-xl hover:bg-blue-700 hover:text-white transition">Lihat Rapor</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div x-show="classTab === 'materials'" class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-sm">
                    <div class="flex justify-between items-center mb-8">
                        <h4 class="font-black text-slate-800 uppercase italic tracking-tighter">Modul Pembelajaran</h4>
                        <button class="bg-blue-700 text-white px-6 py-3 rounded-2xl text-[9px] font-black uppercase tracking-widest shadow-lg">Upload Modul</button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach(['Bab 1: Eksponensial.pdf', 'Latihan Soal Aljabar.docx'] as $doc)
                        <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-transparent hover:border-blue-200 transition group">
                            <div class="flex items-center">
                                <i class="fas fa-file-pdf text-red-500 text-xl mr-3"></i>
                                <span class="text-xs font-black text-slate-700 italic">{{ $doc }}</span>
                            </div>
                            <button class="text-slate-300 group-hover:text-blue-700 transition"><i class="fas fa-download"></i></button>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div x-show="classTab === 'quiz'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-sm">
                         <div class="flex justify-between items-center mb-6">
                             <h4 class="font-black text-slate-800 uppercase italic tracking-tighter">Daftar Kuis</h4>
                             <button class="text-[9px] font-black text-blue-700 uppercase">+ Buat Kuis</button>
                         </div>
                         <div class="space-y-4">
                             <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl italic">
                                 <span class="text-sm font-bold">Kuis Harian 01</span>
                                 <span class="text-[9px] text-blue-700 font-black">10 Soal</span>
                             </div>
                         </div>
                    </div>
                    <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-sm">
                        <div class="flex justify-between items-center mb-6">
                            <h4 class="font-black text-slate-800 uppercase italic tracking-tighter">Penugasan Siswa</h4>
                            <button class="text-[9px] font-black text-blue-700 uppercase">+ Upload Tugas</button>
                        </div>
                        <p class="text-[10px] text-slate-400 italic font-bold">Belum ada tugas yang diupload.</p>
                    </div>
                </div>

                <div x-show="classTab === 'forum'" class="bg-white p-10 rounded-[3rem] border border-slate-100 shadow-sm">
                    <div class="flex items-center space-x-4 mb-8">
                        <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}" class="w-12 h-12 rounded-2xl">
                        <input type="text" placeholder="Ada yang ingin didiskusikan hari ini?" class="flex-grow bg-slate-50 border-none rounded-2xl p-4 text-sm focus:ring-2 focus:ring-blue-700">
                        <button class="bg-blue-700 text-white px-6 py-4 rounded-2xl"><i class="fas fa-paper-plane"></i></button>
                    </div>
                    <div class="space-y-6">
                        <div class="border-l-4 border-blue-700 pl-6 py-2">
                             <p class="text-sm font-black text-slate-800 italic leading-none mb-1">Pertanyaan dari Budi Doremi</p>
                             <p class="text-xs text-slate-500 mb-2 font-medium italic">"Kak, soal nomor 5 yang tadi caranya gimana ya?"</p>
                             <button class="text-[9px] font-black text-blue-700 uppercase italic hover:underline">Balas Diskusi (2 Jawaban)</button>
                        </div>
                    </div>
                </div>

            </div>

        </main>
    </div>

</div>

<style>
    [x-cloak] { display: none !important; }
    
    /* Custom Scrollbar */
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #1d60af; }
    
    /* Sidebar Smooth Transition */
    aside { transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
</style>
@endsection
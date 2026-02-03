@extends('layouts.app')
@section('title', 'Student Dashboard - Mandala Bimbel')

@section('content')
<div class="min-h-screen bg-slate-50 flex" x-data="{ 
    menu: 'overview', 
    sidebarOpen: true,
    selectedClass: null, 
    classTab: 'timeline'
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
                <i class="fas fa-user-graduate text-blue-700 text-xl"></i>
            </div>
            <div x-show="sidebarOpen" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 tracking-widest" x-transition:enter-end="opacity-100 tracking-normal" class="ml-4 whitespace-nowrap">
                <p class="font-black leading-none text-lg tracking-tight text-white uppercase">Mandala</p>
                <p class="text-[9px] text-blue-100 font-bold uppercase tracking-[0.2em] mt-1 opacity-70">Student Hub</p>
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
                    <i class="fas fa-book-open text-lg"></i>
                </div>
                <span x-show="sidebarOpen" class="ml-3 font-bold text-sm">Kelas Saya</span>
            </button>

            <button @click="menu = 'schedule'" 
               :class="menu === 'schedule' ? 'bg-white text-blue-700 shadow-xl' : 'hover:bg-blue-600 text-white'" 
               class="w-full flex items-center p-3.5 rounded-2xl transition-all duration-200 group">
                <div class="w-8 flex justify-center items-center">
                    <i class="fas fa-calendar-alt text-lg"></i>
                </div>
                <span x-show="sidebarOpen" class="ml-3 font-bold text-sm">Jadwal Les</span>
            </button>

            <button @click="menu = 'billing'" 
               :class="menu === 'billing' ? 'bg-white text-blue-700 shadow-xl' : 'hover:bg-blue-600 text-white'" 
               class="w-full flex items-center p-3.5 rounded-2xl transition-all duration-200 group">
                <div class="w-8 flex justify-center items-center">
                    <i class="fas fa-wallet text-lg"></i>
                </div>
                <span x-show="sidebarOpen" class="ml-3 font-bold text-sm">Pembayaran</span>
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
                    <template x-if="menu === 'overview'">Dashboard Student</template>
                    <template x-if="menu === 'my_classes'">Kelas Saya</template>
                    <template x-if="menu === 'schedule'">Jadwal Les</template>
                    <template x-if="menu === 'billing'">Riwayat Pembayaran</template>
                </h1>
            </div>

            <div class="flex items-center space-x-6">
                <div class="hidden md:flex flex-col text-right border-r border-slate-200 pr-6">
                    <p class="text-sm font-black text-slate-800 leading-none">{{ Auth::user()->name ?? 'Student' }}</p>
                    <p class="text-[10px] text-orange-500 font-bold uppercase mt-1">Siswa Mandala</p>
                </div>
                <div class="relative">
                    <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'Student' }}&background=1d60af&color=fff&bold=true" 
                         class="w-11 h-11 rounded-2xl shadow-lg border-2 border-white" />
                    <span class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-white rounded-full shadow-sm"></span>
                </div>
            </div>
        </header>

        <main class="flex-grow overflow-y-auto bg-slate-100 p-8 custom-scrollbar">
            <div x-show="menu === 'overview' && !selectedClass" x-transition x-cloak>
                <div class="mb-8 text-center md:text-left">
                    <h2 class="text-3xl font-black text-slate-800 italic uppercase tracking-tighter">Gas Terus, {{ explode(' ', Auth::user()->name)[0] }}! 🔥</h2>
                    <p class="text-sm text-slate-400 font-bold italic uppercase tracking-widest">Pantau progres belajarmu di sini.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
                        <p class="text-[10px] font-black text-slate-400 uppercase mb-1 tracking-widest italic">Kehadiran</p>
                        <p class="text-3xl font-black text-slate-800">98<span class="text-sm text-blue-500 uppercase italic">%</span></p>
                    </div>
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
                        <p class="text-[10px] font-black text-slate-400 uppercase mb-1 tracking-widest italic">Tugas Selesai</p>
                        <p class="text-3xl font-black text-slate-800">12<span class="text-sm text-orange-500 uppercase italic">/15</span></p>
                    </div>
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
                        <p class="text-[10px] font-black text-slate-400 uppercase mb-1 tracking-widest italic">Avg. Tryout</p>
                        <p class="text-3xl font-black text-slate-800">745</p>
                    </div>
                    <div class="bg-blue-700 p-6 rounded-[2rem] shadow-xl text-white">
                        <p class="text-[10px] font-black text-blue-200 uppercase mb-1 tracking-widest italic">Peringkat Kelas</p>
                        <p class="text-3xl font-black italic">#3</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="bg-white p-8 rounded-[3rem] shadow-sm border border-slate-100">
                        <h3 class="font-black text-slate-800 mb-6 uppercase italic tracking-tighter flex items-center">
                            <i class="fas fa-chart-line mr-3 text-blue-500"></i> Grafik Nilai Try Out
                        </h3>
                        <div class="h-48 bg-slate-50 rounded-3xl border border-dashed border-slate-200 flex flex-col items-center justify-center text-center p-6">
                             <i class="fas fa-brain text-slate-200 text-4xl mb-2"></i>
                             <p class="text-[10px] font-bold text-slate-400 uppercase">Menunggu data Try Out bulan ini...</p>
                        </div>
                    </div>

                    <div class="bg-white p-8 rounded-[3rem] shadow-sm border border-slate-100">
                        <h3 class="font-black text-slate-800 mb-6 uppercase italic tracking-tighter flex items-center">
                            <i class="fas fa-user-tie mr-3 text-blue-500"></i> Mentor Kamu
                        </h3>
                        <div class="space-y-4">
                            @foreach([['name' => 'Kak Andi Satria', 'sub' => 'Matematika'], ['name' => 'Kak Budi Fisikawan', 'sub' => 'Fisika']] as $m)
                            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-transparent hover:border-blue-200 transition group">
                                <div class="flex items-center space-x-3">
                                    <img src="https://ui-avatars.com/api/?name={{$m['name']}}&background=random" class="w-10 h-10 rounded-xl shadow-sm">
                                    <div>
                                        <p class="text-sm font-black text-slate-800 leading-tight">{{ $m['name'] }}</p>
                                        <p class="text-[9px] text-blue-500 font-bold uppercase">{{ $m['sub'] }} Mentor</p>
                                    </div>
                                </div>
                                <button class="bg-white p-2 rounded-lg text-blue-500 shadow-sm hover:bg-green-500 hover:text-white transition">
                                    <i class="fab fa-whatsapp"></i>
                                </button>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div x-show="menu === 'my_classes' && !selectedClass" x-transition x-cloak>
                <h2 class="text-2xl font-black text-slate-800 italic uppercase tracking-tighter mb-8">Program Belajarku</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="bg-white p-1 rounded-[3rem] border border-slate-100 shadow-sm hover:shadow-2xl transition-all duration-500 group overflow-hidden">
                        <div class="bg-blue-700 h-32 rounded-[2.5rem] flex items-center justify-center relative overflow-hidden">
                            <div class="absolute inset-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
                            <span class="text-white font-black italic uppercase tracking-tighter z-10 text-xl">INTENSIF UTBK 2026</span>
                        </div>
                        <div class="p-8">
                            <p class="text-[10px] font-bold text-blue-500 uppercase tracking-widest mb-2">ONLINE • SMA LULUSAN</p>
                            <h4 class="font-black text-slate-800 italic text-xl mb-6">Materi & Latihan Soal</h4>
                            <div class="grid grid-cols-2 gap-2 mb-8">
                                <div class="bg-slate-50 p-3 rounded-2xl text-center">
                                    <p class="text-xs font-black text-slate-800">24</p>
                                    <p class="text-[8px] font-bold text-slate-400 uppercase">Modul</p>
                                </div>
                                <div class="bg-slate-50 p-3 rounded-2xl text-center">
                                    <p class="text-xs font-black text-slate-800">10</p>
                                    <p class="text-[8px] font-bold text-slate-400 uppercase">Kuis</p>
                                </div>
                            </div>
                            <button @click="selectedClass = 'INTENSIF UTBK 2026'; classTab = 'timeline'" class="w-full bg-slate-900 text-white py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-700 transition-all">
                                MULAI BELAJAR <i class="fas fa-rocket ml-2"></i>
                            </button>
                        </div>
                    </div>

                    <div class="bg-white p-1 rounded-[3rem] border border-slate-100 shadow-sm hover:shadow-2xl transition-all duration-500 group overflow-hidden">
                        <div class="bg-orange-500 h-32 rounded-[2.5rem] flex items-center justify-center relative overflow-hidden">
                            <div class="absolute inset-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
                            <span class="text-white font-black italic uppercase tracking-tighter z-10 text-xl">MASTERING ENGLISH</span>
                        </div>
                        <div class="p-8">
                            <p class="text-[10px] font-bold text-orange-500 uppercase tracking-widest mb-2">HYBRID • UMUM</p>
                            <h4 class="font-black text-slate-800 italic text-xl mb-6">Conversation & Grammar</h4>
                            <div class="grid grid-cols-2 gap-2 mb-8">
                                <div class="bg-slate-50 p-3 rounded-2xl text-center">
                                    <p class="text-xs font-black text-slate-800">12</p>
                                    <p class="text-[8px] font-bold text-slate-400 uppercase">Modul</p>
                                </div>
                                <div class="bg-slate-50 p-3 rounded-2xl text-center">
                                    <p class="text-xs font-black text-slate-800">4</p>
                                    <p class="text-[8px] font-bold text-slate-400 uppercase">Kuis</p>
                                </div>
                            </div>
                            <button @click="selectedClass = 'MASTERING ENGLISH'; classTab = 'timeline'" class="w-full bg-slate-900 text-white py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-orange-500 transition-all">
                                MULAI BELAJAR <i class="fas fa-rocket ml-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div x-show="selectedClass" x-transition x-cloak>
                <div class="mb-8">
                    <h2 class="text-3xl font-black text-slate-800 italic uppercase tracking-tighter" x-text="selectedClass"></h2>
                    <p class="text-xs text-slate-400 font-bold uppercase italic tracking-widest">Akses semua materi dan tugas anda</p>
                </div>

                <div class="flex flex-wrap items-center space-x-2 bg-white p-2 rounded-3xl border border-slate-100 shadow-sm mb-10 inline-flex">
                    <button @click="classTab = 'timeline'" :class="classTab === 'timeline' ? 'bg-blue-700 text-white shadow-lg' : 'text-slate-400 hover:text-slate-600'" class="px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition">Timeline</button>
                    <button @click="classTab = 'materials'" :class="classTab === 'materials' ? 'bg-blue-700 text-white shadow-lg' : 'text-slate-400 hover:text-slate-600'" class="px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition">Modul</button>
                    <button @click="classTab = 'tasks'" :class="classTab === 'tasks' ? 'bg-blue-700 text-white shadow-lg' : 'text-slate-400 hover:text-slate-600'" class="px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition">Tugas</button>
                    <button @click="classTab = 'forum'" :class="classTab === 'forum' ? 'bg-blue-700 text-white shadow-lg' : 'text-slate-400 hover:text-slate-600'" class="px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition">Diskusi</button>
                </div>

                <div x-show="classTab === 'timeline'" class="max-w-4xl space-y-8 relative">
                    <div class="absolute left-8 top-0 bottom-0 w-0.5 bg-slate-200"></div>
                    @foreach(['Logika Dasar & Himpunan', 'Eksponensial & Logaritma', 'Vektor Dasar'] as $index => $materi)
                    <div class="relative pl-20 group">
                        <div class="absolute left-5 top-0 w-6 h-6 bg-white border-4 border-blue-700 rounded-full z-10 group-hover:scale-110 transition-transform"></div>
                        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
                            <span class="text-[9px] font-black text-blue-500 uppercase tracking-widest italic leading-none">Sesi Ke-{{ $index + 1 }}</span>
                            <h4 class="text-xl font-black text-slate-800 italic uppercase mb-4">{{ $materi }}</h4>
                            <div class="flex flex-wrap gap-3">
                                <button class="flex items-center space-x-2 bg-slate-50 px-4 py-2 rounded-xl text-[10px] font-bold text-slate-600 hover:bg-blue-700 hover:text-white transition">
                                    <i class="fas fa-play"></i> <span>VIDEO REKAMAN</span>
                                </button>
                                <button class="flex items-center space-x-2 bg-slate-50 px-4 py-2 rounded-xl text-[10px] font-bold text-slate-600 hover:bg-orange-500 hover:text-white transition">
                                    <i class="fas fa-file-pdf"></i> <span>HANDOUT PDF</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div x-show="classTab === 'materials'" class="grid grid-cols-1 md:grid-cols-2 gap-6" x-transition>
                    @foreach(['Modul 01 - Persiapan UTBK', 'Modul 02 - Aljabar Lanjutan', 'Modul 03 - Geometri'] as $modul)
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center text-xl">
                                <i class="fas fa-book"></i>
                            </div>
                            <div>
                                <h5 class="font-black text-slate-800 text-sm italic uppercase">{{ $modul }}</h5>
                                <p class="text-[9px] text-slate-400 font-bold uppercase">PDF • 12.5 MB</p>
                            </div>
                        </div>
                        <button class="text-blue-600 hover:text-blue-800"><i class="fas fa-download"></i></button>
                    </div>
                    @endforeach
                </div>

                <div x-show="classTab === 'tasks'" class="space-y-4" x-transition>
                    @foreach(['Quiz Logika Matematika', 'Latihan Eksponen 01'] as $tugas)
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-orange-100 text-orange-600 rounded-2xl flex items-center justify-center text-xl">
                                <i class="fas fa-tasks"></i>
                            </div>
                            <div>
                                <h5 class="font-black text-slate-800 text-sm italic uppercase">{{ $tugas }}</h5>
                                <p class="text-[9px] text-red-500 font-bold uppercase italic">Deadline: 25 Jan 2026</p>
                            </div>
                        </div>
                        <button class="bg-slate-900 text-white px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-orange-500 transition">KERJAKAN SEKARANG</button>
                    </div>
                    @endforeach
                </div>

                <div x-show="classTab === 'forum'" class="bg-white p-8 rounded-[3rem] border border-slate-100" x-transition>
                    <div class="flex items-center space-x-4 mb-8">
                        <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400">
                            <i class="fas fa-comment-dots"></i>
                        </div>
                        <input type="text" placeholder="Tanyakan sesuatu pada mentor atau teman..." class="flex-grow bg-slate-50 border-none rounded-2xl px-6 py-3 text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="space-y-6">
                        <div class="border-l-4 border-blue-500 pl-4">
                            <p class="text-xs font-black text-slate-800 mb-1">Andi Pratama <span class="text-[9px] text-slate-400 ml-2 font-normal">2 jam yang lalu</span></p>
                            <p class="text-sm text-slate-600">Kak, untuk soal nomor 5 di modul 2 pakai rumus cepat yang mana ya?</p>
                        </div>
                    </div>
                </div>
            </div>

            <div x-show="menu === 'schedule'" x-transition x-cloak>
                <div class="flex justify-between items-end mb-8">
                    <div>
                        <h2 class="text-2xl font-black text-slate-800 italic uppercase tracking-tighter">Agenda Belajar</h2>
                        <p class="text-xs text-slate-400 font-bold uppercase italic tracking-widest">Minggu Ini, Jan 2026</p>
                    </div>
                    <div class="flex space-x-2">
                        <button class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-blue-600"><i class="fas fa-chevron-left"></i></button>
                        <button class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-blue-600"><i class="fas fa-chevron-right"></i></button>
                    </div>
                </div>

                <div class="space-y-6">
                    @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $day)
                    <div class="flex flex-col md:flex-row md:items-center bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm hover:border-blue-500 transition-colors">
                        <div class="md:w-32 mb-4 md:mb-0">
                            <p class="text-[10px] font-black text-blue-600 uppercase italic tracking-widest">{{ $day }}</p>
                            <p class="text-2xl font-black text-slate-800">2{{ $loop->index + 1 }} <span class="text-xs uppercase">Jan</span></p>
                        </div>
                        <div class="flex-grow flex flex-col md:flex-row md:items-center space-y-4 md:space-y-0 md:space-x-6 border-l-0 md:border-l-2 border-slate-100 md:pl-8">
                            <div class="flex-grow">
                                <p class="text-[9px] font-bold text-orange-500 uppercase mb-1">16:00 - 18:00 WIB</p>
                                <h4 class="text-lg font-black text-slate-800 italic uppercase">Matematika Intensif - SBMPTN</h4>
                                <p class="text-xs text-slate-400 font-bold">Mentor: Kak Andi Satria</p>
                            </div>
                            <div class="flex items-center space-x-4">
                                <div class="flex -space-x-2">
                                    <div class="w-8 h-8 rounded-full border-2 border-white bg-slate-200"></div>
                                    <div class="w-8 h-8 rounded-full border-2 border-white bg-slate-300"></div>
                                    <div class="w-8 h-8 rounded-full border-2 border-white bg-blue-500 flex items-center justify-center text-[8px] font-bold text-white">+12</div>
                                </div>
                                <button class="px-6 py-3 bg-blue-700 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:shadow-lg shadow-blue-100 transition-all">JOIN CLASS</button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div x-show="menu === 'billing'" x-transition x-cloak>
                <h2 class="text-2xl font-black text-slate-800 italic uppercase tracking-tighter mb-8">Riwayat Transaksi</h2>
                <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 border-b border-slate-100">
                            <tr>
                                <th class="px-8 py-5 text-[9px] font-black text-slate-400 uppercase italic">Program</th>
                                <th class="px-8 py-5 text-[9px] font-black text-slate-400 uppercase italic text-center">Status</th>
                                <th class="px-8 py-5 text-[9px] font-black text-slate-400 uppercase italic text-right">Tagihan</th>
                                <th class="px-8 py-5 text-[9px] font-black text-slate-400 uppercase italic text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <tr class="hover:bg-blue-50/20 transition">
                                <td class="px-8 py-6 font-bold">
                                    <p class="text-sm font-black text-slate-800 uppercase italic">INTENSIF UTBK 2026</p>
                                    <p class="text-[10px] text-slate-400 italic">Metode Online</p>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase italic bg-green-100 text-green-700">LUNAS</span>
                                </td>
                                <td class="px-8 py-6 text-right font-black text-slate-800">Rp 1.250.000</td>
                                <td class="px-8 py-6 text-center">
                                    <button class="text-[9px] font-black uppercase text-blue-600 hover:underline">Invoice</button>
                                </td>
                            </tr>
                            <tr class="hover:bg-blue-50/20 transition">
                                <td class="px-8 py-6 font-bold">
                                    <p class="text-sm font-black text-slate-800 uppercase italic">MASTERING ENGLISH</p>
                                    <p class="text-[10px] text-slate-400 italic">Metode Hybrid</p>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase italic bg-orange-100 text-orange-700 animate-pulse">MENUNGGU</span>
                                </td>
                                <td class="px-8 py-6 text-right font-black text-slate-800">Rp 750.000</td>
                                <td class="px-8 py-6 text-center">
                                    <button class="bg-blue-700 text-white px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-slate-900 transition shadow-lg shadow-blue-100">
                                        BAYAR SEKARANG
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
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
@extends('layouts.app')
@section('title', 'Admin Panel - Mandala')

@section('content')
{{-- 
    REVISI STRUKTUR:
    1. Menggunakan fixed wrapper untuk background biru agar tidak gantung.
    2. Memisahkan Sidebar dan Content secara visual agar tidak saling menindih (Z-Index).
--}}
<div x-data="{ sidebarOpen: true }" 
     class="bg-slate-50 font-jakarta overflow-hidden" 
     style="transform: scale(0.90); transform-origin: top left; width: 111.111%; height: 111.111%; position: fixed; top: 0; left: 0;">
    
    {{-- 1. BACKGROUND BIRU STATIS (Mencegah tembok putih menutupi area kiri) --}}
    <div :class="sidebarOpen ? 'w-72' : 'w-20'" 
         class="absolute top-0 left-0 bottom-0 bg-blue-700 transition-all duration-300 z-0"></div>

    <div class="flex h-full relative z-10">
        {{-- 2. ASIDE (SIDEBAR) --}}
        <aside 
            :class="sidebarOpen ? 'w-72' : 'w-20'" 
            class="h-full text-white flex flex-col transition-all duration-300 relative z-50 flex-shrink-0">
            
            {{-- Tombol Toggle --}}
            <button 
                @click="sidebarOpen = !sidebarOpen" 
                class="absolute -right-4 top-10 bg-orange-500 text-white w-8 h-8 rounded-full flex items-center justify-center shadow-lg hover:bg-orange-600 transition-all z-[60] border-2 border-white focus:outline-none cursor-pointer">
                <i class="fas text-xs transition-transform duration-300" :class="sidebarOpen ? 'fa-angle-left' : 'fa-angle-right'"></i>
            </button>

            {{-- Logo --}}
            <div class="p-6 flex items-center h-20 mb-4 flex-shrink-0 relative z-10">
                <div class="w-10 h-10 bg-white rounded-xl flex-shrink-0 flex items-center justify-center shadow-lg">
                    <i class="fas fa-shield-alt text-blue-700 text-xl"></i>
                </div>
                <span x-show="sidebarOpen" x-transition x-cloak class="ml-3 font-black tracking-tighter text-lg uppercase whitespace-nowrap text-white">
                    AREA <span class="text-orange-400">ADMIN</span>
                </span>
            </div>

            {{-- Navigasi --}}
            <nav class="flex-grow px-4 space-y-2 overflow-y-auto overflow-x-hidden custom-scrollbar relative z-10">
                <p x-show="sidebarOpen" class="px-3 text-[10px] font-black text-blue-200 uppercase tracking-[0.2em] mb-4 opacity-40">Navigasi Utama</p>
                
                {{-- Link Dashboard --}}
                <a href="{{ route('admin.overview') }}" 
                   class="flex items-center p-3.5 rounded-2xl transition-all duration-200 group {{ request()->routeIs('admin.overview') ? 'bg-white text-blue-700 shadow-xl' : 'hover:bg-blue-600 text-white' }}">
                    <div class="w-8 flex justify-center items-center"><i class="fas fa-th-large text-lg"></i></div>
                    <span x-show="sidebarOpen" class="ml-3 font-bold text-sm">Dashboard</span>
                </a>

                {{-- Link Programs --}}
                <a href="{{ route('admin.programs') }}" 
                   class="flex items-center p-3.5 rounded-2xl transition-all duration-200 group {{ request()->routeIs('admin.programs') && !request()->route('type') ? 'bg-white text-blue-700 shadow-xl' : 'hover:bg-blue-600 text-white' }}">
                    <div class="w-8 flex justify-center items-center"><i class="fas fa-layer-group text-lg"></i></div>
                    <span x-show="sidebarOpen" class="ml-3 font-bold text-sm">Paket Bimbel</span>
                </a>

                {{-- Link Reguler --}}
                <a href="{{ route('admin.programs', ['type' => 'reguler']) }}" 
                   class="flex items-center p-3.5 rounded-2xl transition-all duration-200 group {{ request()->fullUrlIs(route('admin.programs', ['type' => 'reguler'])) ? 'bg-white text-blue-700 shadow-xl' : 'hover:bg-blue-600 text-white' }}">
                    <div class="w-8 flex justify-center items-center"><i class="fas fa-tags text-lg"></i></div>
                    <span x-show="sidebarOpen" class="ml-3 font-bold text-sm">Harga Reguler</span>
                </a>

                {{-- Link Intensif --}}
                <a href="{{ route('admin.programs', ['type' => 'intensif']) }}" 
                   class="flex items-center p-3.5 rounded-2xl transition-all duration-200 group {{ request()->fullUrlIs(route('admin.programs', ['type' => 'intensif'])) ? 'bg-white text-blue-700 shadow-xl' : 'hover:bg-blue-600 text-white' }}">
                    <div class="w-8 flex justify-center items-center"><i class="fas fa-bolt text-lg"></i></div>
                    <span x-show="sidebarOpen" class="ml-3 font-bold text-sm">Harga Intensif</span>
                </a>

                {{-- Link Mentors --}}
                <a href="{{ route('admin.mentors') }}" 
                   class="flex items-center p-3.5 rounded-2xl transition-all duration-200 group {{ request()->routeIs('admin.mentors') ? 'bg-white text-blue-700 shadow-xl' : 'hover:bg-blue-600 text-white' }}">
                    <div class="w-8 flex justify-center items-center"><i class="fas fa-user-tie text-lg"></i></div>
                    <span x-show="sidebarOpen" class="ml-3 font-bold text-sm">Manajemen Mentor</span>
                </a>

                {{-- Link Payments --}}
                <a href="{{ route('admin.payments') }}" 
                   class="flex items-center p-3.5 rounded-2xl transition-all duration-200 group {{ request()->routeIs('admin.payments') ? 'bg-white text-blue-700 shadow-xl' : 'hover:bg-blue-600 text-white' }}">
                    <div class="w-8 flex justify-center items-center"><i class="fas fa-check-double text-lg"></i></div>
                    <span x-show="sidebarOpen" class="ml-3 font-bold text-sm">Verifikasi Bayar</span>
                </a>

                {{-- Link Pesan --}}
                <a href="{{ route('admin.messages') }}" 
                   class="flex items-center p-3.5 rounded-2xl transition-all duration-200 group {{ request()->routeIs('admin.messages') ? 'bg-white text-blue-700 shadow-xl' : 'hover:bg-blue-600 text-white' }}">
                    <div class="w-8 flex justify-center items-center"><i class="fas fa-envelope text-lg"></i></div>
                    <span x-show="sidebarOpen" class="ml-3 font-bold text-sm">Pesan Masuk</span>
                </a>
            </nav>

            {{-- Logout --}}
            <div class="p-4 border-t border-blue-600/50 flex-shrink-0 relative z-10">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="w-full flex items-center p-3.5 rounded-2xl text-blue-200 hover:bg-orange-500 hover:text-white transition-all group">
                        <div class="w-8 flex justify-center items-center"><i class="fas fa-power-off text-lg group-hover:rotate-90 transition-transform"></i></div>
                        <span x-show="sidebarOpen" class="ml-3 font-bold text-sm">Keluar Sesi</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- 3. KONTEN UTAMA --}}
        <div class="flex-1 flex flex-col min-w-0 bg-slate-50 relative z-10">
            <header class="sticky top-0 h-20 bg-white border-b border-slate-200 flex justify-between items-center px-8 flex-shrink-0 z-40 shadow-sm">
                <div class="flex flex-col text-left">
                    <h2 class="text-[10px] font-black text-blue-700 uppercase tracking-[0.2em] mb-1">Mandala Portal</h2>
                    <h1 class="text-slate-900 font-extrabold text-xl tracking-tight">Admin Dashboard</h1>
                </div>

                <div class="flex items-center space-x-6">
                    <div class="hidden md:flex flex-col text-right border-r border-slate-200 pr-6">
                        <p class="text-sm font-black text-slate-800 leading-none">{{ Auth::user()->name ?? 'Administrator' }}</p>
                        <p class="text-[10px] text-orange-500 font-bold uppercase mt-1 tracking-tighter">Super Admin</p>
                    </div>
                    <div class="relative">
                        <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'Admin' }}&background=1d4ed8&color=fff&bold=true" 
                             class="w-11 h-11 rounded-2xl shadow-lg border-2 border-white" />
                        <span class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-white rounded-full"></span>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto custom-main-scroll px-8 py-10">
                <div class="w-full pb-20">
                    @yield('admin_content')
                </div>
            </main>

            {{-- FOOTER - Sejajar dengan sidebar --}}
            <footer class="py-6 px-8 border-t border-slate-200 bg-slate-50 text-slate-500 text-[11px] font-medium flex flex-col md:flex-row justify-between items-center gap-4 flex-shrink-0">
                <div class="flex items-center space-x-2">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    <p>Sistem Aktif &copy; 2026 <span class="font-bold text-blue-600 uppercase">Mandala Bimbel</span></p>
                </div>
                
                <div class="flex items-center space-x-6 uppercase tracking-widest">
                    <a href="{{ route('about') }}" class="hover:text-blue-600 transition-colors">Tentang Kami</a>
                    <a href="{{ route('contact') }}" class="hover:text-orange-500 transition-colors">Hubungi Kontak</a>
                    <span class="text-slate-300">v1.0.2</span>
                </div>
            </footer>
        </div>
    </div>
</div>

<style>
    /* Hilangkan elemen app.blade yang tidak perlu */
    body > nav, body > footer { display: none !important; }
    body { overflow: hidden !important; height: 100vh; background-color: #f8fafc; margin: 0; }
    
    .font-jakarta { font-family: 'Plus Jakarta Sans', sans-serif; }
    
    /* Scrollbar Styling */
    .custom-main-scroll::-webkit-scrollbar { width: 8px; }
    .custom-main-scroll::-webkit-scrollbar-track { background: #f8fafc; }
    .custom-main-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    
    /* Scrollbar Aside */
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }

    [x-cloak] { display: none !important; }
</style>
@endsection
@extends('layouts.app')
@section('title', 'Admin Panel - Mandala')

@section('content')
<div x-data="{ sidebarOpen: true }" class="h-screen w-full bg-slate-50 flex overflow-hidden font-jakarta">
    
    <aside 
        :class="sidebarOpen ? 'w-72' : 'w-20'" 
        class="bg-blue-700 text-white flex flex-col shadow-2xl transition-all duration-300 relative z-50 flex-shrink-0">
        
        <button 
            @click="sidebarOpen = !sidebarOpen" 
            class="absolute -right-4 top-10 bg-orange-500 text-white w-8 h-8 rounded-full flex items-center justify-center shadow-lg hover:bg-orange-600 transition-all z-[60] border-2 border-white focus:outline-none cursor-pointer">
            <i class="fas text-xs transition-transform duration-300" :class="sidebarOpen ? 'fa-angle-left' : 'fa-angle-right'"></i>
        </button>

        <div class="p-6 flex items-center h-20 mb-4 flex-shrink-0">
            <div class="w-10 h-10 bg-white rounded-xl flex-shrink-0 flex items-center justify-center shadow-lg">
                <i class="fas fa-shield-alt text-blue-700 text-xl"></i>
            </div>
            <span x-show="sidebarOpen" x-transition x-cloak class="ml-3 font-black tracking-tighter text-lg uppercase whitespace-nowrap text-white">
                AREA <span class="text-orange-400">ADMIN</span>
            </span>
        </div>

        {{-- Navigasi Utama --}}
        <nav class="flex-grow px-4 space-y-2 overflow-y-auto overflow-x-hidden custom-scrollbar">
            <p x-show="sidebarOpen" class="px-3 text-[10px] font-black text-blue-200 uppercase tracking-[0.2em] mb-4 opacity-40">Navigasi Utama</p>
            
            <a href="{{ route('admin.overview') }}" 
               class="flex items-center p-3.5 rounded-2xl transition-all duration-200 group {{ request()->routeIs('admin.overview') ? 'bg-white text-blue-700 shadow-xl' : 'hover:bg-blue-600 text-white' }}">
                <div class="w-8 flex justify-center items-center">
                    <i class="fas fa-th-large text-lg"></i>
                </div>
                <span x-show="sidebarOpen" class="ml-3 font-bold text-sm">Dashboard</span>
            </a>

            <a href="{{ route('admin.programs') }}" 
               class="flex items-center p-3.5 rounded-2xl transition-all duration-200 group {{ request()->routeIs('admin.programs') ? 'bg-white text-blue-700 shadow-xl' : 'hover:bg-blue-600 text-white' }}">
                <div class="w-8 flex justify-center items-center">
                    <i class="fas fa-layer-group text-lg"></i>
                </div>
                <span x-show="sidebarOpen" class="ml-3 font-bold text-sm">Paket Bimbel</span>
            </a>

            <a href="{{ route('admin.mentors') }}" 
               class="flex items-center p-3.5 rounded-2xl transition-all duration-200 group {{ request()->routeIs('admin.mentors') ? 'bg-white text-blue-700 shadow-xl' : 'hover:bg-blue-600 text-white' }}">
                <div class="w-8 flex justify-center items-center">
                    <i class="fas fa-user-tie text-lg"></i>
                </div>
                <span x-show="sidebarOpen" class="ml-3 font-bold text-sm">Manajemen Mentor</span>
            </a>

            <a href="{{ route('admin.payments') }}" 
               class="flex items-center p-3.5 rounded-2xl transition-all duration-200 group {{ request()->routeIs('admin.payments') ? 'bg-white text-blue-700 shadow-xl' : 'hover:bg-blue-600 text-white' }}">
                <div class="w-8 flex justify-center items-center">
                    <i class="fas fa-check-double text-lg"></i>
                </div>
                <span x-show="sidebarOpen" class="ml-3 font-bold text-sm text-nowrap">Verifikasi Bayar</span>
            </a>

            <a href="{{ route('admin.messages') }}" 
               class="flex items-center p-3.5 rounded-2xl transition-all duration-200 group {{ request()->routeIs('admin.messages') ? 'bg-white text-blue-700 shadow-xl' : 'hover:bg-blue-600 text-white' }}">
                <div class="w-8 flex justify-center items-center">
                    <i class="fas fa-envelope text-lg"></i>
                </div>
                <span x-show="sidebarOpen" class="ml-3 font-bold text-sm text-nowrap">Pesan Masuk</span>
                @if(request()->routeIs('admin.messages'))
                    <i x-show="sidebarOpen" class="fas fa-circle ml-auto text-[6px] text-orange-500"></i>
                @endif
            </a>
        </nav>

        {{-- Footer Sidebar (Logout) --}}
        <div class="p-4 border-t border-blue-600/50 flex-shrink-0">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="w-full flex items-center p-3.5 rounded-2xl text-blue-200 hover:bg-orange-500 hover:text-white transition-all group">
                    <div class="w-8 flex justify-center items-center">
                        <i class="fas fa-power-off text-lg group-hover:rotate-90 transition-transform"></i>
                    </div>
                    <span x-show="sidebarOpen" class="ml-3 font-bold text-sm">Keluar Sesi</span>
                </button>
            </form>
        </div>
    </aside>

    <div class="flex-1 flex flex-col min-w-0 h-screen overflow-y-auto custom-main-scroll">
        
        <header class="sticky top-0 h-20 bg-white border-b border-slate-200 flex justify-between items-center px-8 flex-shrink-0 z-40 shadow-sm">
            <div class="flex flex-col">
                <h2 class="text-[10px] font-black text-blue-700 uppercase tracking-[0.2em] mb-1">Mandala Portal</h2>
                <h1 class="text-slate-900 font-extrabold text-xl tracking-tight">
                    @if(request()->routeIs('admin.overview')) Dashboard Overview
                    @elseif(request()->routeIs('admin.programs')) Programs Management
                    @elseif(request()->routeIs('admin.mentors')) Mentor Directory
                    @elseif(request()->routeIs('admin.messages')) Pesan Masuk
                    @else Payment Verification @endif
                </h1>
            </div>

            <div class="flex items-center space-x-6">
                <div class="hidden md:flex flex-col text-right border-r border-slate-200 pr-6">
                    <p class="text-sm font-black text-slate-800 leading-none">{{ Auth::user()->name ?? 'Administrator' }}</p>
                    <p class="text-[10px] text-orange-500 font-bold uppercase mt-1 tracking-tighter">Super Admin Access</p>
                </div>
                <div class="relative">
                    <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'Admin' }}&background=1d4ed8&color=fff&bold=true" 
                         class="w-11 h-11 rounded-2xl shadow-lg border-2 border-white" />
                    <span class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-white rounded-full shadow-sm"></span>
                </div>
            </div>
        </header>

        <main class="flex-1 p-8">
            <div class="max-w-7xl mx-auto">
                @yield('admin_content')
            </div>
        </main>
    </div>
</div>

<style>
    body > nav, body > footer { display: none !important; }
    body { overflow: hidden !important; height: 100vh; }
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&display=swap');
    .font-jakarta { font-family: 'Plus Jakarta Sans', sans-serif; }
    .custom-main-scroll::-webkit-scrollbar { width: 8px; }
    .custom-main-scroll::-webkit-scrollbar-track { background: #f8fafc; }
    .custom-main-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    [x-cloak] { display: none !important; }
</style>
@endsection
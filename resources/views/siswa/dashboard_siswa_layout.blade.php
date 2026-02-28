@extends('layouts.app')
@section('title', 'Student Hub - Mandala Bimbel')

@section('content')
{{-- REVISI: Menerapkan scale 0.90 sama seperti admin dan memperbaiki sidebar --}}
<div x-data="{ sidebarOpen: true }" 
     class="bg-slate-50 font-jakarta overflow-hidden" 
     style="transform: scale(0.90); transform-origin: top left; width: 111.111%; height: 111.111%; position: fixed; top: 0; left: 0;">
    
    {{-- 1. BACKGROUND BIRU STATIS (Mencegah tembok putih menutupi area kiri) --}}
    <div :class="sidebarOpen ? 'w-72' : 'w-20'" 
         class="absolute top-0 left-0 bottom-0 bg-blue-700 transition-all duration-300 z-0"></div>

    <div class="flex h-full relative z-10">
        {{-- 2. ASIDE (SIDEBAR) --}}
        @include('siswa.sidebar')

        {{-- Area Kanan --}}
        <div class="flex-1 flex flex-col min-w-0 h-full relative">
        
        {{-- Header --}}
        <div class="flex-none z-40">
            @include('siswa.header')
        </div>

        {{-- AREA SCROLL (Isi Dashboard) --}}
        <div class="flex-1 overflow-y-auto custom-scrollbar flex flex-col justify-between">
            
            {{-- Konten Utama --}}
            <main class="p-6 md:p-8 flex-grow">
                @yield('siswa_content')
            </main>

            {{-- FOOTER - Sekarang dipastikan nempel di bawah --}}
            <footer class="mt-auto py-6 px-8 border-t border-slate-200 bg-white text-slate-500 text-[11px] font-medium flex flex-col md:flex-row justify-between items-center gap-4">
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
</div>

<style>
    /* 1. Sembunyikan elemen luar */
    body > nav, body > footer { display: none !important; } 

    body {
        overflow: hidden !important;
        height: 100vh;
        width: 100vw;
        background-color: #f8fafc;
        margin: 0;
    }

    .font-jakarta { font-family: 'Plus Jakarta Sans', sans-serif; }

    /* 2. Scrollbar */
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; }
    .custom-scrollbar::-webkit-scrollbar-thumb { 
        background: #cbd5e1; 
        border-radius: 10px; 
    }

    [x-cloak] { display: none !important; }

    /* 3. Main flex */
    main {
        flex: 1 0 auto;
    }
</style>
@endsection
@extends('layouts.app')
@section('title', 'Student Hub - Mandala Bimbel')

@section('content')
{{-- Kontainer Utama --}}
<div x-data="{ sidebarOpen: true }" class="fixed inset-0 w-full bg-slate-50 flex overflow-hidden">
    
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

<style>
    /* 1. Sembunyikan elemen luar */
    body > nav, body > footer { display: none !important; } 

    body {
        overflow: hidden !important;
        height: 100vh;
        width: 100vw;
    }

    /* 2. Scrollbar */
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; }
    .custom-scrollbar::-webkit-scrollbar-thumb { 
        background: #cbd5e1; 
        border-radius: 10px; 
    }

    [x-cloak] { display: none !important; }

    /* 3. PERBAIKAN: Hapus min-height calc yang bikin berantakan */
    main {
        flex: 1 0 auto; /* Memastikan main bisa tumbuh tapi tidak memaksa tinggi statis */
    }
</style>
@endsection
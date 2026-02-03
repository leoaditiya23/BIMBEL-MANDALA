@extends('layouts.app')
@section('title', 'Student Hub - Mandala Bimbel')

@section('content')
{{-- Kontainer Utama --}}
<div x-data="{ sidebarOpen: true }" class="h-screen w-full bg-slate-50 flex overflow-hidden">
    
    @include('siswa.sidebar')

    <div class="flex-1 flex flex-col min-w-0 h-screen overflow-y-auto custom-scrollbar">
        
        <div class="sticky top-0 z-40">
            @include('siswa.header')
        </div>

        <main class="flex-1 p-6 md:p-8">
            @yield('siswa_content')
        </main>

        <footer class="mt-12 py-6 px-8 border-t border-slate-200 bg-white text-slate-500 text-[11px] font-medium flex flex-col md:flex-row justify-between items-center gap-4">
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

<style>
    /* 1. Hilangkan Navbar & Footer Bawaan Landing Page agar tidak double */
    body > nav, 
    body > footer { 
        display: none !important; 
    } 

    /* 2. Matikan scrollbody utama agar tidak ada double scrollbar */
    body {
        overflow: hidden !important;
        height: 100vh;
    }

    /* 3. Desain Scrollbar Tunggal di sisi paling kanan */
    .custom-scrollbar::-webkit-scrollbar {
        width: 8px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f8fafc;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    [x-cloak] { display: none !important; }

    /* Memastikan konten mepet dan bersih */
    main {
        min-height: calc(100vh - 80px); 
    }
</style>
@endsection
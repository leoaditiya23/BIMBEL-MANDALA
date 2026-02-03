@extends('layouts.app')
@section('title', 'Mentor Panel - Mandala')

@section('content')
<div x-data="{ sidebarOpen: true }" class="h-screen w-full bg-slate-50 flex overflow-hidden font-jakarta">
    
    @include('mentor.sidebar')

    <div class="flex-1 flex flex-col min-w-0 h-screen overflow-y-auto custom-main-scroll">
        
        <div class="sticky top-0 z-40">
            @include('mentor.header')
        </div>

        <main class="flex-1 p-6 md:p-8">
            <div class="max-w-7xl mx-auto">
                @yield('mentor_content')
            </div>
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
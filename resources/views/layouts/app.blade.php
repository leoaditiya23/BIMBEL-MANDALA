<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BimbelApp - @yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-white font-sans text-slate-800">
    @if(session('success'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 3000)"
             x-cloak
             class="fixed top-24 left-1/2 -translate-x-1/2 z-50"> 
            <div x-show="show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform -translate-y-4"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-end="opacity-0 transform -translate-y-4"
                 class="bg-green-600 text-white px-6 py-3 rounded-full shadow-2xl flex items-center font-bold whitespace-nowrap">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        </div>
    @endif

    <nav class="flex justify-between items-center px-10 py-5 sticky top-0 bg-white/80 backdrop-blur-md shadow-sm z-50 border-b border-slate-100">
        <div class="text-2xl font-black tracking-tight text-blue-600">
            <a href="{{ route('home') }}" class="flex items-center space-x-2">
                <img src="{{ asset('images/logomandala.png') }}" alt="Logo Mandala" class="h-10 w-auto">
                <span>Mandala<span class="text-orange-500">Bimbel</span></span>
            </a>
        </div>
        
        <div class="hidden md:flex items-center space-x-10 font-bold text-slate-600">
            <a href="{{ route('home') }}" class="hover:text-blue-600 transition relative group">
                Beranda
                <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-blue-600 transition-all group-hover:w-full"></span>
            </a>
            
            <div class="relative group" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                <button class="flex items-center hover:text-blue-600 outline-none transition">
                    Program <i class="fas fa-chevron-down ml-2 text-[10px] transition-transform duration-300" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open" 
                     x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform translate-y-2 scale-95"
                     x-transition:enter-end="opacity-100 transform translate-y-0 scale-100"
                     class="absolute left-1/2 -translate-x-1/2 w-56 bg-white border border-slate-100 shadow-2xl rounded-2xl py-3 mt-0 overflow-hidden">
                    <div class="px-4 py-2 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50 mb-2">Pilihan Belajar</div>
                    <a href="{{ route('program.reguler') }}" class="flex items-center px-6 py-3 hover:bg-blue-50 hover:text-blue-600 transition group/item">
                        <i class="fas fa-book-reader mr-3 text-slate-300 group-hover/item:text-blue-600"></i> Reguler
                    </a>
                    <a href="{{ route('program.intensif') }}" class="flex items-center px-6 py-3 hover:bg-orange-50 hover:text-orange-600 transition group/item">
                        <i class="fas fa-bolt mr-3 text-slate-300 group-hover/item:text-orange-600"></i> Intensif
                    </a>
                </div>
            </div>

            <a href="{{ route('faq.index') }}" class="hover:text-blue-600 transition relative group">
                FAQ
                <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-blue-600 transition-all group-hover:w-full"></span>
            </a>

            <a href="{{ route('about') }}" class="hover:text-blue-600 transition relative group">
                Tentang Kami
                <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-blue-600 transition-all group-hover:w-full"></span>
            </a>

            <a href="{{ route('contact') }}" class="hover:text-blue-600 transition relative group">
                Kontak
                <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-blue-600 transition-all group-hover:w-full"></span>
            </a>
        </div>

        <div class="flex items-center">
            @auth
                <div class="relative" x-data="{ userMenu: false }">
                    <button @click="userMenu = !userMenu" class="flex items-center space-x-3 bg-slate-50 border border-slate-200 pl-2 pr-4 py-1.5 rounded-full hover:bg-white transition shadow-sm">
                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <span class="font-bold text-sm text-slate-700">{{ explode(' ', Auth::user()->name)[0] }}</span>
                        <i class="fas fa-chevron-down text-[10px] text-slate-400"></i>
                    </button>

                    <div x-show="userMenu" 
                         @click.away="userMenu = false"
                         x-cloak
                         x-transition:enter="transition ease-out duration-100"
                         class="absolute right-0 mt-3 w-48 bg-white border border-slate-100 shadow-xl rounded-2xl py-2 overflow-hidden">
                        <div class="px-4 py-2 border-b border-slate-50 mb-1">
                            <p class="text-xs text-slate-400 uppercase font-bold">{{ Auth::user()->role }}</p>
                            <p class="text-sm font-bold truncate">{{ Auth::user()->email }}</p>
                        </div>
                        <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition">
                            <i class="fas fa-th-large mr-2 text-slate-400"></i> Dashboard
                        </a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-500 font-bold hover:bg-red-50 flex items-center transition">
                                <i class="fas fa-sign-out-alt mr-2"></i> Keluar Aplikasi
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="group relative inline-flex items-center justify-center px-8 py-2.5 font-bold text-white transition-all duration-300 bg-blue-600 rounded-full hover:bg-blue-700 shadow-lg shadow-blue-200 hover:shadow-blue-300 active:scale-95">
                    <i class="fas fa-sign-in-alt mr-2 text-sm group-hover:translate-x-1 transition-transform"></i>
                    Login
                </a>
            @endauth
        </div>
    </nav>

    <main class="overflow-hidden">
        @yield('content')
    </main>

    <footer class="bg-slate-900 text-white py-16 px-10 mt-12 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-blue-600/10 rounded-full -mr-32 -mt-32 blur-3xl"></div>
        <div class="container mx-auto grid md:grid-cols-3 gap-10">
            <div class="text-left">
                <div class="text-2xl font-black mb-4">Mandala<span class="text-orange-500">Bimbel</span></div>
                <p class="text-slate-400 text-sm max-w-sm leading-relaxed">Mencetak generasi cerdas dan kompetitif melalui sistem pembelajaran digital yang menyenangkan.</p>
            </div>
            
            <div class="flex flex-col space-y-3">
                <h4 class="font-bold text-white mb-2 underline decoration-orange-500 underline-offset-4">Tautan Cepat</h4>
                <a href="{{ route('about') }}" class="text-slate-400 hover:text-white transition text-sm">Tentang Kami</a>
                <a href="{{ route('contact') }}" class="text-slate-400 hover:text-white transition text-sm">Hubungi Kontak</a>
                <a href="{{ route('faq.index') }}" class="text-slate-400 hover:text-white transition text-sm">Bantuan FAQ</a>
            </div>

            <div class="md:text-right text-slate-500 text-xs flex flex-col justify-end">
                <p class="mb-2">Terdaftar di Kementerian Pendidikan Indonesia</p>
                <p>&copy; 2026 BimbelApp. All Rights Reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
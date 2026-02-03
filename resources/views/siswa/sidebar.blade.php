<div :class="sidebarOpen ? 'w-72' : 'w-20'" 
     class="bg-blue-700 h-screen p-6 text-white flex flex-col shadow-2xl transition-all duration-300 relative z-50 flex-shrink-0 overflow-visible">
    
    <button 
    @click="sidebarOpen = !sidebarOpen" 
    class="absolute -right-4 top-10 bg-orange-500 text-white w-8 h-8 rounded-full flex items-center justify-center shadow-lg z-[60] border-2 border-white focus:outline-none cursor-pointer">
    <i class="fas text-xs transition-transform duration-300" 
       :class="sidebarOpen ? 'fa-angle-left' : 'fa-angle-right'"></i>
</button>

    <div class="flex items-center space-x-3 mb-10 flex-shrink-0 overflow-hidden h-10">
        <div class="w-10 h-10 bg-white rounded-xl flex-shrink-0 flex items-center justify-center shadow-lg">
            <i class="fas fa-graduation-cap text-blue-700 text-lg"></i>
        </div>
        <span x-show="sidebarOpen" x-transition x-cloak class="font-black tracking-tighter text-xl uppercase whitespace-nowrap text-white drop-shadow-sm">
         AREA <span class="text-orange-400">BELAJAR</span>
        </span>
    </div>

    <nav class="space-y-2 flex-grow overflow-y-auto overflow-x-hidden custom-scrollbar">
        <p x-show="sidebarOpen" class="px-3 text-[10px] font-black text-blue-200 uppercase tracking-[0.2em] mb-4 opacity-40">Main Menu</p>
        
        <a href="{{ route('siswa.overview') }}" 
           class="w-full flex items-center p-3.5 rounded-2xl transition-all duration-200 group {{ request()->routeIs('siswa.overview') ? 'bg-white text-blue-700 shadow-xl' : 'hover:bg-blue-600 text-white' }}">
            <div class="w-8 flex justify-center items-center">
                <i class="fas fa-chart-pie text-lg"></i>
            </div>
            <span x-show="sidebarOpen" class="ml-3 font-bold text-sm">Ringkasan</span>
            @if(request()->routeIs('siswa.overview'))
                <i x-show="sidebarOpen" class="fas fa-circle ml-auto text-[6px] text-orange-500"></i>
            @endif
        </a>

        <a href="{{ route('siswa.programs') }}" 
           class="w-full flex items-center p-3.5 rounded-2xl transition-all duration-200 group {{ request()->routeIs('siswa.programs') ? 'bg-white text-blue-700 shadow-xl' : 'hover:bg-blue-600 text-white' }}">
            <div class="w-8 flex justify-center items-center">
                <i class="fas fa-book-open text-lg"></i>
            </div>
            <span x-show="sidebarOpen" class="ml-3 font-bold text-sm">Kelas Saya</span>
            @if(request()->routeIs('siswa.programs'))
                <i x-show="sidebarOpen" class="fas fa-circle ml-auto text-[6px] text-orange-500"></i>
            @endif
        </a>

        <a href="{{ route('siswa.schedule') }}" 
           class="w-full flex items-center p-3.5 rounded-2xl transition-all duration-200 group {{ request()->routeIs('siswa.schedule') ? 'bg-white text-blue-700 shadow-xl' : 'hover:bg-blue-600 text-white' }}">
            <div class="w-8 flex justify-center items-center">
                <i class="fas fa-calendar-alt text-lg"></i>
            </div>
            <span x-show="sidebarOpen" class="ml-3 font-bold text-sm">Jadwal Les</span>
            @if(request()->routeIs('siswa.schedule'))
                <i x-show="sidebarOpen" class="fas fa-circle ml-auto text-[6px] text-orange-500"></i>
            @endif
        </a>

        <a href="{{ route('siswa.billing') }}" 
           class="w-full flex items-center p-3.5 rounded-2xl transition-all duration-200 group {{ request()->routeIs('siswa.billing') ? 'bg-white text-blue-700 shadow-xl' : 'hover:bg-blue-600 text-white' }}">
            <div class="w-8 flex justify-center items-center">
                <i class="fas fa-wallet text-lg"></i>
            </div>
            <span x-show="sidebarOpen" class="ml-3 font-bold text-sm">Pembayaran</span>
            @if(request()->routeIs('siswa.billing'))
                <i x-show="sidebarOpen" class="fas fa-circle ml-auto text-[6px] text-orange-500"></i>
            @endif
        </a>
    </nav>

    <div class="pt-4 border-t border-blue-600/50 flex-shrink-0">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full flex items-center p-3.5 rounded-2xl text-blue-100 hover:bg-orange-500 hover:text-white transition-all group">
                <div class="w-8 flex justify-center items-center">
                    <i class="fas fa-power-off text-lg group-hover:rotate-90 transition-transform duration-300"></i>
                </div>
                <span x-show="sidebarOpen" class="ml-3 font-bold text-sm whitespace-nowrap">Keluar Sesi</span>
            </button>
        </form>
    </div>
</div>
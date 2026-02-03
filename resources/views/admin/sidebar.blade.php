<div :class="sidebarOpen ? 'w-72' : 'w-20'" 
     class="bg-blue-700 h-screen p-6 text-white flex flex-col shadow-2xl transition-all duration-300 relative z-50 flex-shrink-0 overflow-visible">
    
    <button 
        @click="sidebarOpen = !sidebarOpen" 
        class="absolute -right-4 top-10 bg-orange-500 text-white w-8 h-8 rounded-full flex items-center justify-center shadow-lg z-[60] border-2 border-white focus:outline-none cursor-pointer hover:bg-orange-600 transition-colors">
        <i class="fas text-xs transition-transform duration-300" 
           :class="sidebarOpen ? 'fa-angle-left' : 'fa-angle-right'"></i>
    </button>

    <div class="flex items-center space-x-3 mb-10 flex-shrink-0 overflow-hidden h-10">
        <div class="w-10 h-10 bg-white rounded-xl flex-shrink-0 flex items-center justify-center shadow-lg border border-blue-100">
            <i class="fas fa-shield-alt text-blue-700 text-lg"></i>
        </div>
        <span x-show="sidebarOpen" x-transition x-cloak class="font-black tracking-tight text-lg uppercase whitespace-nowrap text-white">
            AREA <span class="text-orange-400">ADMIN</span>
        </span>
    </div>

    <nav class="space-y-2 flex-grow overflow-y-auto overflow-x-hidden custom-scrollbar">
        <p x-show="sidebarOpen" class="px-3 text-[10px] font-black text-blue-200 uppercase tracking-[0.2em] mb-4 opacity-50 uppercase">Navigasi Admin</p>
        
        <a href="{{ route('admin.overview') }}" 
           class="w-full flex items-center p-3.5 rounded-2xl transition-all duration-200 group {{ request()->routeIs('admin.overview') ? 'bg-white text-blue-700 shadow-xl' : 'hover:bg-blue-600 text-white' }}">
            <div class="w-8 flex justify-center items-center">
                <i class="fas fa-chart-pie text-lg"></i>
            </div>
            <span x-show="sidebarOpen" class="ml-3 font-bold text-sm">Overview</span>
            @if(request()->routeIs('admin.overview'))
                <i x-show="sidebarOpen" class="fas fa-circle ml-auto text-[6px] text-orange-500"></i>
            @endif
        </a>

        <a href="{{ route('admin.programs') }}" 
           class="w-full flex items-center p-3.5 rounded-2xl transition-all duration-200 group {{ request()->routeIs('admin.programs') ? 'bg-white text-blue-700 shadow-xl' : 'hover:bg-blue-600 text-white' }}">
            <div class="w-8 flex justify-center items-center">
                <i class="fas fa-layer-group text-lg"></i>
            </div>
            <span x-show="sidebarOpen" class="ml-3 font-bold text-sm">Paket Bimbel</span>
            @if(request()->routeIs('admin.programs'))
                <i x-show="sidebarOpen" class="fas fa-circle ml-auto text-[6px] text-orange-500"></i>
            @endif
        </a>

        <a href="{{ route('admin.mentors') }}" 
           class="w-full flex items-center p-3.5 rounded-2xl transition-all duration-200 group {{ request()->routeIs('admin.mentors') ? 'bg-white text-blue-700 shadow-xl' : 'hover:bg-blue-600 text-white' }}">
            <div class="w-8 flex justify-center items-center">
                <i class="fas fa-user-tie text-lg"></i>
            </div>
            <span x-show="sidebarOpen" class="ml-3 font-bold text-sm">Manajemen Mentor</span>
            @if(request()->routeIs('admin.mentors'))
                <i x-show="sidebarOpen" class="fas fa-circle ml-auto text-[6px] text-orange-500"></i>
            @endif
        </a>

        <a href="{{ route('admin.payments') }}" 
           class="w-full flex items-center p-3.5 rounded-2xl transition-all duration-200 group {{ request()->routeIs('admin.payments') ? 'bg-white text-blue-700 shadow-xl' : 'hover:bg-blue-600 text-white' }}">
            <div class="w-8 flex justify-center items-center">
                <i class="fas fa-check-double text-lg"></i>
            </div>
            <span x-show="sidebarOpen" class="ml-3 font-bold text-sm">Verifikasi Bayar</span>
            @if(request()->routeIs('admin.payments'))
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
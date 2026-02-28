<aside :class="sidebarOpen ? 'w-72' : 'w-20'" class="bg-blue-700 h-full text-white flex flex-col shadow-2xl transition-all duration-300 relative z-50 flex-shrink-0">
    <button @click="sidebarOpen = !sidebarOpen" class="absolute -right-4 top-10 bg-orange-500 text-white w-8 h-8 rounded-full flex items-center justify-center shadow-lg border-2 border-white focus:outline-none cursor-pointer hover:bg-orange-600 transition-colors z-[60]">
        <i class="fas text-xs transition-transform duration-300" :class="sidebarOpen ? 'fa-angle-left' : 'fa-angle-right'"></i>
    </button>

    <div class="p-6 flex items-center space-x-3 h-20 mb-4 flex-shrink-0 relative z-10">
        <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-lg">
            <i class="fas fa-chalkboard-teacher text-blue-700 text-lg"></i>
        </div>
        <span x-show="sidebarOpen" x-transition x-cloak class="font-black tracking-tighter text-lg uppercase whitespace-nowrap text-white">
            AREA <span class="text-orange-400">MENTOR</span>
        </span>
    </div>

    <nav class="flex-grow px-4 space-y-2 overflow-y-auto custom-scrollbar relative z-10">
        <a href="{{ route('mentor.overview') }}" class="flex items-center p-3.5 rounded-2xl transition-all {{ request()->routeIs('mentor.overview') ? 'bg-white text-blue-700 shadow-xl' : 'hover:bg-blue-600 text-white' }}">
            <div class="w-8 flex justify-center"><i class="fas fa-th-large text-lg"></i></div>
            <span x-show="sidebarOpen" class="ml-3 font-bold text-sm">Ringkasan</span>
        </a>

        <a href="{{ route('mentor.classes') }}" class="flex items-center p-3.5 rounded-2xl transition-all {{ request()->routeIs('mentor.classes') ? 'bg-white text-blue-700 shadow-xl' : 'hover:bg-blue-600 text-white' }}">
            <div class="w-8 flex justify-center"><i class="fas fa-book-open text-lg"></i></div>
            <span x-show="sidebarOpen" class="ml-3 font-bold text-sm">Kelas Saya</span>
        </a>

        <a href="{{ route('mentor.schedule') }}" class="flex items-center p-3.5 rounded-2xl transition-all {{ request()->routeIs('mentor.schedule') ? 'bg-white text-blue-700 shadow-xl' : 'hover:bg-blue-600 text-white' }}">
            <div class="w-8 flex justify-center"><i class="fas fa-calendar-alt text-lg"></i></div>
            <span x-show="sidebarOpen" class="ml-3 font-bold text-sm">Jadwal Mengajar</span>
        </a>
    </nav>

   <div class="p-4 px-6 border-t border-blue-600/50 flex-shrink-0 relative z-10">
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
</aside>
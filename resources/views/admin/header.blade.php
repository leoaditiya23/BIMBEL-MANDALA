<header class="h-20 bg-white border-b border-slate-200 flex justify-between items-center px-8 flex-shrink-0 z-40 shadow-sm sticky top-0">
    <div class="flex flex-col">
        <h2 class="text-[10px] font-black text-blue-700 uppercase tracking-[0.2em] mb-1">Mandala Portal</h2>
        <h1 class="text-slate-900 font-extrabold text-xl tracking-tight uppercase">
            @if(request()->routeIs('admin.overview')) Dashboard Overview
            @elseif(request()->routeIs('admin.programs')) Programs Management
            @elseif(request()->routeIs('admin.mentors')) Mentor Directory
            @elseif(request()->routeIs('admin.payments')) Payment Verification
            @else Admin Panel @endif
        </h1>
    </div>

    <div class="flex items-center space-x-6">
        <div class="hidden md:flex flex-col text-right border-r border-slate-200 pr-6">
            <p class="text-sm font-black text-slate-800 leading-none">{{ Auth::user()->name ?? 'Administrator' }}</p>
            <p class="text-[10px] text-orange-500 font-bold uppercase mt-1 tracking-tighter">Super Admin Access</p>
        </div>
        
        <div class="relative">
            <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'Admin' }}&background=1d4ed8&color=fff&bold=true" 
                 class="w-11 h-11 rounded-2xl shadow-lg border-2 border-white object-cover" />
            <span class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-white rounded-full shadow-sm"></span>
        </div>
    </div>
</header>
<header class="bg-white p-6 border-b border-slate-200 sticky top-0 z-40 flex justify-between items-center">
    <h1 class="text-xl font-black text-slate-800">Student Dashboard</h1>
    <div class="flex items-center space-x-4">
        <div class="text-right">
            <p class="text-sm font-black text-slate-800">{{ Auth::user()->name ?? 'Student' }}</p>
            <p class="text-xs text-orange-500 font-bold">Pelajar Mandala</p>
        </div>
        <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=f97316&color=fff" class="w-10 h-10 rounded-lg border-2 border-orange-100">
    </div>
</header>

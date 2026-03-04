@extends('siswa.dashboard_siswa_layout')

@section('siswa_content')
<div x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4">
    <div class="mb-8">
        <h2 class="text-3xl font-black text-slate-800 tracking-tighter">Gas Terus, {{ explode(' ', Auth::user()->name)[0] }}! 🔥</h2>
        <p class="text-sm text-slate-500 mt-1 font-medium">Pantau progres belajarmu di sini</p>
    </div>

    {{-- Stat Cards Section --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        {{-- Kehadiran --}}
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <p class="text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest italic group-hover:text-blue-500 transition-colors">Kehadiran</p>
                    <p class="text-3xl font-black text-blue-600">{{ $stats['attendance'] ?? 0 }}%</p>
                </div>
                <div class="w-10 h-10 bg-blue-50 rounded-2xl flex items-center justify-center group-hover:rotate-12 transition-transform text-blue-500">
                    <i class="fas fa-check-circle text-lg"></i>
                </div>
            </div>
            <div class="w-full bg-slate-50 h-1.5 mt-4 rounded-full overflow-hidden">
                <div class="bg-blue-500 h-full rounded-full" style="width: {{ $stats['attendance'] ?? 0 }}%"></div>
            </div>
        </div>

        {{-- Tugas Selesai --}}
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <p class="text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest italic group-hover:text-orange-500 transition-colors">Tugas Selesai</p>
                    <p class="text-3xl font-black text-orange-600">{{ $stats['completed_tasks'] ?? 0 }}<span class="text-sm text-slate-400 font-medium ml-1">/{{ $stats['total_tasks'] ?? 0 }}</span></p>
                </div>
                <div class="w-10 h-10 bg-orange-50 rounded-2xl flex items-center justify-center group-hover:rotate-12 transition-transform text-orange-500">
                    <i class="fas fa-tasks text-lg"></i>
                </div>
            </div>
            @php
                $task_progress = ($stats['total_tasks'] ?? 0) > 0 ? (($stats['completed_tasks'] ?? 0) / $stats['total_tasks']) * 100 : 0;
            @endphp
            <div class="w-full bg-slate-50 h-1.5 mt-4 rounded-full overflow-hidden">
                <div class="bg-orange-500 h-full rounded-full" style="width: {{ $task_progress }}%"></div>
            </div>
        </div>

        {{-- Nilai Rata-rata (Persen) --}}
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <p class="text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest italic group-hover:text-indigo-500 transition-colors">Rata-rata Nilai</p>
                    {{-- Diperbaiki agar benar-benar nol jika data rata-rata tidak ada --}}
                    <p class="text-3xl font-black text-indigo-600">
                        {{ number_format($stats['average_score'] ?? 0, 0) }}%
                    </p>
                </div>
                <div class="w-10 h-10 bg-indigo-50 rounded-2xl flex items-center justify-center group-hover:rotate-12 transition-transform text-indigo-500">
                    <i class="fas fa-chart-line text-lg"></i>
                </div>
            </div>
            <div class="w-full bg-slate-50 h-1.5 mt-4 rounded-full overflow-hidden shadow-inner">
                {{-- Bar progres mengikuti data rata-rata secara presisi --}}
                <div class="bg-indigo-500 h-full rounded-full transition-all duration-1000 shadow-[0_0_8px_rgba(79,70,229,0.4)]" 
                     style="width: {{ number_format($stats['average_score'] ?? 0, 0) }}%"></div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Program Aktif --}}
        <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100">
            <h3 class="font-black text-slate-800 mb-6 uppercase flex items-center text-sm tracking-widest">
                <i class="fas fa-book-open mr-3 text-blue-500"></i> Program Aktif
            </h3>
            
            @if($recent_programs && count($recent_programs) > 0)
                <div class="space-y-4">
                    @foreach($recent_programs as $program)
                        <div class="bg-slate-50/50 p-5 rounded-2xl border border-slate-100 hover:border-blue-200 transition-all group">
                            <div class="flex justify-between items-start mb-2">
                                <p class="font-black text-slate-800 group-hover:text-blue-600 transition-colors">{{ $program->name }}</p>
                                <span class="px-2 py-0.5 rounded-full bg-blue-100 text-[8px] font-black text-blue-600 uppercase">Aktif</span>
                            </div>
                            <p class="text-xs text-slate-500 mb-4">Mentor: <span class="font-bold text-slate-700">{{ $program->mentor_name ?? '-' }}</span></p>
                            
                            <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-blue-500 h-full rounded-full transition-all duration-1000" style="width: 0%"></div> 
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-10">
                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-inbox text-slate-300 text-2xl"></i>
                    </div>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">Belum ada program aktif</p>
                </div>
            @endif
        </div>

        {{-- Aktivitas Terbaru --}}
        <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100">
            <h3 class="font-black text-slate-800 mb-6 uppercase flex items-center text-sm tracking-widest">
                <i class="fas fa-clock mr-3 text-orange-500"></i> Aktivitas Terbaru
            </h3>
            
            <div class="space-y-6">
                @forelse($activities as $activity)
                    <div class="flex items-start space-x-4 group">
                        <div class="relative">
                            <div class="w-3 h-3 {{ $activity->status == 'verified' ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.4)]' : 'bg-orange-500 shadow-[0_0_8px_rgba(249,115,22,0.4)]' }} rounded-full mt-1.5 flex-shrink-0 z-10 relative"></div>
                            @if(!$loop->last)
                                <div class="absolute top-5 left-1.5 w-[1px] h-10 bg-slate-100"></div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <p class="font-bold text-slate-800 text-sm group-hover:text-blue-600 transition-colors">{{ $activity->title }}</p>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-[9px] text-slate-400 uppercase font-black tracking-tighter">{{ $activity->type }}</span>
                                <span class="text-[9px] font-black uppercase {{ $activity->status == 'verified' ? 'text-emerald-500' : 'text-orange-500' }} tracking-tighter">{{ $activity->status }}</span>
                            </div>
                            <p class="text-[10px] text-slate-400 mt-1 font-medium italic">{{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10">
                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-history text-slate-300 text-2xl"></i>
                        </div>
                        <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">Belum ada aktivitas</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
@extends('siswa.dashboard_siswa_layout')

@section('siswa_content')
<div x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4">
    <div class="mb-8">
        <h2 class="text-3xl font-black text-slate-800">Gas Terus, {{ explode(' ', Auth::user()->name)[0] }}! 🔥</h2>
        <p class="text-sm text-slate-500">Pantau progres belajarmu di sini</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-lg transition">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <p class="text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest italic">Kehadiran</p>
                    <p class="text-3xl font-black text-blue-600">{{ $stats['attendance'] ?? 0 }}%</p>
                </div>
                <i class="fas fa-check-circle text-blue-500 text-3xl opacity-20"></i>
            </div>
        </div>

        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-lg transition">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <p class="text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest italic">Tugas Selesai</p>
                    <p class="text-3xl font-black text-orange-600">{{ $stats['completed_tasks'] ?? 0 }}<span class="text-sm text-slate-500">/{{ $stats['total_tasks'] ?? 0 }}</span></p>
                </div>
                <i class="fas fa-tasks text-orange-500 text-3xl opacity-20"></i>
            </div>
        </div>

        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-lg transition">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <p class="text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest italic">Nilai Rata-rata</p>
                    <p class="text-3xl font-black text-green-600">{{ $stats['average_score'] ?? 0 }}</p>
                </div>
                <i class="fas fa-star text-green-500 text-3xl opacity-20"></i>
            </div>
        </div>

        <div class="bg-blue-600 p-6 rounded-[2rem] shadow-lg text-white">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <p class="text-[10px] font-black text-blue-200 uppercase mb-2 tracking-widest italic">Peringkat Kelas</p>
                    <p class="text-3xl font-black italic">#{{ $stats['class_rank'] ?? 'New' }}</p>
                </div>
                <i class="fas fa-trophy text-blue-300 text-3xl opacity-40"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100">
            <h3 class="font-black text-slate-800 mb-6 uppercase flex items-center">
                <i class="fas fa-book-open mr-3 text-blue-500"></i> Program Aktif
            </h3>
            
            @if($recent_programs && count($recent_programs) > 0)
                <div class="space-y-4">
                    @foreach($recent_programs as $program)
                        <div class="bg-slate-50 p-4 rounded-xl border-l-4 border-blue-500">
                            <p class="font-black text-slate-800">{{ $program->name }}</p>
                            <p class="text-sm text-slate-600">Mentor: {{ $program->mentor_name ?? '-' }}</p>
                            <div class="mt-2 w-full bg-slate-200 h-2 rounded-full overflow-hidden">
                                <div class="bg-blue-500 h-full w-[0%]"></div> </div>
                            <p class="text-xs text-slate-500 mt-1">Status: Aktif</p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-6">
                    <i class="fas fa-inbox text-slate-300 text-3xl mb-2"></i>
                    <p class="text-slate-500 text-sm">Belum ada program aktif</p>
                </div>
            @endif
        </div>

        <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100">
            <h3 class="font-black text-slate-800 mb-6 uppercase flex items-center">
                <i class="fas fa-clock mr-3 text-orange-500"></i> Aktivitas Terbaru
            </h3>
            
            <div class="space-y-4">
                @forelse($activities as $activity)
                    <div class="flex items-start space-x-4">
                        <div class="w-3 h-3 {{ $activity->status == 'verified' ? 'bg-green-500' : 'bg-orange-500' }} rounded-full mt-2 flex-shrink-0"></div>
                        <div class="flex-1">
                            <p class="font-bold text-slate-800">{{ $activity->title }}</p>
                            <p class="text-[10px] text-slate-400 uppercase font-black">{{ $activity->type }} • {{ $activity->status }}</p>
                            <p class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6">
                        <i class="fas fa-history text-slate-300 text-3xl mb-2"></i>
                        <p class="text-slate-500 text-sm">Belum ada aktivitas terbaru</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
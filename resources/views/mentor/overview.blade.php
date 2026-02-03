@extends('mentor.dashboard_mentor_layout')

@section('mentor_content')
<div x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4">
    <div class="mb-8">
        <h2 class="text-3xl font-black text-slate-800">Halo, {{ explode(' ', Auth::user()->name)[0] }}! 👋</h2>
        <p class="text-sm text-slate-500">Berikut adalah ringkasan aktivitas mengajar Anda</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-lg transition">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <p class="text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest italic">Siswa Aktif</p>
                    <p class="text-3xl font-black text-indigo-600">{{ $stats['total_students'] ?? 0 }}</p>
                </div>
                <i class="fas fa-users text-indigo-500 text-3xl opacity-20"></i>
            </div>
            <div class="w-full bg-indigo-50 h-1 mt-4 rounded-full overflow-hidden">
                <div class="bg-indigo-500 h-full w-[70%]"></div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-lg transition">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <p class="text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest italic">Kelas Aktif</p>
                    <p class="text-3xl font-black text-orange-600">{{ $stats['total_classes'] ?? 0 }}</p>
                </div>
                <i class="fas fa-chalkboard text-orange-500 text-3xl opacity-20"></i>
            </div>
            <div class="w-full bg-orange-50 h-1 mt-4 rounded-full overflow-hidden">
                <div class="bg-orange-500 h-full w-[60%]"></div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-lg transition">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <p class="text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest italic">Sesi Hari Ini</p>
                    <p class="text-3xl font-black text-green-600">{{ $stats['today_sessions'] ?? 0 }}</p>
                </div>
                <i class="fas fa-calendar-day text-green-500 text-3xl opacity-20"></i>
            </div>
            <div class="w-full bg-green-50 h-1 mt-4 rounded-full overflow-hidden">
                <div class="bg-green-500 h-full w-[50%]"></div>
            </div>
        </div>
    </div>

    <!-- Jadwal Mengajar Hari Ini & Penugasan -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Jadwal Hari Ini -->
        <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100">
            <h3 class="font-black text-slate-800 mb-6 uppercase flex items-center">
                <i class="fas fa-calendar-day mr-3 text-indigo-500"></i> Jadwal Hari Ini
            </h3>
            
            @if($today_schedule && count($today_schedule) > 0)
                <div class="space-y-4">
                    @foreach($today_schedule as $jadwal)
                        <div class="border-l-4 border-indigo-500 pl-4 py-2">
                            <p class="font-black text-slate-800">{{ $jadwal->program_name }}</p>
                            <p class="text-sm text-slate-600">Siswa: {{ $jadwal->student_name }}</p>
                            <p class="text-xs text-indigo-500 font-bold mt-1">
                                <i class="fas fa-clock"></i> {{ $jadwal->created_at->format('H:i') }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-6">
                    <i class="fas fa-inbox text-slate-300 text-3xl mb-2"></i>
                    <p class="text-slate-500 text-sm">Tidak ada jadwal hari ini</p>
                </div>
            @endif
        </div>

        <!-- Penugasan Siswa -->
        <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100">
            <h3 class="font-black text-slate-800 mb-6 uppercase flex items-center justify-between">
                <span><i class="fas fa-tasks mr-3 text-orange-500"></i> Penugasan</span>
                <button class="text-[10px] font-black text-indigo-500 uppercase hover:text-indigo-700">+ Buat Tugas</button>
            </h3>
            
            @if($assignments && count($assignments) > 0)
                <div class="space-y-4">
                    @foreach($assignments as $assignment)
                        <div class="bg-slate-50 p-4 rounded-xl">
                            <p class="font-bold text-slate-800">{{ $assignment->title ?? 'Tugas Baru' }}</p>
                            <p class="text-xs text-slate-500 mt-1">Dibuat: {{ $assignment->created_at->format('d M Y') }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-6">
                    <i class="fas fa-inbox text-slate-300 text-3xl mb-2"></i>
                    <p class="text-slate-500 text-sm">Belum ada penugasan</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

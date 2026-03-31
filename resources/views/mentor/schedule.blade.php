@extends('mentor.dashboard_mentor_layout')

@section('mentor_content')
{{-- Gunakan flex-col dan flex-1 agar konten mendorong footer ke bawah --}}
<div x-data="{ activeTab: '{{ \Carbon\Carbon::now()->locale('id')->dayName }}' }" class="min-h-screen pb-12 flex flex-col">
    
    <div class="mb-10 flex flex-col sm:flex-row sm:items-end justify-between gap-6 border-b border-slate-100 pb-8">
        <div>
            <h2 class="text-3xl font-black text-slate-800 tracking-tighter">Timeline Mengajar</h2>
            <p class="text-sm text-slate-500 mt-1 font-medium">Sesi Belajar Terjadwal</p>
        </div>
        
        <div class="flex items-center gap-3 bg-slate-50 px-5 py-3 rounded-2xl border border-slate-200/60">
            <div class="flex flex-col items-end">
                <span class="text-[9px] font-black text-orange-500 uppercase tracking-[0.2em] leading-none mb-1">Hari Ini</span>
                <span class="text-sm font-bold text-slate-700 leading-none">
                    {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d M Y') }}
                </span>
            </div>
            <div class="w-[2px] h-8 bg-orange-200 rounded-full"></div>
            <div class="text-orange-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" />
                </svg>
            </div>
        </div>
    </div>

    <div class="mb-10 w-full flex justify-center overflow-x-auto no-scrollbar">
        <div class="inline-flex bg-slate-100/80 p-2 rounded-[3.5rem] border border-slate-200/50 shadow-inner">
            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $hari)
                <button @click="activeTab = '{{ $hari }}'" 
                    :class="activeTab === '{{ $hari }}' 
                        ? 'bg-orange-500 shadow-[0_10px_20px_-5px_rgba(249,115,22,0.4)] text-white scale-105' 
                        : 'text-slate-500 hover:text-orange-500 hover:bg-white/60'"
                    class="px-10 py-4 rounded-[3rem] text-[11px] font-black transition-all duration-300 whitespace-nowrap uppercase tracking-[0.1em]">
                    {{ $hari }}
                </button>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 flex-1">
        @php
            $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        @endphp

        @foreach($days as $day)
            <div x-show="activeTab === '{{ $day }}'" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 class="space-y-4">
                
                @php
                    $daySchedule = $schedule->filter(function($item) use ($day) {
                        return trim(strtolower($item->hari ?? '')) === trim(strtolower($day));
                    });
                @endphp

                @forelse($daySchedule as $item)
                    @php
                        $now = \Carbon\Carbon::now()->locale('id');
                        $isToday = trim(strtolower($now->dayName)) === trim(strtolower($day));
                        
                        $isLive = false;
                        if($isToday && $item->jam_mulai != '--:--') {
                            try {
                                $startTime = \Carbon\Carbon::createFromFormat('H:i', $item->jam_mulai);
                                $endTime = (clone $startTime)->addMinutes(90);
                                $isLive = $now->between($startTime, $endTime);
                            } catch (\Exception $e) { $isLive = false; }
                        }
                    @endphp

                    <div class="group relative bg-white rounded-[2rem] p-2 border transition-all duration-300 {{ $isLive ? 'border-green-500 ring-4 ring-green-50 shadow-xl' : 'border-slate-100 shadow-sm hover:shadow-xl hover:border-indigo-100' }}">
                        @if($isLive)
                            <div class="absolute -top-3 left-8 px-4 py-1 bg-green-500 text-white text-[8px] font-black uppercase tracking-widest rounded-full animate-bounce shadow-lg shadow-green-200">
                                Sedang Berlangsung
                            </div>
                        @endif

                        <div class="flex flex-col md:flex-row md:items-center gap-6 p-4">
                            <div class="flex md:flex-col items-center justify-center rounded-[1.5rem] py-4 px-6 transition-colors duration-300 {{ $isLive ? 'bg-green-600' : 'bg-slate-50 group-hover:bg-indigo-600' }}">
                                <span class="text-[10px] font-black uppercase tracking-widest md:mb-1 {{ $isLive ? 'text-green-200' : 'text-slate-400 group-hover:text-indigo-200' }}">Mulai</span>
                                <span class="text-xl font-black {{ $isLive ? 'text-white' : 'text-slate-800 group-hover:text-white' }}">{{ $item->jam_mulai }}</span>
                                <span class="text-[10px] font-bold ml-2 md:ml-0 {{ $isLive ? 'text-green-200' : 'text-slate-400 group-hover:text-indigo-200' }}">WIB</span>
                            </div>

                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter {{ $isLive ? 'bg-green-100 text-green-700' : 'bg-indigo-50 text-indigo-600' }}">
                                        {{ $item->jenjang ?? 'Reguler' }}
                                    </span>
                                    <div class="h-1 w-1 rounded-full bg-slate-300"></div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest italic">Verified</span>
                                </div>
                                <h3 class="text-xl font-black text-slate-800 transition-colors mb-1 {{ $isLive ? 'text-green-700' : 'group-hover:text-indigo-600' }}">
                                    {{ $item->program_name }}
                                </h3>
                                {{-- REVISI: MENAMPILKAN KELAS DAN NAMA SISWA --}}
                                <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                                    <div class="flex items-center gap-1.5 text-slate-500">
                                        <i class="fas fa-user-graduate text-xs {{ $isLive ? 'text-green-500' : 'text-indigo-500' }}"></i>
                                        <span class="text-sm font-bold text-slate-600">{{ $item->student_name }}</span>
                                    </div>
                                    <span class="px-2 py-0.5 bg-blue-50 text-blue-600 text-[9px] font-black rounded uppercase border border-blue-100">
                                        KELAS: {{ $item->kelas ?? '-' }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center gap-4 border-t md:border-t-0 md:border-l border-slate-100 pt-4 md:pt-0 md:pl-8">
                                <div class="text-right hidden lg:block mr-4 max-w-[200px]">
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Cabang / Alamat</p>
                                    <p class="text-xs font-bold text-slate-600">{{ $item->lokasi_cabang ?? '-' }}</p>
                                    
                                    @if(!empty($item->alamat_siswa))
                                        <p class="text-[9px] text-slate-400 mt-1 leading-tight italic">
                                            <i class="fas fa-map-marker-alt text-rose-400 mr-1"></i> {{ $item->alamat_siswa }}
                                        </p>
                                    @endif
                                </div>
                                
                                <a href="{{ route('mentor.classes', ['id' => $item->id, 'session' => 'active']) }}" 
                                   class="flex-1 md:flex-none px-8 py-4 rounded-[1.2rem] text-[10px] font-black uppercase tracking-widest transition-all duration-300 text-decoration-none text-center shadow-lg {{ $isLive ? 'bg-green-600 text-white hover:bg-slate-900 shadow-green-100' : 'bg-indigo-600 text-white hover:bg-slate-900 shadow-indigo-100' }}">
                                    {{ $isLive ? 'Masuk Kelas Sekarang' : 'Mulai Mengajar' }}
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-20 text-center bg-slate-50/50 rounded-[3rem] border-2 border-dashed border-slate-200">
                        <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm text-slate-300">
                            <i class="fas fa-calendar-times text-2xl"></i>
                        </div>
                        <h4 class="text-lg font-black text-slate-800">Tidak ada jadwal</h4>
                        <p class="text-sm text-slate-500 font-medium">Hari {{ $day }} belum ada siswa terdaftar.</p>
                    </div>
                @endforelse
            </div>
        @endforeach
    </div>
</div>

<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    .text-decoration-none { text-decoration: none !important; }
</style>
@endsection
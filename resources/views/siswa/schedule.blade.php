@extends('siswa.dashboard_siswa_layout')

@section('siswa_content')
<div x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4">
    <div class="mb-8">
        <h2 class="text-3xl font-black text-slate-800 tracking-tighter">Jadwal Les</h2>
        <p class="text-sm text-slate-500 mt-1 font-medium">Daftar lengkap jadwal pembelajaran Anda</p>
    </div>

    <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
        {{-- Variabel disesuaikan menjadi $schedules sesuai compact di Controller --}}
        @if(isset($schedules) && count($schedules) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($schedules as $item)
                    <div class="bg-gradient-to-r from-blue-50 to-transparent p-6 rounded-xl border-l-4 border-blue-500 hover:shadow-lg transition">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex-1">
                                <p class="font-black text-slate-800 text-lg">{{ $item->program_name }}</p>
                                <p class="text-sm text-slate-600 mt-1">
                                    <i class="fas fa-user mr-2 text-orange-500"></i>
                                    {{ $item->mentor_name ?? 'Mentor Belum Ditentukan' }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-slate-500 font-bold uppercase tracking-tighter italic">Hari</p>
                                <p class="text-sm font-black text-blue-600">{{ $item->hari ?? 'TBA' }}</p>
                            </div>
                        </div>
                        
                        <div class="flex flex-wrap items-center gap-4 text-sm">
                            <div class="flex items-center text-slate-600 bg-white px-3 py-1 rounded-full border border-slate-100">
                                <i class="fas fa-clock mr-2 text-blue-500"></i>
                                {{ $item->jam_mulai ?? '--:--' }} - {{ $item->jam_selesai ?? '--:--' }}
                            </div>
                            <div class="flex items-center text-slate-600 bg-white px-3 py-1 rounded-full border border-slate-100">
                                <i class="fas fa-map-marker-alt mr-2 text-green-500"></i>
                                {{ ($item->metode ?? 'online') === 'offline' ? 'Tatap Muka' : 'Online / Zoom' }}
                            </div>
                        </div>

                        <div class="mt-6 flex gap-3">
                            <button class="flex-1 bg-blue-600 text-white hover:bg-blue-700 py-2 rounded-xl font-bold text-sm transition shadow-sm">
                                <i class="fas fa-sign-in-alt mr-1"></i> Masuk Kelas
                            </button>
                            <button class="flex-1 bg-slate-100 text-slate-600 hover:bg-slate-200 py-2 rounded-xl font-bold text-sm transition">
                                <i class="fas fa-info-circle mr-1"></i> Detail
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-16">
                <div class="relative inline-block mb-4">
                    <i class="fas fa-calendar-times text-slate-200 text-6xl"></i>
                    <i class="fas fa-search text-slate-400 text-xl absolute bottom-0 right-0"></i>
                </div>
                <p class="text-slate-500 font-bold text-lg">Belum Ada Jadwal Kelas</p>
                <p class="text-slate-400 text-sm mt-1">Jadwal akan muncul setelah pendaftaran program diverifikasi admin.</p>
            </div>
        @endif
    </div>
</div>
@endsection
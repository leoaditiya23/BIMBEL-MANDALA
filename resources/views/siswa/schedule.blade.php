@extends('siswa.dashboard_siswa_layout')

@section('siswa_content')
<div x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4">
    <div class="mb-8">
        <h2 class="text-3xl font-black text-slate-800">Jadwal Les</h2>
        <p class="text-sm text-slate-500">Daftar lengkap jadwal pembelajaran Anda</p>
    </div>

    <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
        @if($schedule && count($schedule) > 0)
            <div class="space-y-4">
                @foreach($schedule as $item)
                    <div class="bg-gradient-to-r from-blue-50 to-transparent p-6 rounded-xl border-l-4 border-blue-500 hover:shadow-lg transition">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex-1">
                                <p class="font-black text-slate-800 text-lg">{{ $item->program_name }}</p>
                                <p class="text-sm text-slate-600 mt-1">
                                    <i class="fas fa-user mr-2 text-orange-500"></i>
                                    {{ $item->mentor_name ?? 'Mentor' }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-slate-500 font-bold">Jenjang</p>
                                <p class="text-sm font-black text-slate-800">{{ $item->jenjang ?? '-' }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-6 text-sm">
                            <div class="flex items-center text-slate-600">
                                <i class="fas fa-calendar-alt mr-2 text-blue-500"></i>
                                {{ $item->created_at->format('d M Y') }}
                            </div>
                            <div class="flex items-center text-slate-600">
                                <i class="fas fa-map-marker-alt mr-2 text-green-500"></i>
                                {{ $item->metode === 'offline' ? 'Offline' : 'Online' }}
                            </div>
                        </div>

                        <div class="mt-4 flex gap-2">
                            <button class="flex-1 text-blue-600 hover:text-blue-800 font-bold text-sm">Masuk Kelas</button>
                            <button class="flex-1 text-slate-600 hover:text-slate-800 font-bold text-sm">Detail</button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-inbox text-slate-300 text-4xl mb-4"></i>
                <p class="text-slate-500 font-semibold">Tidak ada jadwal</p>
            </div>
        @endif
    </div>
</div>
@endsection

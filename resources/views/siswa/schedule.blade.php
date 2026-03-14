@extends('siswa.dashboard_siswa_layout')

@section('siswa_content')
<div x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4">
    {{-- Header Section: Judul dan Badge Sejajar --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black text-slate-800 tracking-tighter">Jadwal Les</span></h2>
            <p class="text-sm text-slate-500 mt-1 font-medium">Daftar agenda pembelajaran resmi Anda minggu ini.</p>
        </div>

        <div class="flex items-center">
            <span class="px-4 py-2 bg-indigo-50 text-indigo-600 rounded-2xl text-[10px] font-black uppercase tracking-widest border border-indigo-100 flex items-center shadow-sm">
                <i class="fas fa-calendar-check mr-2 text-xs"></i> 
                {{ count($schedules ?? []) }} Kelas Aktif
            </span>
        </div>
    </div>

    {{-- Grid Card Section --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
        @if(isset($schedules) && count($schedules) > 0)
            @foreach($schedules as $item)
                <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm hover:shadow-2xl transition-all duration-500 overflow-hidden group">
                    {{-- Header Card --}}
                    <div class="p-8 pb-4">
                        <div class="flex justify-between items-start mb-6">
                            <div class="flex-1">
                                <h3 class="text-xl font-black text-slate-800 uppercase tracking-tight group-hover:text-indigo-600 transition-colors">
                                    {{ $item->display_mapel ?? ($item->mapel ?? $item->program_name) }}
                                </h3>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">PROGRAM {{ $item->jenjang ?? 'PILIHAN' }}</p>
                            </div>
                            <span class="px-3 py-1 rounded-lg {{ ($item->metode == 'DARING (ONLINE)') ? 'bg-blue-50 text-blue-600' : 'bg-emerald-50 text-emerald-600' }} text-[9px] font-black uppercase italic">
                                {{ $item->metode ?? 'OFFLINE' }}
                            </span>
                        </div>

                        {{-- Info Details Grid --}}
                        <div class="space-y-4 mb-6">
                            {{-- Mentor --}}
                            <div class="flex items-center gap-4 p-3 bg-slate-50 rounded-2xl border border-slate-100/50">
                                <div class="w-8 h-8 rounded-xl bg-white shadow-sm flex items-center justify-center text-orange-500 text-xs">
                                    <i class="fas fa-chalkboard-teacher"></i>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-slate-400 uppercase leading-none">Mentor Pengajar</p>
                                    <p class="text-xs font-bold text-slate-700 mt-1">{{ $item->mentor_name ?? 'Menunggu Mentor' }}</p>
                                </div>
                            </div>

                            {{-- Waktu --}}
                            <div class="flex items-center gap-4 p-3 bg-slate-50 rounded-2xl border border-slate-100/50">
                                <div class="w-8 h-8 rounded-xl bg-white shadow-sm flex items-center justify-center text-indigo-500 text-xs">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-slate-400 uppercase leading-none">Jadwal & Jam</p>
                                    <p class="text-xs font-bold text-slate-700 mt-1">{{ $item->jadwal_detail ?? 'Belum Ditentukan' }}</p>
                                </div>
                            </div>

                            {{-- Lokasi --}}
                            <div class="flex items-center gap-4 p-3 bg-slate-50 rounded-2xl border border-slate-100/50">
                                <div class="w-8 h-8 rounded-xl bg-white shadow-sm flex items-center justify-center text-rose-500 text-xs">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-slate-400 uppercase leading-none">Keterangan Lokasi</p>
                                    <p class="text-xs font-bold text-slate-700 mt-1 truncate max-w-[200px]">
                                        {{ $item->lokasi_display ?? 'Koordinasi via Mentor' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Action Footer --}}
                    <div class="px-8 pb-8 flex gap-3">
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $item->mentor_wa ?? '628123456789') }}?text=Halo%20{{ $item->mentor_name }},%20saya%20ingin%20koordinasi%20jadwal%20{{ $item->display_mapel }}" 
                           target="_blank" 
                           class="flex-1 py-3 bg-emerald-500 text-white text-center rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-600 hover:-translate-y-0.5 transition-all shadow-lg shadow-emerald-100">
                            <i class="fab fa-whatsapp mr-2 text-sm"></i> Hubungi Mentor
                        </a>
                        
                        <button onclick="Swal.fire({
                            title: 'Info Pembelajaran',
                            text: 'Silakan koordinasi dengan mentor melalui WhatsApp untuk persiapan materi atau konfirmasi kehadiran minimal 30 menit sebelum sesi dimulai.',
                            icon: 'info',
                            confirmButtonColor: '#4f46e5',
                            customClass: { popup: 'rounded-[2rem]' }
                        })" class="px-5 py-3 bg-slate-100 text-slate-400 rounded-xl hover:bg-slate-200 hover:text-slate-600 transition-all">
                            <i class="fas fa-info-circle"></i>
                        </button>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-span-full py-20 text-center bg-white rounded-[3rem] border border-dashed border-slate-200">
                <div class="relative inline-block mb-6">
                    <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto shadow-inner">
                        <i class="fas fa-calendar-alt text-slate-200 text-4xl"></i>
                    </div>
                </div>
                <p class="text-slate-500 font-black uppercase tracking-widest text-sm">Belum Ada Jadwal Pelajaran</p>
                <p class="text-slate-400 text-xs mt-2 italic font-medium">Jadwal resmi akan diterbitkan setelah pendaftaran Anda diverifikasi admin.</p>
            </div>
        @endif
    </div>
</div>
@endsection

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
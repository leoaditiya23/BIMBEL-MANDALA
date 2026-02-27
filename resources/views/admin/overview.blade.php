@extends('admin.dashboard_admin')

@section('admin_content')
    {{-- Menambahkan pb-20 agar ada ruang di bawah --}}
    <div class="w-full pb-20" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-data="{ statusFilter: 'semua' }">
        
        <h2 class="text-3xl font-black text-slate-800 mb-8 tracking-tighter">Ringkasan <span class="text-slate-800">Dashboard</span></h2>
        
        {{-- Stat Cards Section --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            {{-- Total Pendapatan --}}
            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <p class="text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest italic group-hover:text-green-500 transition-colors">Total Pendapatan</p>
                        <p class="text-2xl font-black text-green-600">Rp {{ number_format($stats['total_pendapatan'] ?? 0, 0, ',', '.') }}</p>
                    </div>
                    <div class="w-10 h-10 bg-green-50 rounded-2xl flex items-center justify-center group-hover:rotate-12 transition-transform text-green-500">
                        <i class="fas fa-wallet text-lg"></i>
                    </div>
                </div>
                <div class="w-full bg-slate-50 h-1.5 mt-4 rounded-full overflow-hidden">
                    <div class="bg-green-500 h-full rounded-full" style="width: 100%"></div>
                </div>
            </div>
            
            {{-- Total Siswa --}}
            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <p class="text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest italic group-hover:text-blue-500 transition-colors">Siswa Terverifikasi</p>
                        <p class="text-2xl font-black text-blue-600">{{ $stats['total_siswa'] }} <span class="text-xs text-slate-400 font-medium">Orang</span></p>
                    </div>
                    <div class="w-10 h-10 bg-blue-50 rounded-2xl flex items-center justify-center group-hover:rotate-12 transition-transform text-blue-500">
                        <i class="fas fa-user-graduate text-lg"></i>
                    </div>
                </div>
                <div class="w-full bg-slate-50 h-1.5 mt-4 rounded-full overflow-hidden">
                    <div class="bg-blue-500 h-full rounded-full" style="width: 100%"></div>
                </div>
            </div>

            {{-- Total Mentor --}}
            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <p class="text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest italic group-hover:text-purple-500 transition-colors">Total Mentor</p>
                        <p class="text-2xl font-black text-purple-600">{{ $stats['total_mentor'] }} <span class="text-xs text-slate-400 font-medium">Aktif</span></p>
                    </div>
                    <div class="w-10 h-10 bg-purple-50 rounded-2xl flex items-center justify-center group-hover:rotate-12 transition-transform text-purple-500">
                        <i class="fas fa-chalkboard-teacher text-lg"></i>
                    </div>
                </div>
                <div class="w-full bg-slate-50 h-1.5 mt-4 rounded-full overflow-hidden">
                    <div class="bg-purple-500 h-full rounded-full" style="width: 100%"></div>
                </div>
            </div>

            {{-- Total Program --}}
            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <p class="text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest italic group-hover:text-orange-500 transition-colors">Total Program</p>
                        <p class="text-2xl font-black text-orange-600">{{ $stats['total_program'] }} <span class="text-xs text-slate-400 font-medium">Tersedia</span></p>
                    </div>
                    <div class="w-10 h-10 bg-orange-50 rounded-2xl flex items-center justify-center group-hover:rotate-12 transition-transform text-orange-500">
                        <i class="fas fa-layer-group text-lg"></i>
                    </div>
                </div>
                <div class="w-full bg-slate-50 h-1.5 mt-4 rounded-full overflow-hidden">
                    <div class="bg-orange-500 h-full rounded-full" style="width: 100%"></div>
                </div>
            </div>
        </div>

        {{-- Menambahkan min-h agar tabel memanjang ke bawah dan menutup area abu-abu --}}
        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden flex flex-col mb-0 min-h-[600px]">
            <div class="p-8 border-b border-slate-50 flex flex-col md:flex-row justify-between items-center gap-4 flex-shrink-0">
                <div>
                    <h3 class="text-lg font-bold text-slate-800 tracking-tight">Pendaftaran Terbaru</h3>
                    <p class="text-xs text-slate-400 mt-1">Sistem sinkronisasi otomatis</p>
                </div>

                <div class="flex items-center gap-2 bg-slate-50 p-1 rounded-xl">
                    <button @click="statusFilter = 'semua'" :class="statusFilter === 'semua' ? 'bg-white shadow-sm text-blue-600' : 'text-slate-400'" class="px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all">Semua</button>
                    <button @click="statusFilter = 'verified'" :class="statusFilter === 'verified' ? 'bg-white shadow-sm text-emerald-600' : 'text-slate-400'" class="px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all">Verified</button>
                    <button @click="statusFilter = 'pending'" :class="statusFilter === 'pending' ? 'bg-white shadow-sm text-amber-600' : 'text-slate-400'" class="px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all">Pending</button>
                    <button @click="statusFilter = 'rejected'" :class="statusFilter === 'rejected' ? 'bg-white shadow-sm text-rose-600' : 'text-slate-400'" class="px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all">Ditolak</button>
                </div>

                <a href="{{ route('admin.payments') }}" class="text-blue-600 hover:text-blue-700 text-xs font-bold flex items-center transition-colors">
                    Verifikasi Pembayaran <i class="fas fa-chevron-right ml-2 text-[10px]"></i>
                </a>
            </div>
            
            <div class="flex-1 overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="sticky top-0 z-10 bg-white">
                        <tr class="bg-slate-50/50">
                            <th class="py-4 px-8 text-[11px] font-black text-slate-400 uppercase tracking-widest">Siswa</th>
                            <th class="py-4 px-6 text-[11px] font-black text-slate-400 uppercase tracking-widest">Jenjang & Program</th>
                            <th class="py-4 px-6 text-center text-[11px] font-black text-slate-400 uppercase tracking-widest">Nominal</th>
                            <th class="py-4 px-6 text-center text-[11px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                            <th class="py-4 px-8 text-right text-[11px] font-black text-slate-400 uppercase tracking-widest">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($recent_enrollments as $enrollment)
    <tr x-show="statusFilter === 'semua' || statusFilter === '{{ $enrollment->status_pembayaran }}'" class="hover:bg-slate-50/80 transition-colors group">
        <td class="py-4 px-8">
            <div class="flex items-center">
                <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mr-3 border border-slate-200">
                    <i class="fas fa-user text-[10px]"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-700 leading-tight">{{ $enrollment->user_name }}</p>
                    <p class="text-[9px] text-slate-400">#UID-{{ $enrollment->user_id }}</p>
                </div>
            </div>
        </td>
        <td class="py-4 px-6">
            <div class="flex items-center gap-2">
                {{-- Badge Jenjang: Background Hitam, Teks Putih --}}
                <span class="px-2 py-0.5 rounded-md bg-slate-900 text-[8px] font-black text-white uppercase tracking-tighter shadow-sm flex-shrink-0">
                    {{ $enrollment->program_jenjang }}
                </span>
                {{-- Teks Program: Slate 800 (Gelap) agar kontras dengan putih --}}
                <p class="text-xs font-bold text-slate-800 tracking-tight leading-none">
                    {{ $enrollment->program_name }}
                </p>
            </div>
        </td>
        <td class="py-4 px-6 text-center text-xs font-bold text-slate-700">
            Rp {{ number_format($enrollment->total_harga, 0, ',', '.') }}
        </td>
        <td class="py-4 px-6 text-center">
            @if($enrollment->status_pembayaran === 'verified')
                <span class="inline-flex items-center px-2 py-0.5 rounded-full font-black text-[8px] uppercase bg-emerald-50 text-emerald-600 border border-emerald-100">Verified</span>
            @elseif($enrollment->status_pembayaran === 'rejected')
                <span class="inline-flex items-center px-2 py-0.5 rounded-full font-black text-[8px] uppercase bg-rose-50 text-rose-600 border border-rose-100">Ditolak</span>
            @else
                <span class="inline-flex items-center px-2 py-0.5 rounded-full font-black text-[8px] uppercase bg-amber-50 text-amber-600 border border-amber-100">Pending</span>
            @endif
        </td>
        <td class="py-4 px-8 text-right">
            <p class="text-xs font-medium text-slate-500">{{ \Carbon\Carbon::parse($enrollment->created_at)->format('d/m/Y') }}</p>
        </td>
    </tr>
@endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-8 py-3 bg-white border-t border-slate-50 flex items-center justify-between flex-shrink-0">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                    Showing {{ $recent_enrollments->firstItem() ?? 0 }}-{{ $recent_enrollments->lastItem() ?? 0 }} of {{ $recent_enrollments->total() }}
                </div>
                
                <div class="flex items-center gap-1">
                    @if ($recent_enrollments->onFirstPage())
                        <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 text-slate-300 cursor-not-allowed text-xs"><i class="fas fa-chevron-left"></i></span>
                    @else
                        <a href="{{ $recent_enrollments->previousPageUrl() }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-blue-600 hover:bg-blue-600 hover:text-white transition-all text-xs"><i class="fas fa-chevron-left"></i></a>
                    @endif

                    <div class="px-3 py-1 rounded-lg bg-blue-50 text-blue-700 font-black text-[10px]">
                        {{ $recent_enrollments->currentPage() }}
                    </div>

                    @if ($recent_enrollments->hasMorePages())
                        <a href="{{ $recent_enrollments->nextPageUrl() }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-blue-600 hover:bg-blue-600 hover:text-white transition-all text-xs"><i class="fas fa-chevron-right"></i></a>
                    @else
                        <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 text-slate-300 cursor-not-allowed text-xs"><i class="fas fa-chevron-right"></i></span>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
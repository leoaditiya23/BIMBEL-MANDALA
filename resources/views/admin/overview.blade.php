@extends('admin.dashboard_admin')

@section('admin_content')
    {{-- REVISI: Hapus p-8, ganti jadi w-full agar sejajar lurus dengan header --}}
    <div class="w-full" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4">
        
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
            
            {{-- Total Siswa (Verified) --}}
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

        {{-- Recent Enrollments Table --}}
        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
            <div class="p-8 border-b border-slate-50 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-bold text-slate-800 tracking-tight">Pendaftaran Terbaru</h3>
                    <p class="text-xs text-slate-400 mt-1">Sistem sinkronisasi otomatis</p>
                </div>
                <a href="{{ route('admin.payments') }}" class="text-blue-600 hover:text-blue-700 text-xs font-bold flex items-center transition-colors">
                    Verifikasi Pembayaran <i class="fas fa-chevron-right ml-2 text-[10px]"></i>
                </a>
            </div>
            
            @if($recent_enrollments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th class="py-4 px-8 text-[11px] font-black text-slate-400 uppercase tracking-widest">Siswa</th>
                                <th class="py-4 px-6 text-[11px] font-black text-slate-400 uppercase tracking-widest">Program</th>
                                <th class="py-4 px-6 text-center text-[11px] font-black text-slate-400 uppercase tracking-widest">Nominal</th>
                                <th class="py-4 px-6 text-center text-[11px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                                <th class="py-4 px-8 text-right text-[11px] font-black text-slate-400 uppercase tracking-widest">Waktu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($recent_enrollments as $enrollment)
                                <tr class="hover:bg-slate-50/80 transition-colors group">
                                    <td class="py-5 px-8">
                                        <div class="flex items-center">
                                            <div class="w-9 h-9 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mr-3 border border-slate-200 group-hover:border-blue-200 transition-colors">
                                                <i class="fas fa-user text-xs group-hover:text-blue-500"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-slate-700">{{ $enrollment->user_name }}</p>
                                                <p class="text-[10px] text-slate-400">#UID-{{ $enrollment->user_id }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-5 px-6">
                                        <p class="text-sm font-medium text-slate-600">{{ $enrollment->program_name }}</p>
                                    </td>
                                    <td class="py-5 px-6 text-center">
                                        <span class="text-sm font-bold text-slate-700">Rp {{ number_format($enrollment->total_harga, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="py-5 px-6 text-center">
                                        @if($enrollment->status_pembayaran === 'verified')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full font-black text-[9px] uppercase bg-emerald-50 text-emerald-600 border border-emerald-100">
                                                Verified
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full font-black text-[9px] uppercase bg-amber-50 text-amber-600 border border-amber-100">
                                                Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-5 px-8 text-right text-slate-500">
                                        <p class="text-sm font-medium">{{ \Carbon\Carbon::parse($enrollment->created_at)->format('d/m/Y') }}</p>
                                        <p class="text-[10px] text-slate-400 italic">{{ \Carbon\Carbon::parse($enrollment->created_at)->diffForHumans() }}</p>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-20">
                    <i class="fas fa-database text-slate-200 text-4xl mb-4"></i>
                    <p class="text-slate-400 text-sm font-medium">Belum ada data pendaftaran yang masuk.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
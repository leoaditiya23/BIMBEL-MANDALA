@extends('admin.dashboard_admin')

@section('admin_content')
    <div class="p-8" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4">
        <h2 class="text-3xl font-black text-slate-800 mb-8 tracking-tighter">Ringkasan <span class="text-blue-600">Dashboard</span></h2>
        
        {{-- Stat Cards Section --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            {{-- Total Pendapatan --}}
            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group cursor-default">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <p class="text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest italic group-hover:text-green-500 transition-colors">Total Pendapatan</p>
                        <p class="text-2xl font-black text-green-600">Rp {{ number_format($stats['total_pendapatan'] ?? 0, 0, ',', '.') }}</p>
                    </div>
                    <div class="w-10 h-10 bg-green-50 rounded-2xl flex items-center justify-center group-hover:rotate-12 transition-transform">
                        <i class="fas fa-wallet text-green-500 text-lg"></i>
                    </div>
                </div>
                <div class="w-full bg-slate-50 h-1.5 mt-4 rounded-full overflow-hidden">
                    <div class="bg-green-500 h-full w-[75%] rounded-full shadow-[0_0_8px_rgba(34,197,94,0.4)]"></div>
                </div>
            </div>
            
            {{-- Total Siswa --}}
            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group cursor-default">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <p class="text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest italic group-hover:text-blue-500 transition-colors">Total Siswa</p>
                        <p class="text-2xl font-black text-blue-600">{{ $stats['total_siswa'] ?? 0 }} <span class="text-xs text-slate-400 font-medium">Orang</span></p>
                    </div>
                    <div class="w-10 h-10 bg-blue-50 rounded-2xl flex items-center justify-center group-hover:rotate-12 transition-transform">
                        <i class="fas fa-user-graduate text-blue-500 text-lg"></i>
                    </div>
                </div>
                <div class="w-full bg-slate-50 h-1.5 mt-4 rounded-full overflow-hidden">
                    <div class="bg-blue-500 h-full w-[60%] rounded-full shadow-[0_0_8px_rgba(59,130,246,0.4)]"></div>
                </div>
            </div>

            {{-- Total Mentor --}}
            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group cursor-default">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <p class="text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest italic group-hover:text-purple-500 transition-colors">Total Mentor</p>
                        <p class="text-2xl font-black text-purple-600">{{ $stats['total_mentor'] ?? 0 }} <span class="text-xs text-slate-400 font-medium">Aktif</span></p>
                    </div>
                    <div class="w-10 h-10 bg-purple-50 rounded-2xl flex items-center justify-center group-hover:rotate-12 transition-transform">
                        <i class="fas fa-chalkboard-teacher text-purple-500 text-lg"></i>
                    </div>
                </div>
                <div class="w-full bg-slate-50 h-1.5 mt-4 rounded-full overflow-hidden">
                    <div class="bg-purple-500 h-full w-[85%] rounded-full shadow-[0_0_8px_rgba(168,85,247,0.4)]"></div>
                </div>
            </div>

            {{-- Total Program --}}
            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group cursor-default">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <p class="text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest italic group-hover:text-orange-500 transition-colors">Total Program</p>
                        <p class="text-2xl font-black text-orange-600">{{ $stats['total_program'] ?? 0 }} <span class="text-xs text-slate-400 font-medium">Tersedia</span></p>
                    </div>
                    <div class="w-10 h-10 bg-orange-50 rounded-2xl flex items-center justify-center group-hover:rotate-12 transition-transform">
                        <i class="fas fa-layer-group text-orange-500 text-lg"></i>
                    </div>
                </div>
                <div class="w-full bg-slate-50 h-1.5 mt-4 rounded-full overflow-hidden">
                    <div class="bg-orange-500 h-full w-[50%] rounded-full shadow-[0_0_8px_rgba(249,115,22,0.4)]"></div>
                </div>
            </div>
        </div>

        {{-- Recent Enrollments Table --}}
        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
            <div class="p-8 border-b border-slate-50 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-bold text-slate-800 tracking-tight">Pendaftaran Terbaru</h3>
                    <p class="text-xs text-slate-400 mt-1">Kelola data pendaftaran masuk hari ini</p>
                </div>
                <a href="{{ route('admin.payments') }}" class="text-blue-600 hover:text-blue-700 text-xs font-bold flex items-center transition-colors">
                    Lihat Laporan Lengkap <i class="fas fa-chevron-right ml-2 text-[10px]"></i>
                </a>
            </div>
            
            @if(isset($recent_enrollments) && count($recent_enrollments) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th class="text-left py-4 px-8 text-[11px] font-black text-slate-400 uppercase tracking-widest">Siswa</th>
                                <th class="text-left py-4 px-6 text-[11px] font-black text-slate-400 uppercase tracking-widest">Program</th>
                                <th class="text-center py-4 px-6 text-[11px] font-black text-slate-400 uppercase tracking-widest">Nominal</th>
                                <th class="text-center py-4 px-6 text-[11px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                                <th class="text-right py-4 px-8 text-[11px] font-black text-slate-400 uppercase tracking-widest">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($recent_enrollments as $enrollment)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-5 px-8">
                                        <div class="flex items-center">
                                            <div class="w-9 h-9 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mr-3 border border-slate-200">
                                                <i class="fas fa-user text-xs"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-slate-700">{{ $enrollment->user_name }}</p>
                                                <p class="text-[10px] text-slate-400">#{{ $enrollment->id }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="py-5 px-6">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-medium text-slate-600">{{ $enrollment->program_name }}</span>
                                            <span class="text-[9px] font-bold uppercase {{ ($enrollment->metode ?? '') === 'offline' ? 'text-blue-500' : 'text-slate-400' }}">
                                                {{ $enrollment->metode ?? 'Online' }}
                                            </span>
                                        </div>
                                    </td>

                                    <td class="py-5 px-6 text-center">
                                        <span class="text-sm font-bold text-slate-700">Rp {{ number_format($enrollment->total_harga ?? 0, 0, ',', '.') }}</span>
                                    </td>

                                    <td class="py-5 px-6 text-center">
                                        <div class="flex justify-center">
                                            @if($enrollment->status_pembayaran === 'verified')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full font-black text-[9px] uppercase tracking-widest bg-emerald-100 text-emerald-600 border border-emerald-200">
                                                    <i class="fas fa-check-circle mr-1"></i> Verified
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1 rounded-full font-black text-[9px] uppercase tracking-widest bg-amber-100 text-amber-600 border border-amber-200">
                                                    <i class="fas fa-clock mr-1"></i> Pending
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="py-5 px-8 text-right text-slate-500">
                                        <p class="text-sm font-medium">{{ \Carbon\Carbon::parse($enrollment->created_at)->format('d M Y') }}</p>
                                        <p class="text-[10px] text-slate-400 italic">{{ \Carbon\Carbon::parse($enrollment->created_at)->diffForHumans() }}</p>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-16">
                    <i class="fas fa-inbox text-slate-200 text-4xl mb-4"></i>
                    <p class="text-slate-400 text-sm font-medium">Belum ada data pendaftaran.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
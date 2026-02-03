@extends('admin.dashboard_admin')

@section('admin_content')
    <div class="p-8" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4">
        <h2 class="text-3xl font-black text-slate-800 mb-8">Ringkasan</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <!-- Total Pendapatan -->
            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-lg transition">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <p class="text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest italic">Total Pendapatan</p>
                        <p class="text-3xl font-black text-green-600">Rp {{ number_format($stats['total_pendapatan'] ?? 0, 0, ',', '.') }}</p>
                    </div>
                    <i class="fas fa-coins text-green-500 text-3xl opacity-20"></i>
                </div>
                <div class="w-full bg-green-50 h-1 mt-4 rounded-full overflow-hidden">
                    <div class="bg-green-500 h-full w-[70%]"></div>
                </div>
            </div>
            
            <!-- Total Siswa -->
            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-lg transition">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <p class="text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest italic">Total Siswa</p>
                        <p class="text-3xl font-black text-blue-600">{{ $stats['total_siswa'] ?? 0 }}</p>
                    </div>
                    <i class="fas fa-users text-blue-500 text-3xl opacity-20"></i>
                </div>
                <div class="w-full bg-blue-50 h-1 mt-4 rounded-full overflow-hidden">
                    <div class="bg-blue-500 h-full w-[60%]"></div>
                </div>
            </div>
            
            <!-- Total Mentor -->
            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-lg transition">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <p class="text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest italic">Total Mentor</p>
                        <p class="text-3xl font-black text-purple-600">{{ $stats['total_mentor'] ?? 0 }}</p>
                    </div>
                    <i class="fas fa-chalkboard-teacher text-purple-500 text-3xl opacity-20"></i>
                </div>
                <div class="w-full bg-purple-50 h-1 mt-4 rounded-full overflow-hidden">
                    <div class="bg-purple-500 h-full w-[80%]"></div>
                </div>
            </div>
            
            <!-- Total Program -->
            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-lg transition">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <p class="text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest italic">Total Program</p>
                        <p class="text-3xl font-black text-orange-600">{{ $stats['total_program'] ?? 0 }}</p>
                    </div>
                    <i class="fas fa-box text-orange-500 text-3xl opacity-20"></i>
                </div>
                <div class="w-full bg-orange-50 h-1 mt-4 rounded-full overflow-hidden">
                    <div class="bg-orange-500 h-full w-[50%]"></div>
                </div>
            </div>
        </div>

        <!-- Recent Enrollments -->
        <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
            <h3 class="text-xl font-black text-slate-800 mb-6">Pendaftaran Terbaru</h3>
            
            @if($recent_enrollments && count($recent_enrollments) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b-2 border-slate-100">
                                <th class="text-left py-4 px-4 font-black text-slate-600 text-sm">Nama Siswa</th>
                                <th class="text-left py-4 px-4 font-black text-slate-600 text-sm">Program</th>
                                <th class="text-left py-4 px-4 font-black text-slate-600 text-sm">Total Harga</th>
                                <th class="text-left py-4 px-4 font-black text-slate-600 text-sm">Status</th>
                                <th class="text-left py-4 px-4 font-black text-slate-600 text-sm">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recent_enrollments as $enrollment)
                                <tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                                    <td class="py-4 px-4 text-slate-700 font-semibold">{{ $enrollment->user_name }}</td>
                                    <td class="py-4 px-4 text-slate-700">{{ $enrollment->program_name }}</td>
                                    <td class="py-4 px-4 font-black text-green-600">Rp {{ number_format($enrollment->total_harga ?? 0, 0, ',', '.') }}</td>
                                    <td class="py-4 px-4">
                                        @if($enrollment->status_pembayaran === 'verified')
                                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-lg text-xs font-bold">Verifikasi</span>
                                        @else
                                            <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-lg text-xs font-bold">Menunggu</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-slate-500 text-sm">{{ $enrollment->created_at->format('d M Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-inbox text-slate-300 text-4xl mb-4"></i>
                    <p class="text-slate-500 font-semibold">Belum ada pendaftaran</p>
                </div>
            @endif
        </div>
    </div>
@endsection
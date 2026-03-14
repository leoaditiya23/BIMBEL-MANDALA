@extends('admin.dashboard_admin')

@section('admin_content')
    <div class="w-full pb-20 relative z-10" 
         x-transition:enter="transition ease-out duration-300" 
         x-transition:enter-start="opacity-0 translate-y-4" 
         x-data="{ statusFilter: 'semua', showModalJadwal: false, selectedEnrollment: null }">
        
        <h2 class="text-3xl font-black text-slate-800 tracking-tighter">Ringkasan <span class="text-slate-800">Dashboard</span></h2>
        <p class="text-sm text-slate-500 mt-1 font-medium mb-8">Lihat ringkasan data dan statistik penting aplikasi Mandala.</p>
        
        {{-- Stat Cards Section --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
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

        {{-- Section Tabel --}}
        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden flex flex-col mb-0 min-h-[600px] relative">
            <div class="p-8 border-b border-slate-50 flex flex-col md:flex-row justify-between items-center gap-4 flex-shrink-0">
                <div>
                    <h3 class="text-lg font-bold text-slate-800 tracking-tight">Pendaftaran Terbaru</h3>
                    <p class="text-xs text-slate-400 mt-1">Pantau lokasi dan pilihan jadwal siswa</p>
                </div>

                <div class="flex items-center gap-2 bg-slate-50 p-1 rounded-xl">
                    <button @click="statusFilter = 'semua'" :class="statusFilter === 'semua' ? 'bg-white shadow-sm text-blue-600' : 'text-slate-400'" class="px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all">Semua</button>
                    <button @click="statusFilter = 'verified'" :class="statusFilter === 'verified' ? 'bg-white shadow-sm text-emerald-600' : 'text-slate-400'" class="px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all">Verified</button>
                    <button @click="statusFilter = 'pending'" :class="statusFilter === 'pending' ? 'bg-white shadow-sm text-amber-600' : 'text-slate-400'" class="px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all">Pending</button>
                </div>

                <a href="{{ route('admin.payments') }}" class="text-blue-600 hover:text-blue-700 text-xs font-bold flex items-center transition-colors">
                    Verifikasi Pembayaran <i class="fas fa-chevron-right ml-2 text-[10px]"></i>
                </a>
            </div>
            
            <div class="flex-1 overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="sticky top-0 z-10 bg-white">
                        <tr class="bg-slate-50/50">
                            <th class="py-4 px-8 text-[11px] font-black text-slate-400 uppercase tracking-widest">Siswa & Lokasi</th>
                            <th class="py-4 px-6 text-[11px] font-black text-slate-400 uppercase tracking-widest">Program</th>
                            <th class="py-4 px-6 text-[11px] font-black text-slate-400 uppercase tracking-widest text-center">Detail Pertemuan</th>
                            <th class="py-4 px-6 text-center text-[11px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                            <th class="py-4 px-8 text-right text-[11px] font-black text-slate-400 uppercase tracking-widest">Penugasan</th>
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
                                        
                                        {{-- REVISI LOKASI: Pengecekan Aman dengan operator null coalescing ?? --}}
                                        <p class="text-[10px] font-bold mt-0.5 uppercase {{ ($enrollment->is_online ?? false) ? 'text-indigo-600' : 'text-rose-600' }}">
                                            <i class="fas {{ ($enrollment->is_online ?? false) ? 'fa-video' : 'fa-house-user' }} text-[9px] mr-1"></i>
                                            {{ $enrollment->display_lokasi ?? ($enrollment->alamat_siswa ?: 'Lokasi Tidak Terdeteksi') }}
                                        </p>

                                        @if(!($enrollment->is_online ?? false) && !empty($enrollment->lokasi_cabang))
                                            <p class="text-[8px] text-slate-400 font-black uppercase tracking-tighter italic">Cabang: {{ $enrollment->lokasi_cabang }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="py-4 px-6">
                                <span class="px-2 py-0.5 rounded-md bg-slate-900 text-[8px] font-black text-white uppercase tracking-tighter">{{ $enrollment->program_jenjang }}</span>
                                {{-- REVISI PROGRAM: Menampilkan nama mapel asli --}}
                                <p class="text-xs font-bold text-slate-800 mt-1 uppercase tracking-tight">{{ $enrollment->display_program ?? ($enrollment->mapel ?? $enrollment->program_name) }}</p>
                                @if(($enrollment->is_mengaji ?? 0) == 1)
                                    <span class="text-[8px] font-black text-emerald-600 uppercase italic">+ Ambil Ngaji</span>
                                @endif
                            </td>

                            <td class="py-4 px-6 text-center">
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black text-slate-700 italic">
                                        {{ $enrollment->jadwal_detail ?: 'Belum Pilih Jadwal' }}
                                    </span>
                                    <div class="flex items-center justify-center gap-2 mt-1">
                                        <span class="text-[8px] bg-slate-100 px-1.5 py-0.5 rounded text-slate-500 font-bold uppercase">
                                            {{ $enrollment->per_minggu ?? '0' }}x Pertemuan
                                        </span>
                                        
                                        <span class="text-[8px] px-1.5 py-0.5 rounded font-bold uppercase {{ ($enrollment->is_online ?? false) ? 'bg-indigo-50 text-indigo-600' : 'bg-orange-50 text-orange-600' }}">
                                            {{ strtoupper($enrollment->metode) }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full font-black text-[8px] uppercase {{ ($enrollment->status_pembayaran ?? '') === 'verified' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                                    {{ $enrollment->status_pembayaran ?? 'pending' }}
                                </span>
                            </td>
                            <td class="py-4 px-8 text-right">
                                <button @click="showModalJadwal = true; selectedEnrollment = {{ json_encode($enrollment) }}" 
                                        class="px-4 py-2 bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-indigo-700 transition-all shadow-sm">
                                    {{ ($enrollment->mentor_id ?? null) ? 'Ganti Mentor' : 'Pilih Mentor' }}
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-8 py-3 bg-white border-t border-slate-50 flex items-center justify-between flex-shrink-0">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                    Total {{ $recent_enrollments->total() }} Data
                </div>
                <div class="flex items-center gap-1">
                    {{ $recent_enrollments->links() }}
                </div>
            </div>
        </div>

        {{-- MODAL PILIH MENTOR --}}
        <div x-show="showModalJadwal" 
             class="fixed inset-0 z-[99] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
             x-cloak>
            <div class="bg-white w-full max-w-md rounded-[2.5rem] p-10 shadow-2xl" @click.away="showModalJadwal = false">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-black text-slate-800 tracking-tighter">Tugaskan <span class="text-indigo-600">Mentor</span></h3>
                    <button @click="showModalJadwal = false" class="text-slate-400 hover:text-rose-500"><i class="fas fa-times"></i></button>
                </div>

                <div class="bg-slate-50 p-4 rounded-2xl mb-6 border border-slate-100">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Pilihan Jadwal Siswa:</p>
                    <p class="text-xs font-bold text-slate-700" x-text="selectedEnrollment?.jadwal_detail || selectedEnrollment?.jadwal_pertemuan || '-'"></p>
                </div>

                <form :action="'/admin/enrollment/update-jadwal/' + selectedEnrollment?.id" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <input type="hidden" name="jadwal_pertemuan" :value="selectedEnrollment?.jadwal_detail || selectedEnrollment?.jadwal_pertemuan">
                    
                    <div class="space-y-4">
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Pilih Mentor Tersedia</label>
                            <select name="mentor_id" class="w-full mt-2 p-4 bg-slate-50 border-none rounded-2xl text-xs font-bold focus:ring-2 focus:ring-indigo-500">
                                <option value="">-- Pilih Mentor --</option>
                                @foreach($mentors as $m)
                                    <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->specialization ?? 'Umum' }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full mt-8 py-4 bg-indigo-600 text-white font-black uppercase text-xs tracking-[0.2em] rounded-2xl shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all">Konfirmasi Penugasan</button>
                </form>
            </div>
        </div>
    </div>
@endsection
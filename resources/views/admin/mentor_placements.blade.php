@extends('admin.dashboard_admin')

@section('admin_content')
<div class="w-full pb-20 relative z-10" x-data="{ statusFilter: '{{ $statusFilter ?? 'butuh_aksi' }}' }">
    <div class="mb-8 flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black text-slate-800 tracking-tighter">Penempatan <span class="text-slate-800">Mentor</span></h2>
            <p class="text-sm text-slate-500 mt-1 font-medium">Pusat pengaturan penugasan siswa ke mentor, lengkap dengan validasi tabrakan jadwal dan status approval mentor.</p>
        </div>
        <div class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-400">
            <span class="px-3 py-1.5 rounded-full bg-slate-100">Flow: Admin Assign</span>
            <i class="fas fa-arrow-right text-slate-300"></i>
            <span class="px-3 py-1.5 rounded-full bg-amber-50 text-amber-600">Mentor Review</span>
            <i class="fas fa-arrow-right text-slate-300"></i>
            <span class="px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-600">Aktif Mengajar</span>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-2xl bg-emerald-500 text-white text-sm font-bold shadow-lg shadow-emerald-100">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 rounded-2xl bg-rose-500 text-white text-sm font-bold shadow-lg shadow-rose-100">
            <i class="fas fa-triangle-exclamation mr-2"></i>{{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-500 text-white text-xs font-bold">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl p-4 border border-slate-100">
            <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Butuh Aksi</p>
            <p class="text-2xl font-black text-slate-800 mt-1">{{ ($placementStats['pending'] ?? 0) + ($placementStats['unassigned'] ?? 0) + ($placementStats['rejected'] ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-slate-100">
            <p class="text-[10px] font-black uppercase text-amber-500 tracking-widest">Pending Mentor</p>
            <p class="text-2xl font-black text-amber-600 mt-1">{{ $placementStats['pending'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-slate-100">
            <p class="text-[10px] font-black uppercase text-rose-500 tracking-widest">Perlu Reassign</p>
            <p class="text-2xl font-black text-rose-600 mt-1">{{ ($placementStats['unassigned'] ?? 0) + ($placementStats['rejected'] ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-slate-100">
            <p class="text-[10px] font-black uppercase text-emerald-500 tracking-widest">Sudah Aktif</p>
            <p class="text-2xl font-black text-emerald-600 mt-1">{{ $placementStats['approved'] ?? 0 }}</p>
        </div>
    </div>

    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.mentor_placements', ['status' => 'butuh_aksi']) }}" class="px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest {{ ($statusFilter ?? 'butuh_aksi') === 'butuh_aksi' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-500 hover:text-slate-700' }}">Butuh Aksi</a>
                <a href="{{ route('admin.mentor_placements', ['status' => 'pending']) }}" class="px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest {{ ($statusFilter ?? '') === 'pending' ? 'bg-amber-500 text-white' : 'bg-amber-50 text-amber-600 hover:bg-amber-100' }}">Pending</a>
                <a href="{{ route('admin.mentor_placements', ['status' => 'rejected']) }}" class="px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest {{ ($statusFilter ?? '') === 'rejected' ? 'bg-rose-500 text-white' : 'bg-rose-50 text-rose-600 hover:bg-rose-100' }}">Rejected</a>
                <a href="{{ route('admin.mentor_placements', ['status' => 'approved']) }}" class="px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest {{ ($statusFilter ?? '') === 'approved' ? 'bg-emerald-500 text-white' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' }}">Approved</a>
                <a href="{{ route('admin.mentor_placements', ['status' => 'unassigned']) }}" class="px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest {{ ($statusFilter ?? '') === 'unassigned' ? 'bg-indigo-600 text-white' : 'bg-indigo-50 text-indigo-600 hover:bg-indigo-100' }}">Belum Mentor</a>
            </div>
            <p class="text-xs text-slate-400 font-bold">Total {{ $placements->total() }} data</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left">
                <thead class="bg-slate-50/80">
                    <tr>
                        <th class="py-4 px-6 text-[11px] font-black uppercase tracking-widest text-slate-400">Siswa</th>
                        <th class="py-4 px-4 text-[11px] font-black uppercase tracking-widest text-slate-400">Program & Jadwal</th>
                        <th class="py-4 px-4 text-[11px] font-black uppercase tracking-widest text-slate-400">Status</th>
                        <th class="py-4 px-4 text-[11px] font-black uppercase tracking-widest text-slate-400">Aksi Penempatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($placements as $item)
                        <tr class="align-top hover:bg-slate-50/60 transition-colors">
                            <td class="py-4 px-6">
                                <p class="text-sm font-black text-slate-800 leading-tight">{{ $item->student_name }}</p>
                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mt-1">{{ $item->program_jenjang ?? '-' }} • Kelas {{ $item->kelas ?? '-' }}</p>
                                <p class="text-[10px] text-slate-500 mt-2">Lokasi: {{ strtoupper($item->metode ?? 'offline') }} {{ !empty($item->lokasi_cabang) ? ' - ' . $item->lokasi_cabang : '' }}</p>
                            </td>
                            <td class="py-4 px-4">
                                <p class="text-xs font-bold text-slate-700 uppercase">{{ $item->program_name }}</p>
                                <p class="text-[10px] text-slate-500 mt-1 leading-relaxed">{{ $item->jadwal_detail ?: 'Jadwal belum diisi siswa.' }}</p>
                                @if(!empty($item->potential_conflicts))
                                    <div class="mt-2 p-2 rounded-lg bg-rose-50 border border-rose-100">
                                        <p class="text-[9px] font-black uppercase tracking-widest text-rose-600 mb-1">Potensi Tabrakan</p>
                                        @foreach(array_slice($item->potential_conflicts, 0, 2) as $conflict)
                                            <p class="text-[10px] text-rose-600">{{ $conflict['student_name'] }} - {{ $conflict['day'] }} {{ $conflict['time_range'] }}</p>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="py-4 px-4">
                                @php
                                    $status = $item->mentor_assignment_status ?? 'unassigned';
                                @endphp

                                @if($status === 'approved')
                                    <span class="inline-flex px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-600 text-[9px] font-black uppercase">Approved</span>
                                @elseif($status === 'pending')
                                    <span class="inline-flex px-2.5 py-1 rounded-full bg-amber-50 text-amber-600 text-[9px] font-black uppercase">Pending Mentor</span>
                                @elseif($status === 'rejected')
                                    <span class="inline-flex px-2.5 py-1 rounded-full bg-rose-50 text-rose-600 text-[9px] font-black uppercase">Rejected</span>
                                @else
                                    <span class="inline-flex px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 text-[9px] font-black uppercase">Belum Mentor</span>
                                @endif

                                <p class="text-[10px] text-slate-500 mt-2">
                                    Mentor: <span class="font-bold text-slate-700">{{ $item->mentor_name ?? '-' }}</span>
                                </p>

                                @if(!empty($item->mentor_assignment_note))
                                    <p class="text-[10px] mt-1 text-rose-500">Catatan: {{ $item->mentor_assignment_note }}</p>
                                @endif
                            </td>
                            <td class="py-4 px-4">
                                <form method="POST" action="{{ route('admin.mentor_placements.assign', $item->id) }}" class="space-y-2">
                                    @csrf
                                    <select name="mentor_id" class="w-full p-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500" required>
                                        <option value="">-- Pilih Mentor --</option>
                                        @foreach($mentors as $mentor)
                                            <option value="{{ $mentor->id }}" {{ (int) ($item->mentor_id ?? 0) === (int) $mentor->id ? 'selected' : '' }}>
                                                {{ $mentor->name }}{{ !empty($mentor->specialization) ? ' - ' . $mentor->specialization : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="w-full py-2.5 rounded-xl bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest hover:bg-indigo-700 transition">
                                        {{ ($item->mentor_id ?? null) ? 'Kirim Ulang Penempatan' : 'Kirim Penempatan' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-16 text-center">
                                <i class="fas fa-user-check text-3xl text-slate-200 mb-2"></i>
                                <p class="text-xs uppercase tracking-widest font-black text-slate-400">Tidak ada data pada filter ini</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-slate-100 bg-white">
            {{ $placements->links() }}
        </div>
    </div>
</div>
@endsection

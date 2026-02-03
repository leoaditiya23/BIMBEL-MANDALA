@extends('mentor.dashboard_mentor_layout')

@section('mentor_content')
<div x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4">
    <div class="mb-8">
        <h2 class="text-3xl font-black text-slate-800">Jadwal Mengajar</h2>
        <p class="text-sm text-slate-500">Daftar lengkap jadwal mengajar Anda</p>
    </div>

    <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
        @if($schedule && count($schedule) > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b-2 border-slate-100">
                            <th class="text-left py-4 px-4 font-black text-slate-600 text-sm">Kelas</th>
                            <th class="text-left py-4 px-4 font-black text-slate-600 text-sm">Siswa</th>
                            <th class="text-left py-4 px-4 font-black text-slate-600 text-sm">Jenjang</th>
                            <th class="text-left py-4 px-4 font-black text-slate-600 text-sm">Status</th>
                            <th class="text-left py-4 px-4 font-black text-slate-600 text-sm">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($schedule as $item)
                            <tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                                <td class="py-4 px-4 font-semibold text-slate-800">{{ $item->program_name }}</td>
                                <td class="py-4 px-4 text-slate-700">{{ $item->student_name }}</td>
                                <td class="py-4 px-4 text-slate-700">{{ $item->jenjang ?? '-' }}</td>
                                <td class="py-4 px-4">
                                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-lg text-xs font-bold">Aktif</span>
                                </td>
                                <td class="py-4 px-4">
                                    <button class="text-indigo-600 hover:text-indigo-800 font-bold text-sm">Detail</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-inbox text-slate-300 text-4xl mb-4"></i>
                <p class="text-slate-500 font-semibold">Tidak ada jadwal mengajar</p>
            </div>
        @endif
    </div>
</div>
@endsection

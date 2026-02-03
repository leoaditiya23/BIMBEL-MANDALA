@extends('admin.dashboard_admin')

@section('admin_content')
<div class="p-8">
    <div x-data="{ showProgramModal: false }">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
            <div>
                <h2 class="text-3xl font-black text-slate-800">Manajemen Program</h2>
                <p class="text-sm text-slate-500">Kelola semua paket dan program bimbingan</p>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
            @if($programs && count($programs) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b-2 border-slate-100">
                                <th class="text-left py-4 px-4 font-black text-slate-600 text-sm">Nama Program</th>
                                <th class="text-left py-4 px-4 font-black text-slate-600 text-sm">Jenjang</th>
                                <th class="text-left py-4 px-4 font-black text-slate-600 text-sm">Mentor</th>
                                <th class="text-left py-4 px-4 font-black text-slate-600 text-sm">Peserta</th>
                                <th class="text-left py-4 px-4 font-black text-slate-600 text-sm">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($programs as $program)
                                <tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                                    <td class="py-4 px-4 font-semibold text-slate-800">{{ $program->name }}</td>
                                    <td class="py-4 px-4 text-slate-700">{{ $program->jenjang ?? '-' }}</td>
                                    <td class="py-4 px-4 text-slate-700">{{ $program->mentor_name ?? '-' }}</td>
                                    <td class="py-4 px-4">
                                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-lg text-xs font-bold">{{ $program->jumlah_peserta ?? 0 }}</span>
                                    </td>
                                    <td class="py-4 px-4">
                                        <button class="text-blue-600 hover:text-blue-800 font-bold text-sm">Edit</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-inbox text-slate-300 text-4xl mb-4"></i>
                    <p class="text-slate-500 font-semibold">Belum ada program</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
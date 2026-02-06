@extends('admin.dashboard_admin')

@section('admin_content')
<div class="p-8">
    {{-- Tambahkan variabel edit di x-data untuk menyimpan data program yang dipilih --}}
    <div x-data="{ 
        showProgramModal: false, 
        showEditModal: false,
        editData: { id: '', name: '', jenjang: '', price: '', mentor_id: '', type: '' }
    }">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
            <div>
                <h2 class="text-3xl font-black text-slate-800">Manajemen Program</h2>
                <p class="text-sm text-slate-500">Kelola semua paket dan program bimbingan</p>
            </div>
            {{-- Tombol Tambah Program (Jika diperlukan) --}}
            <button @click="showProgramModal = true" class="mt-4 md:mt-0 bg-slate-800 text-white px-6 py-3 rounded-2xl font-bold hover:bg-slate-700 transition">
                + Tambah Program
            </button>
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
                                    <td class="py-4 px-4 flex items-center gap-4">
                                        {{-- Tombol Edit yang Berfungsi --}}
                                        <button 
                                            @click="
                                                editData = { 
                                                    id: '{{ $program->id }}', 
                                                    name: '{{ $program->name }}', 
                                                    jenjang: '{{ $program->jenjang }}',
                                                    price: '{{ $program->price }}',
                                                    mentor_id: '{{ $program->mentor_id }}',
                                                    type: '{{ $program->type }}'
                                                };
                                                showEditModal = true;
                                            "
                                            class="text-blue-600 hover:text-blue-800 font-bold text-sm">
                                            Edit
                                        </button>

                                        {{-- Tombol Hapus --}}
                                        <form action="{{ route('admin.programs.delete', $program->id) }}" method="POST" onsubmit="return confirm('Hapus program ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 font-bold text-sm">Hapus</button>
                                        </form>
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

        <div x-show="showEditModal" 
             class="fixed inset-0 z-[99] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
             x-cloak>
            <div @click.away="showEditModal = false" 
                 class="bg-white w-full max-w-md rounded-[2rem] p-8 shadow-2xl">
                <h3 class="text-2xl font-black text-slate-800 mb-6">Edit Program</h3>
                
                <form :action="'{{ url('admin/programs/update') }}/' + editData.id" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">Nama Program</label>
                            <input type="text" name="name" x-model="editData.name" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Jenjang</label>
                                <select name="jenjang" x-model="editData.jenjang" class="w-full px-4 py-3 rounded-xl border border-slate-200 outline-none">
                                    <option value="SD">SD</option>
                                    <option value="SMP">SMP</option>
                                    <option value="SMA">SMA</option>
                                    <option value="Umum">Umum</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Tipe</label>
                                <select name="type" x-model="editData.type" class="w-full px-4 py-3 rounded-xl border border-slate-200 outline-none">
                                    <option value="reguler">Reguler</option>
                                    <option value="intensif">Intensif</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">Harga (Rp)</label>
                            <input type="number" name="price" x-model="editData.price" class="w-full px-4 py-3 rounded-xl border border-slate-200 outline-none">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">Pilih Mentor</label>
                            <select name="mentor_id" x-model="editData.mentor_id" class="w-full px-4 py-3 rounded-xl border border-slate-200 outline-none">
                                <option value="">Tanpa Mentor</option>
                                @foreach($mentors as $mentor)
                                    <option value="{{ $mentor->id }}">{{ $mentor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-8 flex gap-3">
                        <button type="button" @click="showEditModal = false" class="flex-1 px-6 py-3 rounded-xl font-bold text-slate-500 bg-slate-100 hover:bg-slate-200 transition">Batal</button>
                        <button type="submit" class="flex-1 px-6 py-3 rounded-xl font-bold text-white bg-blue-600 hover:bg-blue-700 transition shadow-lg shadow-blue-200">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
        </div>
</div>

{{-- CSS Tambahan agar x-cloak tidak berkedip --}}
<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
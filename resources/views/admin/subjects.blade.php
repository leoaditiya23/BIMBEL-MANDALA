@extends('admin.dashboard_admin')

@section('admin_content')
<div class="w-full pb-20 relative z-10" x-data="{ 
    showSubjectModal: false, 
    showEditModal: false,
    editData: { id: '', name: '', jenjang: '', slug: '' }
}">
    
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-black text-slate-800 tracking-tighter">
                Master Mata Pelajaran</span>
            </h2>
            <p class="text-sm text-slate-500 mt-1 font-medium">Kelola daftar bimbingan dasar sebelum dijadikan paket program.</p>
        </div>
        <button @click="showSubjectModal = true" class="bg-slate-900 text-white px-6 py-3 rounded-2xl font-bold hover:bg-slate-800 transition-all shadow-lg flex items-center gap-2 group">
            <i class="fas fa-layer-group group-hover:scale-110 transition-transform"></i>
            Tambah Mapel Baru
        </button>
    </div>

    {{-- Subjects Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($subjects as $subject)
            <div class="bg-white border-2 border-slate-100 rounded-[2.5rem] p-6 shadow-sm relative overflow-hidden group hover:border-blue-500/30 transition-all">
                <div class="relative z-10">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase bg-blue-100 text-blue-600">
                                {{ $subject->jenjang }}
                            </span>
                            <h3 class="text-xl font-black mt-3 text-slate-800 tracking-tight">{{ $subject->name }}</h3>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">ID: #{{ str_pad($subject->id, 3, '0', STR_PAD_LEFT) }}</p>
                        </div>
                        
                        <div class="flex flex-col gap-2">
                            <button @click="
                                editData.id = '{{ $subject->id }}';
                                editData.name = '{{ addslashes($subject->name) }}';
                                editData.jenjang = '{{ $subject->jenjang }}';
                                showEditModal = true;
                            " class="w-8 h-8 bg-slate-50 rounded-lg flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all">
                                <i class="fas fa-edit text-xs"></i>
                            </button>
                            {{-- REVISI: Mengubah destroy menjadi delete sesuai route name yang ada --}}
                            <form action="{{ route('admin.subjects.delete', $subject->id) }}" method="POST" onsubmit="return confirm('Hapus mapel ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-8 h-8 bg-slate-50 rounded-lg flex items-center justify-center hover:bg-red-500 hover:text-white transition-all text-red-500">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- MODAL TAMBAH --}}
    <template x-teleport="body">
        <div x-show="showSubjectModal" class="fixed inset-0 flex items-center justify-center p-4" style="z-index: 999999999 !important;" x-cloak>
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showSubjectModal = false"></div>
            
            <div x-show="showSubjectModal" class="relative bg-white w-full max-w-sm rounded-2xl p-6 shadow-2xl border-[4px] border-slate-50">
                <div class="mb-5">
                    <h3 class="text-lg font-black text-slate-900 tracking-tight">Tambah Mata Pelajaran</h3>
                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">Registrasi kategori mapel baru</p>
                </div>

                <form action="{{ route('admin.subjects.store') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div class="bg-white">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1 px-1">Nama Mata Pelajaran</label>
                            <input type="text" name="name" required placeholder="Contoh: Matematika Gasing" class="w-full px-4 py-3 rounded-xl bg-[#f8fafc] border-2 border-slate-100 outline-none font-bold text-slate-800 text-xs focus:border-blue-500 transition">
                        </div>

                        <div class="bg-white">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1 px-1">Jenjang Pelajaran</label>
                            <select name="jenjang" required class="w-full px-3 py-3 rounded-xl bg-[#f8fafc] border-2 border-slate-100 outline-none font-bold text-slate-800 text-xs focus:border-blue-500 transition">
                                <option value="TK">TK</option>
                                <option value="SD">SD</option>
                                <option value="SMP">SMP</option>
                                <option value="SMA">SMA</option>
                                <option value="Umum">Umum</option>
                            </select>
                        </div>

                        <div class="mt-6 flex gap-2.5 pt-2">
                            <button type="button" @click="showSubjectModal = false" class="flex-1 py-3 rounded-xl font-black text-slate-400 bg-slate-50 uppercase text-[9px] tracking-wider hover:bg-slate-100 transition border border-slate-100">Batal</button>
                            <button type="submit" class="flex-1 py-3 rounded-xl font-black text-white bg-blue-600 shadow-md shadow-blue-100 uppercase text-[9px] tracking-wider hover:bg-blue-700 transition">Simpan Mapel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </template>

    {{-- MODAL EDIT --}}
    <template x-teleport="body">
        <div x-show="showEditModal" class="fixed inset-0 flex items-center justify-center p-4" style="z-index: 999999999 !important;" x-cloak>
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showEditModal = false"></div>
            
            <div x-show="showEditModal" class="relative bg-white w-full max-w-sm rounded-2xl p-6 shadow-2xl border-[4px] border-slate-50">
                <div class="mb-5">
                    <h3 class="text-lg font-black text-slate-900 tracking-tight">Update Mata Pelajaran</h3>
                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">Ubah data master mata pelajaran</p>
                </div>

                <form :action="'{{ url('admin/subjects/update') }}/' + editData.id" method="POST">
                    @csrf @method('PUT')
                    <div class="space-y-4">
                        <div class="bg-white">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1 px-1">Nama Mata Pelajaran</label>
                            <input type="text" name="name" x-model="editData.name" class="w-full px-4 py-3 rounded-xl bg-[#f8fafc] border-2 border-slate-100 outline-none font-bold text-slate-800 text-xs focus:border-blue-500 transition">
                        </div>

                        <div class="bg-white">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1 px-1">Jenjang Pelajaran</label>
                            <select name="jenjang" x-model="editData.jenjang" class="w-full px-3 py-3 rounded-xl bg-[#f8fafc] border-2 border-slate-100 outline-none font-bold text-slate-800 text-xs focus:border-blue-500 transition">
                                <option value="TK">TK</option>
                                <option value="SD">SD</option>
                                <option value="SMP">SMP</option>
                                <option value="SMA">SMA</option>
                                <option value="Umum">Umum</option>
                            </select>
                        </div>

                        <div class="mt-6 flex gap-2.5 pt-2">
                            <button type="button" @click="showEditModal = false" class="flex-1 py-3 rounded-xl font-black text-slate-400 bg-slate-50 uppercase text-[9px] tracking-wider hover:bg-slate-100 transition border border-slate-100">Batal</button>
                            <button type="submit" class="flex-1 py-3 rounded-xl font-black text-white bg-slate-900 shadow-md uppercase text-[9px] tracking-wider hover:bg-slate-800 transition">Update Data</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>

<style>
    [x-cloak] { display: none !important; }
    input, select { background-color: #f8fafc !important; color: #0f172a !important; }
</style>
@endsection
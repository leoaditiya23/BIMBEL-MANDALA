@extends('admin.dashboard_admin')

@section('admin_content')
{{-- REVISI: Ubah p-8 menjadi w-full agar sejajar lurus dengan header --}}
<div class="w-full" x-data="{ 
    showProgramModal: false, 
    showEditModal: false,
    editData: { id: '', name: '', jenjang: '', price: '', mentor_id: '', type: '' }
}" :class="(showEditModal || showProgramModal) ? 'overflow-hidden' : ''">
    
    {{-- Header & Stats --}}
    <div class="flex flex-col md:flex-row justify-between items-end mb-10 gap-4">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <h2 class="text-3xl font-black text-slate-800 tracking-tighter">Katalog <span class="text-slate-800">Program</span></h2>
            </div>
            <p class="text-sm text-slate-400 font-medium">Atur paket bimbingan belajar dan penetapan mentor pendamping.</p>
        </div>
        <button @click="showProgramModal = true" class="bg-blue-600 text-white px-6 py-3 rounded-2xl font-bold hover:bg-blue-700 transition-all shadow-lg flex items-center gap-2 group">
            <i class="fas fa-plus group-hover:rotate-90 transition-transform"></i>
            Tambah Program Baru
        </button>
    </div>

    {{-- Programs Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @foreach($programs as $program)
            <div class="bg-slate-900 rounded-[2.5rem] p-8 shadow-xl relative overflow-hidden group">
                <div class="relative z-10 text-white">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase bg-white/10 border border-white/20">
                                {{ $program->jenjang ?? 'Umum' }}
                            </span>
                            <h3 class="text-xl font-black mt-4">{{ $program->name }}</h3>
                            <p class="text-slate-400 font-bold mt-1 text-lg">Rp {{ number_format($program->price, 0, ',', '.') }}</p>
                            
                            {{-- REVISI: Menggunakan mentor_name dari hasil join SQL --}}
                            <div class="mt-4 flex items-center gap-2 text-blue-400">
                                <i class="fas fa-user-tie text-xs"></i>
                                <span class="text-xs font-bold uppercase tracking-wider">
                                    Mentor: {{ $program->mentor_name ?? 'Tanpa Mentor' }}
                                </span>
                            </div>
                        </div>
                        <button @click="
                            editData = { 
                                id: '{{ $program->id }}', 
                                name: '{{ $program->name }}', 
                                jenjang: '{{ $program->jenjang }}',
                                price: '{{ $program->price }}',
                                mentor_id: '{{ $program->mentor_id }}'
                            };
                            showEditModal = true;
                        " class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center hover:bg-white hover:text-slate-900 transition-all">
                            <i class="fas fa-edit text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- MODAL TAMBAH --}}
    <template x-teleport="body">
        <div x-show="showProgramModal" 
             class="fixed inset-0 flex items-center justify-center p-4" 
             style="z-index: 999999999 !important;"
             x-cloak>
            <div class="fixed inset-0 bg-[#0f172a] opacity-100" @click="showProgramModal = false"></div>
            <div x-show="showProgramModal"
                 class="relative bg-white w-full max-w-lg rounded-[2.5rem] p-12 shadow-2xl border-[10px] border-slate-50 overflow-y-auto max-h-[90vh]"
                 style="background-color: #ffffff !important; isolation: isolate !important;">
                
                <div class="mb-10">
                    <h3 class="text-3xl font-black text-slate-900 tracking-tight">Tambah Program Baru</h3>
                    <p class="text-base text-slate-500 font-bold mt-2">Buat paket bimbingan belajar baru untuk katalog.</p>
                </div>
                
                <form action="{{ route('admin.programs.store') }}" method="POST">
                    @csrf
                    <div class="space-y-6">
                        <div class="bg-white">
                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Nama Paket Program</label>
                            <input type="text" name="name" required class="w-full px-6 py-4 rounded-2xl bg-[#f1f5f9] border-2 border-slate-200 focus:border-blue-600 outline-none font-black text-slate-800">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-white">
                                <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Jenjang</label>
                                <select name="jenjang" class="w-full px-6 py-4 rounded-2xl bg-[#f1f5f9] border-2 border-slate-200 outline-none font-black text-slate-800">
                                    <option value="SD">SD</option><option value="SMP">SMP</option><option value="SMA">SMA</option><option value="Umum">Umum</option>
                                </select>
                            </div>
                            <div class="bg-white">
                                <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Harga (Rp)</label>
                                <input type="number" name="price" required class="w-full px-6 py-4 rounded-2xl bg-[#f1f5f9] border-2 border-slate-200 outline-none font-black text-slate-800">
                            </div>
                        </div>

                        <div class="bg-white">
                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Pilih Mentor Pendamping</label>
                            <select name="mentor_id" class="w-full px-6 py-4 rounded-2xl bg-[#f1f5f9] border-2 border-slate-200 focus:border-blue-600 outline-none font-black text-slate-800">
                                <option value="">Tanpa Mentor</option>
                                @foreach($mentors as $mentor)
                                    <option value="{{ $mentor->id }}">{{ $mentor->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mt-12 flex gap-4 bg-white pt-4">
                            <button type="button" @click="showProgramModal = false" class="flex-1 py-5 rounded-2xl font-black text-slate-400 bg-slate-100 hover:bg-slate-200 uppercase text-xs">Batal</button>
                            <button type="submit" class="flex-1 py-5 rounded-2xl font-black text-white bg-blue-600 hover:bg-blue-700 shadow-xl uppercase text-xs">Simpan Program</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </template>

    {{-- MODAL EDIT --}}
    <template x-teleport="body">
        <div x-show="showEditModal" 
             class="fixed inset-0 flex items-center justify-center p-4" 
             style="z-index: 999999999 !important;"
             x-cloak>
            
            <div class="fixed inset-0 bg-[#0f172a] opacity-100" @click="showEditModal = false"></div>

            <div x-show="showEditModal"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-100"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="relative bg-white w-full max-w-lg rounded-[2.5rem] p-12 shadow-2xl border-[10px] border-slate-50 overflow-y-auto max-h-[90vh]"
                 style="background-color: #ffffff !important; isolation: isolate !important;">
                
                <div class="mb-10">
                    <h3 class="text-3xl font-black text-slate-900 tracking-tight">Edit Detail Program</h3>
                    <p class="text-base text-slate-500 font-bold mt-2">Perbarui informasi program bimbingan.</p>
                </div>
                
                <form :action="'{{ url('admin/programs/update') }}/' + editData.id" method="POST">
                    @csrf @method('PUT')
                    <div class="space-y-6">
                        <div class="relative bg-white">
                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Nama Paket Program</label>
                            <input type="text" name="name" x-model="editData.name" 
                                   class="w-full px-6 py-4 rounded-2xl bg-[#f1f5f9] border-2 border-slate-200 focus:border-blue-600 focus:bg-white outline-none font-black text-slate-800">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-white">
                                <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Jenjang</label>
                                <select name="jenjang" x-model="editData.jenjang" 
                                        class="w-full px-6 py-4 rounded-2xl bg-[#f1f5f9] border-2 border-slate-200 focus:border-blue-600 outline-none font-black text-slate-800">
                                    <option value="SD">SD</option><option value="SMP">SMP</option><option value="SMA">SMA</option><option value="Umum">Umum</option>
                                </select>
                            </div>
                            <div class="bg-white">
                                <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Harga (Rp)</label>
                                <input type="number" name="price" x-model="editData.price" 
                                       class="w-full px-6 py-4 rounded-2xl bg-[#f1f5f9] border-2 border-slate-200 focus:border-blue-600 outline-none font-black text-slate-800">
                            </div>
                        </div>

                        <div class="bg-white">
                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Pilih Mentor Pendamping</label>
                            <select name="mentor_id" x-model="editData.mentor_id" 
                                    class="w-full px-6 py-4 rounded-2xl bg-[#f1f5f9] border-2 border-slate-200 focus:border-blue-600 outline-none font-black text-slate-800">
                                <option value="">Tanpa Mentor</option>
                                @foreach($mentors as $mentor)
                                    <option value="{{ $mentor->id }}">{{ $mentor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-12 flex gap-4 bg-white pt-4">
                        <button type="button" @click="showEditModal = false" class="flex-1 py-5 rounded-2xl font-black text-slate-400 bg-slate-100 hover:bg-slate-200 uppercase text-xs">Batal</button>
                        <button type="submit" class="flex-1 py-5 rounded-2xl font-black text-white bg-blue-600 hover:bg-blue-700 shadow-xl uppercase text-xs">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>

<style>
    [x-cloak] { display: none !important; }
    .bg-white { background-color: #ffffff !important; opacity: 1 !important; }
    input, select { background-color: #f1f5f9 !important; color: #0f172a !important; opacity: 1 !important; }
    body.overflow-hidden { overflow: hidden; }
</style>
@endsection
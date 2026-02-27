@extends('admin.dashboard_admin')

@section('admin_content')
<div style="transform: scale(0.85); transform-origin: top left; width: 117.6%;">
    <div class="w-full" x-data="{ 
        showProgramModal: false, 
        showEditModal: false,
        editData: { id: '', name: '', jenjang: '', price: 0, extra_meeting_price: 0, quran_price: 0, mentor_id: '', type: '' }
    }" :class="(showEditModal || showProgramModal) ? 'overflow-hidden' : ''">
        
        {{-- Header --}}
        <div class="flex flex-col md:flex-row justify-between items-end mb-10 gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    {{-- JUDUL DINAMIS BERDASARKAN PARAMETER URL --}}
                    <h2 class="text-3xl font-black text-slate-800 tracking-tighter">
                        Katalog 
                        <span class="text-slate-800">
                            {{ request()->route('type') ? ucfirst(request()->route('type')) : 'Program' }}
                        </span>
                    </h2>
                </div>
                <p class="text-sm text-slate-400 font-medium">Atur paket bimbingan belajar dan penetapan mentor pendamping.</p>
            </div>
            <button @click="showProgramModal = true" class="bg-blue-600 text-white px-6 py-3 rounded-2xl font-bold hover:bg-blue-700 transition-all shadow-lg flex items-center gap-2 group">
                <i class="fas fa-plus group-hover:rotate-90 transition-transform"></i>
                Tambah Program Baru
            </button>
        </div>

        {{-- Programs Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            @foreach($programs as $program)
                <div class="bg-slate-900 rounded-[2.5rem] p-8 shadow-xl relative overflow-hidden group">
                    <div class="relative z-10 text-white">
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase bg-white/10 border border-white/20">
                                    {{ $program->jenjang ?? 'Umum' }}
                                </span>
                                <h3 class="text-xl font-black mt-4">{{ $program->name }}</h3>
                                
                                <p class="text-slate-400 font-bold mt-1 text-lg">
                                    Rp {{ number_format($program->price, 0, ',', '.') }}
                                </p>
                                
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
                                    extra_meeting_price: '{{ $program->extra_meeting_price ?? 0 }}',
                                    quran_price: '{{ $program->quran_price ?? 0 }}',
                                    mentor_id: '{{ $program->mentor_id }}',
                                    type: '{{ $program->type }}'
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
            <div x-show="showProgramModal" class="fixed inset-0 flex items-center justify-center p-4" style="z-index: 999999999 !important;" x-cloak>
                <div class="fixed inset-0 bg-[#0f172a]/60 backdrop-blur-sm" @click="showProgramModal = false"></div>
                
                <div x-show="showProgramModal" class="relative bg-white w-full max-w-sm rounded-2xl p-6 shadow-2xl border-[4px] border-slate-50 overflow-y-auto max-h-[90vh]">
                    <div class="mb-5">
                        <h3 class="text-lg font-black text-slate-900 tracking-tight">Tambah Program Baru</h3>
                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest text-nowrap">Input data katalog bimbingan baru</p>
                    </div>

                    {{-- SINKRONISASI RUTE STORE --}}
                    <form action="{{ route('admin.programs.store') }}" method="POST">
                        @csrf
                        <div class="space-y-3.5">
                            <div class="bg-white">
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1 px-1">Nama Paket Program</label>
                                <input type="text" name="name" required class="w-full px-4 py-2.5 rounded-lg bg-[#f8fafc] border-2 border-slate-100 outline-none font-bold text-slate-800 text-xs focus:border-blue-500 transition">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-white">
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1 px-1">Tipe</label>
                                    <select name="type" class="w-full px-3 py-2.5 rounded-lg bg-[#f8fafc] border-2 border-slate-100 outline-none font-bold text-slate-800 text-xs focus:border-blue-500 transition">
                                        <option value="reguler">Reguler</option>
                                        <option value="intensif">Intensif</option>
                                    </select>
                                </div>
                                <div class="bg-white">
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1 px-1">Jenjang</label>
                                    <select name="jenjang" class="w-full px-3 py-2.5 rounded-lg bg-[#f8fafc] border-2 border-slate-100 outline-none font-bold text-slate-800 text-xs focus:border-blue-500 transition">
                                        <option value="SD">SD</option>
                                        <option value="SMP">SMP</option>
                                        <option value="SMA">SMA</option>
                                        <option value="Umum">Umum</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="bg-white">
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1 px-1">Harga Dasar (Contoh: 125000)</label>
                                <input type="text" name="price" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required class="w-full px-4 py-2.5 rounded-lg bg-[#f8fafc] border-2 border-slate-100 outline-none font-bold text-slate-800 text-xs focus:border-blue-500 transition">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-white">
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1 px-1">Extra Sesi</label>
                                    <input type="text" name="extra_meeting_price" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required class="w-full px-4 py-2.5 rounded-lg bg-[#f8fafc] border-2 border-slate-100 outline-none font-bold text-slate-800 text-xs focus:border-blue-500 transition">
                                </div>
                                <div class="bg-white">
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1 px-1">Harga Ngaji</label>
                                    <input type="text" name="quran_price" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required class="w-full px-4 py-2.5 rounded-lg bg-[#f8fafc] border-2 border-slate-100 outline-none font-bold text-slate-800 text-xs focus:border-blue-500 transition">
                                </div>
                            </div>
                            
                            <div class="mt-6 flex gap-2.5 pt-1">
                                <button type="button" @click="showProgramModal = false" class="flex-1 py-3 rounded-lg font-black text-slate-400 bg-slate-50 uppercase text-[9px] tracking-wider hover:bg-slate-100 transition border border-slate-100">Batal</button>
                                <button type="submit" class="flex-1 py-3 rounded-lg font-black text-white bg-blue-600 shadow-md shadow-blue-100 uppercase text-[9px] tracking-wider hover:bg-blue-700 transition">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        {{-- MODAL EDIT --}}
<template x-teleport="body">
    <div x-show="showEditModal" class="fixed inset-0 flex items-center justify-center p-4" style="z-index: 999999999 !important;" x-cloak>
        <div class="fixed inset-0 bg-[#0f172a]/60 backdrop-blur-sm" @click="showEditModal = false"></div>
        
        <div x-show="showEditModal" class="relative bg-white w-full max-w-sm rounded-2xl p-6 shadow-2xl border-[4px] border-slate-50 overflow-y-auto max-h-[90vh]">
            <div class="mb-5">
                <h3 class="text-lg font-black text-slate-900 tracking-tight">Edit Program</h3>
                <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest text-nowrap">Pembaruan data katalog bimbingan</p>
            </div>

            {{-- SINKRONISASI RUTE UPDATE --}}
            <form :action="'{{ url('admin/programs-action/update') }}/' + editData.id" method="POST">
                @csrf 
                @method('PUT')
                <div class="space-y-3.5">
                    <div class="bg-white">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1 px-1">Nama Program</label>
                        <input type="text" name="name" x-model="editData.name" class="w-full px-4 py-2.5 rounded-lg bg-[#f8fafc] border-2 border-slate-100 outline-none font-bold text-slate-800 text-xs focus:border-blue-500 transition">
                    </div>

                    <div class="bg-white">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1 px-1">Harga Dasar (Input: 125000)</label>
                        <input type="text" name="price" x-model="editData.price" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="w-full px-4 py-2.5 rounded-lg bg-[#f8fafc] border-2 border-slate-100 outline-none font-bold text-slate-800 text-xs focus:border-blue-500 transition">
                    </div>

                    <div class="grid grid-cols-2 gap-5">
                        <div class="bg-white">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5 px-1 text-nowrap">Extra Sesi</label>
                            <input type="text" name="extra_meeting_price" x-model="editData.extra_meeting_price" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="w-full px-4 py-2.5 rounded-lg bg-[#f8fafc] border-2 border-slate-100 outline-none font-bold text-slate-800 text-xs focus:border-blue-500 transition">
                        </div>
                        <div class="bg-white">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5 px-1 text-nowrap">Harga Ngaji</label>
                            <input type="text" name="quran_price" x-model="editData.quran_price" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="w-full px-4 py-2.5 rounded-lg bg-[#f8fafc] border-2 border-slate-100 outline-none font-bold text-slate-800 text-xs focus:border-blue-500 transition">
                        </div>
                    </div>

                    <div class="mt-6 flex gap-2.5 pt-1">
                        <button type="button" @click="showEditModal = false" class="flex-1 py-3 rounded-lg font-black text-slate-400 bg-slate-50 uppercase text-[9px] tracking-wider hover:bg-slate-100 transition border border-slate-100">Batal</button>
                        <button type="submit" class="flex-1 py-3 rounded-lg font-black text-white bg-blue-600 shadow-md shadow-blue-100 uppercase text-[9px] tracking-wider hover:bg-blue-700 transition">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
    input, select { background-color: #f8fafc !important; color: #0f172a !important; }
    body.overflow-hidden { overflow: hidden; }
</style>
@endsection
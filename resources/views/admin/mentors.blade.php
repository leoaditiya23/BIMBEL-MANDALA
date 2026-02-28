@extends('admin.dashboard_admin')

@section('admin_content')
<div class="w-full pb-20 relative z-10" x-data="{ 
    showMentorModal: false, 
    showEditModal: false,
    editData: { id: '', name: '', specialist: '', whatsapp: '', photo: '' },
    openEdit(mentor) {
        this.editData = { ...mentor };
        this.showEditModal = true;
    }
}">
    
    {{-- Header & Stats --}}
    <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-black text-slate-800 tracking-tighter">Manajemen <span class="text-slate-800">Mentor</span></h2>
            <p class="text-sm text-slate-500 mt-1 font-medium">Tambah atau atur mentor yang tampil di halaman depan aplikasi.</p>
        </div>
        <button @click="showMentorModal = true" class="bg-blue-600 text-white px-6 py-3 rounded-2xl font-bold hover:bg-blue-700 transition-all shadow-lg flex items-center gap-2 group">
            <i class="fas fa-plus group-hover:rotate-90 transition-transform"></i>
            Tambah Mentor Baru
        </button>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-500 text-white font-bold rounded-2xl shadow-lg shadow-green-100 flex items-center gap-3">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
    @endif

    {{-- Mentors Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($mentors as $mentor)
                {{-- REVISI: Menghapus mx-auto dan memaksa margin-left: 0 agar sejajar dengan judul di atas --}}
                <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-slate-100 hover:shadow-md transition-all group relative" style="max-width: 280px; width: 100%; margin-left: 0;">
                    <div class="flex flex-col items-center text-center">
                        
                        {{-- Avatar --}}
                        <div class="w-20 h-20 rounded-[1.5rem] overflow-hidden mb-4 border-2 border-slate-50 group-hover:border-blue-100 transition-all shadow-inner">
                            @if($mentor->photo)
                                <img src="{{ asset('storage/' . $mentor->photo) }}" class="w-full h-full object-cover">
                            @else
                                <img src="https://api.dicebear.com/7.x/avataaars/svg?seed={{ urlencode($mentor->name) }}" class="w-full h-full object-cover">
                            @endif
                        </div>

                        {{-- Text Information --}}
                        <h3 class="text-base font-black text-slate-800 leading-tight mb-1">{{ $mentor->name }}</h3>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">
                            {{ $mentor->specialist }}
                        </p>

                        {{-- WhatsApp Link --}}
                        @if($mentor->whatsapp)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $mentor->whatsapp) }}" 
                               target="_blank"
                               class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-green-50 text-green-600 border border-green-100 text-[10px] font-black uppercase hover:bg-green-600 hover:text-white transition-all">
                                <i class="fab fa-whatsapp text-xs"></i>
                                {{ $mentor->whatsapp }}
                            </a>
                        @else
                            <span class="text-[10px] font-bold text-slate-300 uppercase italic">No WhatsApp -</span>
                        @endif

                        {{-- Action Buttons --}}
                        <div class="flex gap-2 mt-6 w-full">
                            <button @click="openEdit({{ json_encode($mentor) }})" 
                                    class="flex-1 py-2.5 bg-slate-50 text-slate-600 rounded-xl font-black text-[10px] uppercase tracking-wider hover:bg-blue-600 hover:text-white transition-all">
                                Edit
                            </button>

                            <form action="{{ route('admin.mentors.delete', $mentor->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Hapus mentor ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" 
                                        class="w-full py-2.5 bg-slate-50 text-red-400 rounded-xl font-black text-[10px] uppercase tracking-wider hover:bg-red-500 hover:text-white transition-all">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- MODAL TAMBAH MENTOR --}}
        <template x-teleport="body">
            <div x-show="showMentorModal" class="fixed inset-0 flex items-center justify-center p-4" style="z-index: 999999999 !important;" x-cloak>
                <div class="fixed inset-0 bg-[#0f172a]/60 backdrop-blur-sm" @click="showMentorModal = false"></div>
                <div x-show="showMentorModal" class="relative bg-white w-full max-w-sm rounded-2xl p-6 shadow-2xl border-[4px] border-slate-50 overflow-y-auto max-h-[90vh]">
                    <div class="mb-5">
                        <h3 class="text-lg font-black text-slate-900 tracking-tight">Tambah Mentor</h3>
                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">Daftarkan pengajar baru ke sistem</p>
                    </div>

                    <form action="{{ route('admin.mentors.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-3.5">
                            <div>
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1 px-1">Nama Lengkap</label>
                                <input type="text" name="name" required placeholder="Nama lengkap" class="w-full px-4 py-2.5 rounded-lg bg-[#f8fafc] border-2 border-slate-100 outline-none font-bold text-slate-800 text-xs focus:border-blue-500 transition">
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1 px-1">Spesialisasi</label>
                                    <input type="text" name="specialist" required placeholder="Fisika, dll" class="w-full px-4 py-2.5 rounded-lg bg-[#f8fafc] border-2 border-slate-100 outline-none font-bold text-slate-800 text-xs focus:border-blue-500 transition">
                                </div>
                                <div>
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1 px-1">No. WhatsApp</label>
                                    <input type="text" name="whatsapp" required placeholder="0812..." class="w-full px-4 py-2.5 rounded-lg bg-[#f8fafc] border-2 border-slate-100 outline-none font-bold text-slate-800 text-xs focus:border-blue-500 transition">
                                </div>
                            </div>

                            <div>
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1 px-1">Upload Foto Profil</label>
                                <input type="file" name="photo" class="w-full text-[10px] text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-[9px] file:font-black file:bg-slate-100 file:text-slate-600 hover:file:bg-slate-200 transition-all cursor-pointer">
                            </div>

                            <div class="mt-6 flex gap-2.5 pt-1">
                                <button type="button" @click="showMentorModal = false" class="flex-1 py-3 rounded-lg font-black text-slate-400 bg-slate-50 uppercase text-[9px] tracking-wider hover:bg-slate-100 transition border border-slate-100">Batal</button>
                                <button type="submit" class="flex-1 py-3 rounded-lg font-black text-white bg-blue-600 shadow-md shadow-blue-100 uppercase text-[9px] tracking-wider hover:bg-blue-700 transition">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        {{-- MODAL EDIT MENTOR --}}
        <template x-teleport="body">
            <div x-show="showEditModal" class="fixed inset-0 flex items-center justify-center p-4" style="z-index: 999999999 !important;" x-cloak>
                <div class="fixed inset-0 bg-[#0f172a]/60 backdrop-blur-sm" @click="showEditModal = false"></div>
                <div x-show="showEditModal" class="relative bg-white w-full max-w-sm rounded-2xl p-6 shadow-2xl border-[4px] border-slate-50 overflow-y-auto max-h-[90vh]">
                    <div class="mb-5">
                        <h3 class="text-lg font-black text-slate-900 tracking-tight">Edit Mentor</h3>
                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">Pembaruan data pengajar aplikasi</p>
                    </div>

                    <form :action="'{{ url('admin/mentors/update') }}/' + editData.id" method="POST" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        <div class="space-y-3.5">
                            <div>
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1 px-1">Nama Lengkap</label>
                                <input type="text" name="name" x-model="editData.name" required class="w-full px-4 py-2.5 rounded-lg bg-[#f8fafc] border-2 border-slate-100 outline-none font-bold text-slate-800 text-xs focus:border-blue-500 transition">
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1 px-1">Spesialisasi</label>
                                    <input type="text" name="specialist" x-model="editData.specialist" required class="w-full px-4 py-2.5 rounded-lg bg-[#f8fafc] border-2 border-slate-100 outline-none font-bold text-slate-800 text-xs focus:border-blue-500 transition">
                                </div>
                                <div>
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1 px-1">No. WhatsApp</label>
                                    <input type="text" name="whatsapp" x-model="editData.whatsapp" required class="w-full px-4 py-2.5 rounded-lg bg-[#f8fafc] border-2 border-slate-100 outline-none font-bold text-slate-800 text-xs focus:border-blue-500 transition">
                                </div>
                            </div>

                            <div>
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1 px-1 text-nowrap">Ganti Foto Profil (Opsional)</label>
                                <input type="file" name="photo" class="w-full text-[10px] text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-[9px] file:font-black file:bg-slate-100 file:text-slate-600 hover:file:bg-slate-200 transition-all cursor-pointer">
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

<style>
    [x-cloak] { display: none !important; }
    .bg-white { background-color: #ffffff !important; opacity: 1 !important; }
    input, select { background-color: #f8fafc !important; color: #0f172a !important; opacity: 1 !important; }
    body.overflow-hidden { overflow: hidden; }
</style>
@endsection
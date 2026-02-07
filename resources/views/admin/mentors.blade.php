@extends('admin.dashboard_admin')

@section('admin_content')
<div class="p-8">
    <div x-data="{ 
        showAddModal: false, 
        showEditModal: false,
        selectedMentor: { id: '', name: '', email: '', specialization: '', whatsapp: '' }
    }">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
            <div>
                <h2 class="text-3xl font-black text-slate-800 tracking-tight">Manajemen Mentor</h2>
                <p class="text-sm text-slate-500 mt-1">Kelola tenaga pendidik profesional Mandala Bimbel</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="bg-white px-6 py-3 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-center min-w-[140px]">
                    <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest leading-none mb-1">Total Pengajar</p>
                    <p class="text-xl font-black text-blue-600 leading-none">{{ count($mentors) }} <span class="text-sm text-slate-400 font-bold">Orang</span></p>
                </div>
                <button @click="showAddModal = true" class="bg-blue-600 text-white px-8 py-4 rounded-2xl font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-100 flex items-center gap-2">
                    <i class="fas fa-plus-circle"></i>
                    Mentor Baru
                </button>
            </div>
        </div>

        <div class="bg-slate-50/50 p-8 rounded-3xl border border-slate-100">
            @if($mentors && count($mentors) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($mentors as $mentor)
                        <div class="group border border-white rounded-2xl p-7 hover:shadow-2xl hover:shadow-slate-200/50 transition-all duration-500 bg-white relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50/50 rounded-bl-[3rem] -mr-16 -mt-16 transition-transform group-hover:scale-110"></div>
                            
                            <div class="flex items-center mb-8 relative">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($mentor->name) }}&background=6366f1&color=fff&bold=true" 
                                     class="w-16 h-16 rounded-xl border-4 border-slate-50 shadow-sm object-cover">
                                <div class="ml-4">
                                    <p class="font-black text-slate-800 text-lg leading-tight mb-1">{{ $mentor->name }}</p>
                                    <span class="text-[10px] bg-blue-600 text-white px-3 py-1 rounded-md font-black uppercase tracking-tighter">Professional Mentor</span>
                                </div>
                            </div>

                            <div class="space-y-4 mb-8">
                                <div class="flex items-center group/item">
                                    <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center mr-4 group-hover/item:bg-blue-50 transition">
                                        <i class="fas fa-envelope text-slate-400 text-xs group-hover/item:text-blue-500"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest">Email</p>
                                        <p class="text-slate-600 font-bold text-sm truncate">{{ $mentor->email }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center group/item">
                                    <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center mr-4 group-hover/item:bg-orange-50 transition">
                                        <i class="fas fa-book-open text-slate-400 text-xs group-hover/item:text-orange-500"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest">Spesialisasi Mapel</p>
                                        <p class="text-slate-800 font-black text-sm">{{ $mentor->specialization ?? 'Umum' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3 relative z-10">
                                <button @click="
                                    selectedMentor = { 
                                        id: '{{ $mentor->id }}', 
                                        name: '{{ $mentor->name }}', 
                                        email: '{{ $mentor->email }}', 
                                        specialization: '{{ $mentor->specialization }}' 
                                    };
                                    showEditModal = true;
                                " class="py-3.5 rounded-xl bg-slate-100 text-slate-600 font-black text-xs hover:bg-slate-800 hover:text-white transition-all duration-300">
                                    Edit Profil
                                </button>
                                <a href="https://wa.me/{{ $mentor->whatsapp ?? '' }}" target="_blank" class="py-3.5 rounded-xl bg-green-500 text-white font-black text-xs hover:bg-green-600 transition-all duration-300 flex items-center justify-center gap-2 shadow-lg shadow-green-100">
                                    <i class="fab fa-whatsapp text-sm"></i> Hubungi
                                </a>
                            </div>

                            <form action="{{ route('admin.programs.delete', $mentor->id) }}" method="POST" class="mt-4">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Hapus mentor ini?')" class="w-full py-2 text-[10px] font-black text-red-300 hover:text-red-500 transition-colors uppercase tracking-[0.2em]">
                                    Nonaktifkan Akses
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-24 bg-white rounded-2xl border border-dashed border-slate-200">
                    <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-user-tie text-slate-200 text-4xl"></i>
                    </div>
                    <p class="text-slate-400 font-black text-lg">Belum ada mentor terdaftar</p>
                    <button @click="showAddModal = true" class="mt-4 text-blue-600 font-black text-sm hover:text-blue-800 transition">Mulai tambah mentor baru</button>
                </div>
            @endif
        </div>

        {{-- MODAL TAMBAH - REVISI UKURAN --}}
        <div x-show="showAddModal" x-transition x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md">
            <div @click.away="showAddModal = false" class="bg-white w-full max-w-lg rounded-[1.5rem] p-8 shadow-2xl relative overflow-y-auto max-h-[95vh]">
                <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-blue-400 to-indigo-600"></div>
                
                <div class="mb-6 text-center">
                    <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center text-white text-xl shadow-lg shadow-blue-200 mx-auto mb-4">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h3 class="text-xl font-black text-slate-900">Registrasi Mentor</h3>
                    <p class="text-slate-400 font-medium text-xs">Buat kredensial akses pengajar baru.</p>
                </div>
                
                <form action="{{ route('admin.mentors.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="role" value="mentor">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="col-span-2 md:col-span-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-1.5 block">Nama Lengkap</label>
                            <input type="text" name="name" required placeholder="Nama & Gelar" 
                                class="w-full px-5 py-3 rounded-xl bg-slate-50 border-2 border-transparent focus:border-blue-500 focus:bg-white transition-all font-bold text-slate-700 outline-none text-sm">
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-1.5 block">Email</label>
                            <input type="email" name="email" required placeholder="email@mandala.com" 
                                class="w-full px-5 py-3 rounded-xl bg-slate-50 border-2 border-transparent focus:border-blue-500 focus:bg-white transition-all font-bold text-slate-700 outline-none text-sm">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-1.5 block">Spesialisasi</label>
                            <input type="text" name="specialization" placeholder="Matematika" 
                                class="w-full px-5 py-3 rounded-xl bg-slate-50 border-2 border-transparent focus:border-blue-500 focus:bg-white transition-all font-bold text-slate-700 outline-none text-sm">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-1.5 block">WhatsApp</label>
                            <input type="text" name="whatsapp" placeholder="628xxx" 
                                class="w-full px-5 py-3 rounded-xl bg-slate-50 border-2 border-transparent focus:border-blue-500 focus:bg-white transition-all font-bold text-slate-700 outline-none text-sm">
                        </div>
                        <div class="col-span-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-1.5 block">Password</label>
                            <input type="password" name="password" required placeholder="••••••••" 
                                class="w-full px-5 py-3 rounded-xl bg-slate-50 border-2 border-transparent focus:border-blue-500 focus:bg-white transition-all font-bold text-slate-700 outline-none text-sm">
                        </div>
                    </div>

                    <div class="pt-4 flex items-center gap-3">
                        <button type="button" @click="showAddModal = false" class="flex-1 py-3.5 rounded-xl font-black text-slate-400 hover:text-slate-600 transition-all text-xs">
                            Batal
                        </button>
                        <button type="submit" class="flex-[2] py-3.5 rounded-xl font-black text-white bg-blue-600 hover:bg-blue-700 shadow-lg shadow-blue-100 transition-all uppercase tracking-widest text-xs">
                            Daftarkan Mentor
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL EDIT - REVISI UKURAN --}}
        <div x-show="showEditModal" x-transition x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md">
            <div @click.away="showEditModal = false" class="bg-white w-full max-w-lg rounded-[1.5rem] p-8 shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1.5 bg-slate-800"></div>

                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 rounded-xl bg-slate-900 flex items-center justify-center text-white text-xl shadow-xl shadow-slate-200">
                        <i class="fas fa-user-pen"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-slate-900 leading-tight">Edit Profil</h3>
                        <p class="text-slate-400 text-xs font-medium">Perbarui informasi pengajar</p>
                    </div>
                </div>
                
                <form :action="'{{ url('admin/mentors/update') }}/' + selectedMentor.id" method="POST" class="space-y-4">
                    @csrf @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-1.5 block">Nama Lengkap</label>
                            <input type="text" name="name" x-model="selectedMentor.name" 
                                class="w-full px-5 py-3 rounded-xl bg-slate-50 border-2 border-transparent focus:border-slate-800 focus:bg-white transition-all font-bold text-slate-800 outline-none text-sm">
                        </div>
                        
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-1.5 block">Spesialisasi</label>
                            <input type="text" name="specialization" x-model="selectedMentor.specialization" 
                                class="w-full px-5 py-3 rounded-xl bg-slate-50 border-2 border-transparent focus:border-slate-800 focus:bg-white transition-all font-bold text-slate-800 outline-none text-sm">
                        </div>
                    </div>
                    
                    <div class="pt-6 flex items-center gap-3">
                        <button type="button" @click="showEditModal = false" class="flex-1 py-3.5 rounded-xl font-black text-slate-400 hover:text-slate-600 transition-all text-xs">
                            Kembali
                        </button>
                        <button type="submit" class="flex-[2] py-3.5 rounded-xl font-black text-white bg-slate-900 hover:bg-black shadow-lg shadow-slate-200 transition-all uppercase tracking-widest text-xs">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
    * { font-style: normal !important; }
</style>
@endsection
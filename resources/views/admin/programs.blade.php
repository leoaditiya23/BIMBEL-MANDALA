@extends('admin.dashboard_admin')

@section('admin_content')
<div class="p-8" x-data="{ 
    showProgramModal: false, 
    showEditModal: false,
    editData: { id: '', name: '', jenjang: '', price: '', mentor_id: '', type: '' }
}">
    
    {{-- Header Section - Ikon Dihapus --}}
    <div class="flex flex-col md:flex-row justify-between items-end mb-10 gap-4">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <h2 class="text-3xl font-black text-slate-800 tracking-tighter">Katalog <span class="text-slate-800">Program</span></h2>
            </div>
            <p class="text-sm text-slate-400 font-medium">Atur paket bimbingan belajar dan penetapan mentor pendamping.</p>
        </div>
        
        <button @click="showProgramModal = true" class="bg-blue-600 text-white px-6 py-3 rounded-2xl font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-100 flex items-center gap-2 group">
            <i class="fas fa-plus group-hover:rotate-90 transition-transform"></i>
            Tambah Program Baru
        </button>
    </div>

    {{-- Program Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-500">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Program</p>
                <p class="text-xl font-black text-slate-800">{{ $programs->count() }} Paket</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-500">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Peserta</p>
                <p class="text-xl font-black text-slate-800">{{ $programs->sum('jumlah_peserta') }} Siswa</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-purple-50 rounded-2xl flex items-center justify-center text-purple-500">
                <i class="fas fa-star"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Program Terlaris</p>
                <p class="text-sm font-bold text-slate-800 truncate w-32">
                    {{ $programs->sortByDesc('jumlah_peserta')->first()->name ?? '-' }}
                </p>
            </div>
        </div>
    </div>

    {{-- Programs Grid - Warna Item Direvisi ke Slate/Hitam --}}
    @if($programs && count($programs) > 0)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach($programs as $program)
                <div class="bg-slate-900 rounded-[2.5rem] p-8 shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
                    {{-- Decor --}}
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-bl-[5rem] -mr-16 -mt-16 group-hover:bg-white/10 transition-colors"></div>

                    <div class="relative z-10">
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-tighter bg-white/10 text-white border border-white/20">
                                    {{ $program->jenjang ?? 'Umum' }}
                                </span>
                                <h3 class="text-xl font-black text-white mt-4">{{ $program->name }}</h3>
                                <p class="text-slate-400 font-bold mt-1 text-lg">Rp {{ number_format($program->price, 0, ',', '.') }}</p>
                            </div>
                            <div class="flex gap-2">
                                <button @click="
                                    editData = { 
                                        id: '{{ $program->id }}', 
                                        name: '{{ $program->name }}', 
                                        jenjang: '{{ $program->jenjang }}',
                                        price: '{{ $program->price }}',
                                        mentor_id: '{{ $program->mentor_id }}',
                                        type: '{{ $program->type }}'
                                    };
                                    showEditModal = true;
                                " class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center text-white hover:bg-white hover:text-slate-900 transition-all">
                                    <i class="fas fa-edit text-xs"></i>
                                </button>
                                <form action="{{ route('admin.programs.delete', $program->id) }}" method="POST" onsubmit="return confirm('Hapus program ini?')">
                                    @csrf @method('DELETE')
                                    <button class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center text-white hover:bg-red-500 transition-all border border-white/5">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mt-8 pt-6 border-t border-white/10">
                            <div>
                                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Mentor Pendamping</p>
                                <div class="flex items-center gap-2 text-slate-200">
                                    <span class="text-sm font-bold">{{ $program->mentor_name ?? 'Belum Ada' }}</span>
                                </div>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Total Peserta</p>
                                <div class="flex items-center gap-2 text-slate-200">
                                    <i class="fas fa-users text-[10px] text-slate-500"></i>
                                    <span class="text-sm font-bold">{{ $program->jumlah_peserta ?? 0 }} Siswa Aktif</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-[3rem] p-20 text-center border-2 border-dashed border-slate-100">
            <img src="https://illustrations.popsy.co/slate/empty-folder.svg" class="w-48 mx-auto mb-6 opacity-50" alt="Empty">
            <h3 class="text-xl font-bold text-slate-800">Katalog Masih Kosong</h3>
            <p class="text-slate-400 max-w-xs mx-auto mt-2">Belum ada program bimbingan yang dibuat.</p>
        </div>
    @endif

    {{-- Modal Edit --}}
    <div x-show="showEditModal" class="fixed inset-0 z-[99] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak x-transition>
        <div @click.away="showEditModal = false" class="bg-white w-full max-w-md rounded-[2.5rem] p-10 shadow-2xl">
            <h3 class="text-2xl font-black text-slate-800 mb-2">Konfigurasi Program</h3>
            <p class="text-sm text-slate-400 mb-8 font-medium">Perbarui informasi paket belajar secara real-time.</p>
            
            <form :action="'{{ url('admin/programs/update') }}/' + editData.id" method="POST">
                @csrf @method('PUT')
                <div class="space-y-5">
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase ml-2 tracking-widest">Nama Paket</label>
                        <input type="text" name="name" x-model="editData.name" class="w-full px-5 py-4 rounded-2xl bg-slate-50 border-none focus:ring-2 focus:ring-blue-500 outline-none font-bold text-slate-700 mt-1">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase ml-2 tracking-widest">Jenjang</label>
                            <select name="jenjang" x-model="editData.jenjang" class="w-full px-5 py-4 rounded-2xl bg-slate-50 border-none outline-none font-bold text-slate-700 mt-1">
                                <option value="SD">SD</option>
                                <option value="SMP">SMP</option>
                                <option value="SMA">SMA</option>
                                <option value="Umum">Umum</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase ml-2 tracking-widest">Harga (Rp)</label>
                            <input type="number" name="price" x-model="editData.price" class="w-full px-5 py-4 rounded-2xl bg-slate-50 border-none outline-none font-bold text-slate-700 mt-1">
                        </div>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase ml-2 tracking-widest">Mentor Bertanggung Jawab</label>
                        <select name="mentor_id" x-model="editData.mentor_id" class="w-full px-5 py-4 rounded-2xl bg-slate-50 border-none outline-none font-bold text-slate-700 mt-1">
                            <option value="">Tanpa Mentor</option>
                            @foreach($mentors as $mentor)
                                <option value="{{ $mentor->id }}">{{ $mentor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-10 flex gap-4">
                    <button type="button" @click="showEditModal = false" class="flex-1 py-4 rounded-2xl font-black text-slate-400 bg-slate-50 hover:bg-slate-100 transition">Batal</button>
                    <button type="submit" class="flex-1 py-4 rounded-2xl font-black text-white bg-slate-800 hover:bg-slate-700 transition shadow-xl">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
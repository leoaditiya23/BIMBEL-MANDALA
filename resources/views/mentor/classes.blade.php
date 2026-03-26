@extends('mentor.dashboard_mentor_layout')

@section('mentor_content')
<div x-data="{ 
    activeTab: '{{ request()->query('id') ? 'semua' : 'semua' }}', 
    searchQuery: '',
    modalMateri: false, 
    modalAbsen: false,
    modalTugas: false,
    isAbsenOpen: false, 
    selectedClass: {id: '', name: '', students: [], materials: [], total_sessions: 0},
    fileName: '',
    editingMaterial: null,

    // Fungsi untuk membuka modal absensi dengan data yang benar
    openAbsenModal(item) {
        this.selectedClass = JSON.parse(JSON.stringify(item));
        this.isAbsenOpen = !!item.is_absen_active;
        this.modalAbsen = true;
    },

    // Fungsi untuk membuka modal materi
    openMateriModal(item) {
        this.selectedClass = JSON.parse(JSON.stringify(item));
        this.editingMaterial = null;
        this.fileName = '';
        this.modalMateri = true;
    },

    // Fungsi untuk membuka modal nilai
    openNilaiModal(item) {
        this.selectedClass = JSON.parse(JSON.stringify(item));
        this.modalTugas = true;
    },

    toggleAbsen(status) {
        if(!this.selectedClass.id) return;
        
        fetch('{{ route('mentor.toggleAbsen') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                class_id: this.selectedClass.id,
                is_active: status
            })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                this.isAbsenOpen = status;
                window.location.reload();
            }
        }).catch(err => console.error('Error toggling attendance:', err));
    }
}" x-transition:enter="transition ease-out duration-300" class="min-h-full flex flex-col pb-10">
    
    {{-- AREA KONTEN UTAMA --}}
    <div class="flex-1">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <div class="bg-white p-6 rounded-[25px] border border-slate-100 shadow-sm hover:shadow-md transition-all">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600">
                        <i class="fas fa-chalkboard-teacher text-lg"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Kelas</p>
                        <h4 class="text-xl font-black text-slate-800">{{ count($classes) }} <span class="text-xs font-bold text-slate-400">Grup</span></h4>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-[25px] border border-slate-100 shadow-sm hover:shadow-md transition-all">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600">
                        <i class="fas fa-book text-lg"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Materi</p>
                        <h4 class="text-xl font-black text-slate-800">
                            {{ $classes->sum(function($c) { return count($c->materials ?? []); }) }}
                            <span class="text-xs font-bold text-slate-400">Sesi</span>
                        </h4>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-[25px] border border-slate-100 shadow-sm hover:shadow-md transition-all">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-orange-50 rounded-2xl flex items-center justify-center text-orange-600">
                        <i class="fas fa-users text-lg"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Siswa</p>
                        <h4 class="text-xl font-black text-slate-800">{{ $classes->sum('student_count') }} <span class="text-xs font-bold text-slate-400">Orang</span></h4>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-[25px] border border-slate-100 shadow-sm hover:shadow-md transition-all">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-rose-50 rounded-2xl flex items-center justify-center text-rose-600">
                        <i class="fas fa-tower-broadcast text-lg"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Absensi Aktif</p>
                        <h4 class="text-xl font-black text-slate-800">
                            {{ $classes->where('is_absen_active', true)->count() }}
                            <span class="text-xs font-bold text-slate-400">Kelas</span>
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-8 flex flex-col xl:flex-row justify-between items-start xl:items-center gap-6">
            <div>
                <h2 class="text-3xl font-black text-slate-800 tracking-tighter">Manajemen Kelas</h2>
                <p class="text-sm text-slate-500 mt-1 font-medium italic">"Membimbing dengan hati, mencetak prestasi."</p>
            </div>
            
            <div class="flex flex-col md:flex-row items-center gap-4 w-full xl:w-auto">
                <div class="relative w-full md:w-72">
                    <input type="text" x-model="searchQuery" placeholder="Cari nama kelas..." 
                        class="w-full px-5 py-3 bg-white border border-slate-200 rounded-[18px] text-xs font-bold focus:ring-2 focus:ring-indigo-500 transition-all shadow-sm outline-none placeholder:text-slate-400">
                </div>

                <div class="flex bg-slate-200/50 p-1 rounded-[20px] w-full md:w-auto">
                    <button @click="activeTab = 'semua'" :class="activeTab === 'semua' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500'" class="flex-1 md:flex-none px-6 py-2 rounded-[15px] text-xs font-bold transition-all duration-200">Semua</button>
                    <button @click="activeTab = 'aktif'" :class="activeTab === 'aktif' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500'" class="flex-1 md:flex-none px-6 py-2 rounded-[15px] text-xs font-bold transition-all duration-200">Sesi Aktif</button>
                </div>
            </div>
        </div>

        @if(request()->query('id'))
        <div class="mb-6 bg-indigo-600 p-4 rounded-2xl flex items-center justify-between shadow-lg shadow-indigo-100 animate-in fade-in slide-in-from-top-4 duration-700">
            <div class="flex items-center gap-3 text-white">
                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-bullseye text-sm"></i>
                </div>
                <p class="text-xs font-bold uppercase tracking-wider">Mode Fokus: Menampilkan kelas yang dipilih dari jadwal</p>
            </div>
            <a href="{{ route('mentor.classes') }}" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-xl text-[10px] font-black uppercase transition-all">Tampilkan Semua</a>
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 flex-1">
            @forelse($classes as $class)
                @php 
                    $isFocused = request()->query('id') == $class->id; 
                @endphp
                
                @if(!request()->query('id') || $isFocused)
                    <div x-show="(activeTab === 'semua' || (activeTab === 'aktif' && {{ ($class->is_absen_active ?? false) ? 'true' : 'false' }})) && ('{{ strtolower($class->name) }}'.includes(searchQuery.toLowerCase()))"
                         x-transition
                         data-class-id="{{ $class->id }}"
                         class="bg-white rounded-[25px] border {{ $isFocused ? 'border-indigo-500 ring-4 ring-indigo-50 shadow-2xl scale-[1.02]' : 'border-slate-100 shadow-sm' }} hover:shadow-xl transition-all duration-500 group overflow-hidden flex flex-col h-full">
                        
                        <div class="p-6 flex-1">
                            <div class="flex items-start justify-between mb-4">
                                <div class="p-3 {{ $isFocused ? 'bg-indigo-600 text-white' : 'bg-indigo-50 text-indigo-600' }} rounded-[18px] group-hover:bg-indigo-600 group-hover:text-white transition-all duration-500">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                                @if($class->is_absen_active ?? false)
                                    <span class="flex items-center gap-1.5 text-[9px] font-black px-3 py-1 bg-rose-100 text-rose-600 rounded-full uppercase tracking-wider animate-pulse">
                                        <span class="w-1.5 h-1.5 bg-rose-600 rounded-full"></span> Absensi Dibuka
                                    </span>
                                @else
                                    <span class="text-[9px] font-black px-3 py-1 bg-slate-100 text-slate-400 rounded-full uppercase tracking-wider">Materi Standby</span>
                                @endif
                            </div>
                            
                            <h3 class="font-black text-slate-800 text-lg leading-tight mb-1 group-hover:text-indigo-600 transition-colors">{{ $class->name }}</h3>
                            
                            <div class="flex flex-col gap-2 mt-1 mb-4">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">• {{ $class->type ?? 'Reguler' }}</span>
                                
                                <div class="flex flex-wrap gap-1.5">
                                    @php
                                        $listJadwal = !empty($class->jadwal_detail) ? explode(',', $class->jadwal_detail) : [];
                                    @endphp

                                    @if(count($listJadwal) > 0)
                                        @foreach($listJadwal as $j)
                                            <div class="flex items-center gap-2 bg-indigo-50 px-2 py-1 rounded-md border border-indigo-100">
                                                <i class="far fa-calendar-alt text-[9px] text-indigo-500"></i>
                                                <span class="text-[9px] font-black text-indigo-600 uppercase">{{ trim($j) }}</span>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="flex items-center gap-2 bg-indigo-50 px-2 py-0.5 rounded-md border border-indigo-100">
                                            <i class="far fa-calendar-alt text-[9px] text-indigo-500"></i>
                                            <span class="text-[9px] font-black text-indigo-600 uppercase">{{ $class->hari ?? 'TBA' }}</span>
                                            <span class="text-indigo-200">|</span>
                                            <i class="far fa-clock text-[9px] text-indigo-500"></i>
                                            <span class="text-[9px] font-black text-indigo-600">{{ $class->jam ?? '--:--' }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-4">
                                <p class="text-[9px] font-black text-slate-400 uppercase mb-2 tracking-widest">Siswa Terdaftar :</p>
                                <div class="flex flex-wrap gap-1.5">
                                    @forelse($class->students ?? [] as $student)
                                        <div class="flex items-center gap-1.5 bg-slate-50 border border-slate-100 px-2 py-1 rounded-lg">
                                            <div class="w-4 h-4 rounded bg-indigo-100 text-indigo-600 flex items-center justify-center text-[7px] font-black">
                                                {{ strtoupper(substr($student->name, 0, 1)) }}
                                            </div>
                                            <span class="text-[9px] font-bold text-slate-600">{{ $student->name }}</span>
                                        </div>
                                    @empty
                                        <p class="text-[9px] font-bold text-slate-300 italic">Belum ada siswa</p>
                                    @endforelse
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3 mb-6">
                                <div class="bg-slate-50 p-3 rounded-[20px] border border-transparent hover:border-indigo-100 transition-all">
                                    <p class="text-[9px] font-black text-slate-400 uppercase mb-1">Total Siswa</p>
                                    <p class="font-black text-slate-700 text-sm">
                                        <i class="fas fa-user-friends mr-1 text-indigo-400"></i> 
                                        {{ $class->student_count ?? count($class->students ?? []) }} 
                                        <span class="text-[10px]">Pax</span>
                                    </p>
                                </div>

                                <div class="bg-slate-50 p-3 rounded-[20px] border border-transparent hover:border-indigo-100 transition-all">
                                    <p class="text-[9px] font-black text-slate-400 uppercase mb-1">Sesi Selesai</p>
                                    <p class="font-black text-slate-700 text-sm">
                                        <i class="fas fa-check-circle mr-1 text-emerald-400"></i> 
                                        <span>{{ $class->pertemuan_selesai ?? 0 }}</span> / <span>{{ $class->total_sessions ?? 8 }}</span>
                                    </p>
                                </div>
                            </div>

                            <div class="mb-6">
                                @php
                                    $pertemuanSelesai = $class->pertemuan_selesai ?? 0;
                                    $totalSesi = $class->total_sessions ?? 8;
                                    $progress = ($totalSesi > 0) ? ($pertemuanSelesai / $totalSesi) * 100 : 0;
                                @endphp
                                <div class="flex justify-between text-[10px] font-black mb-2">
                                    <span class="text-slate-400 uppercase tracking-widest">Attendance Progress</span>
                                    <span class="text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md">{{ round($progress) }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden p-0.5">
                                    <div class="bg-gradient-to-r from-indigo-400 to-indigo-600 h-full rounded-full transition-all duration-1000 relative" style="width: {{ $progress }}%">
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <button @click="openAbsenModal({{ json_encode($class) }})" 
                                        class="py-3 bg-slate-900 text-white rounded-[15px] text-[9px] font-black uppercase tracking-widest hover:scale-[1.02] active:scale-95 transition-all shadow-lg shadow-slate-200">
                                    <i class="fas fa-fingerprint mr-1.5"></i> Absensi
                                </button>
                                <button @click="openNilaiModal({{ json_encode($class) }})" 
                                        class="py-3 bg-indigo-600 text-white rounded-[15px] text-[9px] font-black uppercase tracking-widest hover:scale-[1.02] active:scale-95 transition-all shadow-lg shadow-indigo-100">
                                    <i class="fas fa-star mr-1.5"></i> Nilai
                                </button>
                            </div>
                        </div>

                        <button @click="openMateriModal({{ json_encode($class) }})" 
                                class="w-full px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-between items-center hover:bg-white transition-colors group/btn">
                            <span class="text-[10px] font-black text-slate-500 group-hover/btn:text-indigo-600 transition uppercase tracking-widest">
                                <i class="fas fa-layer-group mr-1.5"></i> Input Materi & Sesi
                            </span>
                            <i class="fas fa-arrow-right text-slate-300 group-hover/btn:text-indigo-600 group-hover/btn:translate-x-1 transition-all"></i>
                        </button>
                    </div>
                @endif
            @empty
                <div class="col-span-full bg-white p-20 rounded-[40px] border-2 border-dashed border-slate-100 text-center">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-200 text-3xl">
                        <i class="fas fa-calendar-times"></i>
                    </div>
                    <h3 class="text-xl font-black text-slate-800">Tidak Ada Jadwal</h3>
                    <p class="text-slate-400 font-bold mt-2">Belum ada kelas yang ditugaskan untuk akun Anda saat ini.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- MODAL ABSEN --}}
    <div x-show="modalAbsen" class="fixed inset-0 z-[70] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm" x-cloak x-transition>
        <div @click.away="modalAbsen = false" class="bg-white rounded-[35px] shadow-2xl w-full max-w-md overflow-hidden border border-white">
            <div class="p-8">
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h3 class="text-2xl font-black text-slate-800 leading-tight">Sesi Presensi</h3>
                        <p class="text-[10px] font-black text-indigo-500 uppercase tracking-widest mt-1" x-text="selectedClass.name"></p>
                    </div>
                    <button @click="modalAbsen = false" class="text-slate-300 hover:text-rose-500 transition-colors"><i class="fas fa-times-circle text-2xl"></i></button>
                </div>

                <div class="bg-indigo-600 p-6 rounded-[25px] text-white shadow-xl shadow-indigo-100 mb-8 relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 text-white/10 text-7xl rotate-12 group-hover:rotate-45 transition-transform duration-700">
                        <i class="fas fa-signal"></i>
                    </div>
                    <p class="text-[9px] font-black uppercase text-indigo-200 mb-4 tracking-[0.2em]">Remote Control Absensi</p>
                    <div class="flex gap-3">
                        <button type="button" @click="toggleAbsen(true)" :disabled="isAbsenOpen" :class="isAbsenOpen ? 'bg-white/20 text-white cursor-not-allowed opacity-50' : 'bg-white text-indigo-600 hover:bg-indigo-50 shadow-lg'" class="flex-1 py-3 rounded-[15px] text-[10px] font-black uppercase tracking-widest transition-all">Buka Sesi</button>
                        <button type="button" @click="toggleAbsen(false)" :disabled="!isAbsenOpen" :class="!isAbsenOpen ? 'bg-white/20 text-white cursor-not-allowed opacity-50' : 'bg-rose-500 text-white hover:bg-rose-600 shadow-lg'" class="flex-1 py-3 rounded-[15px] text-[10px] font-black uppercase tracking-widest transition-all">Tutup Sesi</button>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex justify-between items-center px-1">
                        <label class="text-[10px] font-black uppercase text-slate-400 tracking-tighter">Monitoring Realtime :</label>
                        <span class="text-[10px] font-black text-indigo-600 px-2 py-1 bg-indigo-50 rounded-lg">
                            <span x-text="selectedClass.students ? selectedClass.students.filter(s => s.status && s.status.trim() !== '').length : 0"></span> / <span x-text="selectedClass.students ? selectedClass.students.length : 0"></span> Siswa Berpartisipasi
                        </span>
                    </div>
                    <div class="max-h-60 overflow-y-auto pr-2 space-y-2 custom-scrollbar">
                        <template x-for="student in selectedClass.students" :key="student.id">
                            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-[20px] border border-transparent hover:border-indigo-100 transition-all">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-white shadow-sm flex items-center justify-center text-[10px] font-black text-indigo-500" x-text="student.name.substring(0,2).toUpperCase()"></div>
                                    <div>
                                        <span class="text-xs font-black text-slate-700 block" x-text="student.name"></span>
                                        <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest" 
                                              x-text="'Pertemuan Ke-' + (selectedClass.pertemuan_selesai || 0)"></span>
                                    </div>
                                </div>
                                
                                <div class="text-right flex flex-col items-end gap-1">
                                    <template x-if="student.status">
                                        <span :class="{
                                            'bg-emerald-500': student.status === 'Hadir',
                                            'bg-amber-500': student.status === 'Izin',
                                            'bg-rose-500': student.status === 'Sakit'
                                        }" class="text-[8px] font-black px-3 py-1.5 text-white rounded-full uppercase tracking-widest shadow-sm" x-text="student.status"></span>
                                    </template>
                                    
                                    <template x-if="!student.status">
                                        <span class="text-[8px] font-black px-3 py-1.5 bg-slate-200 text-slate-400 rounded-full uppercase tracking-widest animate-pulse">Belum Absen</span>
                                    </template>

                                    <span class="text-[7px] font-black text-slate-400 uppercase tracking-tighter">
                                        {{ date('d M Y') }}
                                    </span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL MATERI --}}
    <div x-show="modalMateri" class="fixed inset-0 z-[70] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md overflow-y-auto" x-cloak x-transition>
        <div @click.away="modalMateri = false" 
             class="bg-white rounded-[40px] shadow-2xl w-full max-w-5xl overflow-hidden border border-white flex flex-col md:flex-row h-[85vh]">
            
            <div class="w-full md:w-5/12 bg-slate-50 border-r border-slate-100 flex flex-col h-full">
                <div class="p-8 border-b border-slate-200 bg-white">
                    <h3 class="text-xl font-black text-slate-800 tracking-tight">Kurikulum Sesi</h3>
                    <p class="text-[10px] font-bold text-indigo-500 uppercase mt-1">Total: <span x-text="selectedClass.materials ? selectedClass.materials.length : 0"></span> Materi Terunggah</p>
                </div>
                
                <div class="flex-1 overflow-y-auto p-6 space-y-3 custom-scrollbar">
                    <template x-if="selectedClass.materials && selectedClass.materials.length > 0">
                        <template x-for="material in selectedClass.materials" :key="material.id">
                            <button @click="editingMaterial = material; fileName = ''" 
                                    :class="editingMaterial?.id === material.id ? 'border-indigo-600 bg-indigo-50 ring-2 ring-indigo-100' : 'bg-white border-slate-200'"
                                    class="w-full text-left p-4 rounded-[22px] border flex items-center justify-between group transition-all duration-300">
                                <div class="flex items-center gap-4">
                                    <div :class="editingMaterial?.id === material.id ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-400 group-hover:bg-slate-800 group-hover:text-white'" 
                                         class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-xs transition-colors" x-text="material.session_number"></div>
                                    <div>
                                        <h4 class="font-black text-slate-800 text-[13px] leading-tight" x-text="material.title"></h4>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span x-show="material.video_url" class="text-[8px] font-black text-indigo-400 uppercase"><i class="fas fa-video mr-0.5"></i> Video</span>
                                            <span x-show="material.file_path" class="text-[8px] font-black text-rose-400 uppercase"><i class="fas fa-file-pdf mr-0.5"></i> PDF</span>
                                        </div>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-right text-slate-200 group-hover:text-indigo-500 text-xs"></i>
                            </button>
                        </template>
                    </template>
                </div>
            </div>

            <div class="w-full md:w-7/12 p-10 bg-white flex flex-col relative overflow-y-auto custom-scrollbar">
                <div class="absolute top-8 right-8 flex items-center gap-3 z-50">
                    <button x-show="editingMaterial" @click="editingMaterial = null; fileName = ''" 
                            class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center hover:bg-indigo-100 transition-all shadow-sm">
                        <i class="fas fa-plus"></i>
                    </button>
                    <button @click="modalMateri = false" 
                            class="w-10 h-10 bg-slate-50 text-slate-400 rounded-xl flex items-center justify-center hover:bg-rose-50 hover:text-rose-500 transition-all shadow-sm border border-slate-100">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <div class="mb-10">
                    <h3 class="text-3xl font-black text-slate-800 leading-none" x-text="editingMaterial ? 'Update Sesi' : 'Buat Sesi Baru'"></h3>
                    <p class="text-sm font-bold text-slate-400 mt-3" x-text="'Kelas: ' + selectedClass.name"></p>
                </div>
                
                <form :action="editingMaterial ? `{{ url('mentor/materials/update') }}/${editingMaterial.id}` : '{{ route('mentor.storeMaterial') }}'" 
                      method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <template x-if="editingMaterial"><input type="hidden" name="_method" value="PUT"></template>
                    <input type="hidden" name="program_id" :value="selectedClass.id">
                    
                    <div class="grid grid-cols-5 gap-4">
                        <div class="col-span-1">
                            <label class="text-[10px] font-black uppercase text-slate-400 ml-1">No. Sesi</label>
                            <input type="number" name="session_number" required :value="editingMaterial ? editingMaterial.session_number : (selectedClass.materials ? selectedClass.materials.length + 1 : 1)" class="w-full mt-2 bg-slate-50 border-none rounded-[18px] text-sm font-black focus:ring-2 focus:ring-indigo-500 p-4">
                        </div>
                        <div class="col-span-4">
                            <label class="text-[10px] font-black uppercase text-slate-400 ml-1">Judul Materi Pembelajaran</label>
                            <input type="text" name="title" required :value="editingMaterial ? editingMaterial.title : ''" placeholder="Contoh: Dasar-dasar Pemrograman" class="w-full mt-2 bg-slate-50 border-none rounded-[18px] text-sm font-black focus:ring-2 focus:ring-indigo-500 p-4">
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400 ml-1 tracking-widest">URL Video (Youtube/Lainnya)</label>
                        <div class="relative mt-2">
                            <input type="url" name="video_url" :value="editingMaterial ? editingMaterial.video_url : ''" 
                                   placeholder="https://youtube.com/watch?v=..." 
                                   class="w-full px-5 py-4 bg-slate-50 border-none rounded-[18px] text-xs font-bold focus:ring-2 focus:ring-indigo-500 shadow-inner">
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400 ml-1 tracking-widest">Modul Dokumentasi (PDF/PPT)</label>
                        <div class="mt-2 p-8 border-2 border-dashed border-slate-100 rounded-[30px] text-center hover:border-indigo-400 hover:bg-indigo-50 transition-all relative group cursor-pointer">
                            <input type="file" name="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" @change="fileName = $event.target.files[0].name">
                            <div class="space-y-3">
                                <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center mx-auto shadow-sm group-hover:scale-110 transition-transform">
                                    <i class="fas fa-cloud-upload-alt text-indigo-500"></i>
                                </div>
                                <p class="text-xs font-black text-slate-500 uppercase tracking-tighter" x-text="fileName || 'Tarik file atau klik untuk telusuri'"></p>
                                <p class="text-[9px] font-bold text-slate-300">Format: PDF, PPTX (Maks. 10MB)</p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full py-5 bg-slate-900 text-white rounded-[22px] text-xs font-black uppercase tracking-[0.2em] shadow-xl shadow-slate-200 hover:bg-indigo-600 transition-all active:scale-95">
                            <i class="fas fa-save mr-2"></i> Konfirmasi Sesi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL NILAI --}}
    <div x-show="modalTugas" class="fixed inset-0 z-[70] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm" x-cloak x-transition>
        <div @click.away="modalTugas = false" class="bg-white rounded-[40px] shadow-2xl w-full max-w-2xl overflow-hidden border border-white flex flex-col h-[85vh]">
            <div class="p-10 pb-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-3xl font-black text-slate-800 leading-tight">Penilaian Siswa</h3>
                        <p class="text-sm font-bold text-indigo-500 mt-2" x-text="selectedClass.name"></p>
                    </div>
                    <button @click="modalTugas = false" class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-300 hover:text-rose-500 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto px-10 pb-10 space-y-6 custom-scrollbar">
                <template x-for="student in selectedClass.students" :key="student.id">
                    <div class="bg-white rounded-[30px] border-2 border-slate-100 shadow-sm overflow-hidden hover:border-indigo-200 transition-all">
                        <div class="bg-slate-50 p-5 flex items-center gap-4 border-b border-slate-100">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-600 flex items-center justify-center text-white shadow-lg shadow-indigo-100">
                                <span class="font-black text-sm" x-text="student.name.substring(0,2).toUpperCase()"></span>
                            </div>
                            <div>
                                <h4 class="text-sm font-black text-slate-800 leading-tight" x-text="student.name"></h4>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Student ID: #<span x-text="student.id"></span></p>
                            </div>
                        </div>

                        <form action="{{ route('mentor.storeGrade') }}" method="POST" class="p-6 space-y-4">
                            @csrf
                            <input type="hidden" name="program_id" :value="selectedClass.id">
                            <input type="hidden" name="student_id" :value="student.id">
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-[10px] font-black uppercase text-slate-400 ml-1">Kategori Tugas</label>
                                    <input type="text" name="title" placeholder="Misal: Kuis 1" required class="w-full mt-2 bg-slate-50 border-none rounded-[15px] text-[11px] font-black p-3 focus:ring-2 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="text-[10px] font-black uppercase text-slate-400 ml-1">Skor (0-100)</label>
                                    <input type="number" name="score" min="0" max="100" placeholder="0" required class="w-full mt-2 bg-slate-50 border-none rounded-[15px] text-[11px] font-black p-3 focus:ring-2 focus:ring-indigo-500">
                                </div>
                            </div>

                            <div>
                                <label class="text-[10px] font-black uppercase text-slate-400 ml-1">Feedback Mentor</label>
                                <textarea name="note" rows="2" placeholder="Tulis catatan perkembangan..." class="w-full mt-2 bg-slate-50 border-none rounded-[15px] text-[11px] font-black p-3 focus:ring-2 focus:ring-indigo-500"></textarea>
                            </div>

                            <button type="submit" class="w-full py-3.5 bg-slate-900 text-white rounded-[18px] text-[10px] font-black uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-lg shadow-slate-100 active:scale-95">
                                <i class="fas fa-paper-plane mr-2"></i> Simpan Nilai <span x-text="student.name.split(' ')[0]"></span>
                            </button>
                        </form>
                    </div>
                </template>
                
                <template x-if="!selectedClass.students || selectedClass.students.length === 0">
                    <div class="text-center py-10">
                        <i class="fas fa-user-slash text-4xl text-slate-200 mb-4"></i>
                        <p class="text-slate-400 font-bold text-sm">Belum ada siswa di kelas ini.</p>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<style>
    html, body { 
        height: auto !important; 
        min-height: 100% !important; 
        background-color: #fcfcfd; 
        font-family: 'Plus Jakarta Sans', sans-serif;
        overflow-y: auto !important;
    }
    
    [x-cloak] { display: none !important; }
    
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 20px; }
    
    @keyframes shimmer {
        0% { transform: translateX(-150%) skewX(-12deg); }
        100% { transform: translateX(150%) skewX(-12deg); }
    }
    .animate-shimmer { animation: shimmer 2s infinite linear; }

    .backdrop-blur-md {
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
    }
</style>
@endsection
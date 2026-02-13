@extends('admin.dashboard_admin')

@section('admin_content')
{{-- REVISI: Ganti p-8 menjadi w-full agar alignment konsisten dengan navbar --}}
<div class="w-full">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-4">
        <div>
            <h2 class="text-3xl font-black text-slate-800 tracking-tight">Pesan Masuk</h2>
            <p class="text-slate-500 font-medium mt-1">Kelola pesan dan pertanyaan dari calon siswa.</p>
        </div>
        <div class="flex items-center space-x-3 bg-white px-6 py-3 rounded-2xl shadow-sm border border-slate-100">
            <i class="fas fa-inbox text-orange-500"></i>
            <span class="font-bold text-slate-700">{{ $messages->count() }} Total Pesan</span>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-700 rounded-2xl flex items-center animate-bounce">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-8 py-6 text-left text-xs font-black text-slate-400 uppercase tracking-[0.2em]">Pengirim</th>
                        <th class="px-8 py-6 text-left text-xs font-black text-slate-400 uppercase tracking-[0.2em]">Isi Pesan</th>
                        <th class="px-8 py-6 text-left text-xs font-black text-slate-400 uppercase tracking-[0.2em]">Tanggal</th>
                        <th class="px-8 py-6 text-center text-xs font-black text-slate-400 uppercase tracking-[0.2em]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($messages as $msg)
                        <tr class="hover:bg-blue-50/30 transition-colors group">
                            <td class="px-8 py-6">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center font-bold">
                                        {{ substr($msg->name, 0, 1) }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-700">{{ $msg->name }}</span>
                                        <span class="text-[11px] text-slate-400 font-medium">{{ $msg->email ?? 'Tanpa Email' }}</span>
                                        @if(isset($msg->whatsapp))
                                            <span class="text-[10px] text-green-500 font-bold flex items-center">
                                                <i class="fab fa-whatsapp mr-1"></i> {{ $msg->whatsapp }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="max-w-md">
                                    <p class="text-slate-600 leading-relaxed">{{ $msg->message }}</p>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-sm text-slate-400 font-medium">
                                {{ $msg->created_at->diffForHumans() }}
                                <br>
                                <span class="text-[10px] uppercase opacity-60">{{ $msg->created_at->format('d M Y, H:i') }}</span>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    {{-- Tombol Balas WA --}}
                                    @if(isset($msg->whatsapp))
                                        @php
                                            $phone = preg_replace('/[^0-9]/', '', $msg->whatsapp);
                                            if (str_starts_with($phone, '0')) {
                                                $phone = '62' . substr($phone, 1);
                                            }
                                            $waText = urlencode("Halo " . $msg->name . ", saya Admin Mandala Bimbel. Menanggapi pesan Anda...");
                                        @endphp
                                        <a href="https://wa.me/{{ $phone }}?text={{ $waText }}" 
                                           target="_blank"
                                           class="w-10 h-10 bg-white border border-slate-100 text-green-500 rounded-xl hover:bg-green-500 hover:text-white hover:shadow-lg hover:shadow-green-200 transition-all flex items-center justify-center">
                                            <i class="fab fa-whatsapp text-lg"></i>
                                        </a>
                                    @endif

                                    {{-- Tombol Hapus --}}
                                    <form action="{{ route('admin.messages.delete', $msg->id) }}" method="POST" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="w-10 h-10 bg-white border border-slate-100 text-red-400 rounded-xl hover:bg-red-500 hover:text-white hover:shadow-lg hover:shadow-red-200 transition-all flex items-center justify-center">
                                            <i class="fas fa-trash-alt text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-8 py-20 text-center text-slate-400 italic">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-comment-slash text-4xl mb-4 opacity-20"></i>
                                    <p>Belum ada pesan masuk untuk saat ini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
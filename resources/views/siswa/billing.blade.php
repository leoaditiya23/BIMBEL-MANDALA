@extends('siswa.dashboard_siswa_layout')

@section('siswa_content')
<div x-data="{ openModal: false, imgSrc: '' }" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4">
    <div class="mb-8">
        <h2 class="text-3xl font-black text-slate-800 tracking-tighter">Pembayaran</h2>
        <p class="text-sm text-slate-500 mt-1 font-medium">Kelola semua transaksi dan pantau status verifikasi Anda</p>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest italic">Total Terbayar</p>
            <p class="text-3xl font-black text-emerald-600">
                Rp {{ number_format($payments->where('status_pembayaran', 'verified')->sum('total_harga'), 0, ',', '.') }}
            </p>
        </div>
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest italic">Menunggu Verifikasi</p>
            <p class="text-3xl font-black text-orange-500">
                Rp {{ number_format($payments->where('status_pembayaran', 'pending')->sum('total_harga'), 0, ',', '.') }}
            </p>
        </div>
        <div class="bg-slate-900 p-6 rounded-[2rem] text-white shadow-lg">
            <p class="text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest text-slate-400 italic">Total Transaksi</p>
            <p class="text-3xl font-black">
                Rp {{ number_format($payments->sum('total_harga'), 0, ',', '.') }}
            </p>
        </div>
    </div>

    <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
        <h3 class="font-black text-slate-800 mb-6 flex items-center uppercase text-sm tracking-wider">
            <i class="fas fa-history mr-2 text-blue-600"></i> Riwayat Pembayaran
        </h3>
        
        @if($payments && count($payments) > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b-2 border-slate-50">
                            <th class="text-left py-4 px-4 font-black text-slate-400 text-[10px] uppercase tracking-widest italic">Program</th>
                            <th class="text-left py-4 px-4 font-black text-slate-400 text-[10px] uppercase tracking-widest italic">Nominal</th>
                            <th class="text-left py-4 px-4 font-black text-slate-400 text-[10px] uppercase tracking-widest italic">Status</th>
                            <th class="text-left py-4 px-4 font-black text-slate-400 text-[10px] uppercase tracking-widest italic">Tanggal</th>
                            <th class="text-right py-4 px-4 font-black text-slate-400 text-[10px] uppercase tracking-widest italic">Bukti</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($payments as $payment)
                            <tr class="group hover:bg-slate-50/50 transition-all">
                                <td class="py-5 px-4">
                                    <p class="font-bold text-slate-800">{{ $payment->program_name }}</p>
                                    <p class="text-[10px] text-slate-400 font-medium tracking-tight">ID TX #{{ $payment->id }}</p>
                                </td>
                                <td class="py-5 px-4">
                                    <p class="font-black text-slate-800 text-sm">Rp {{ number_format($payment->total_harga ?? 0, 0, ',', '.') }}</p>
                                    @if(isset($payment->payment_code))
                                        <p class="text-[10px] text-orange-500 font-bold uppercase italic">Kode: {{ $payment->payment_code }}</p>
                                    @endif
                                </td>
                                <td class="py-5 px-4">
                                    @if($payment->status_pembayaran === 'verified')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[9px] font-black uppercase bg-emerald-100 text-emerald-600">
                                            <i class="fas fa-check-circle mr-1"></i> Berhasil
                                        </span>
                                    @elseif($payment->status_pembayaran === 'pending')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[9px] font-black uppercase bg-orange-100 text-orange-600 animate-pulse">
                                            <i class="fas fa-clock mr-1"></i> Proses
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[9px] font-black uppercase bg-red-100 text-red-600">
                                            <i class="fas fa-times-circle mr-1"></i> Gagal
                                        </span>
                                    @endif
                                </td>
                                <td class="py-5 px-4 text-slate-500 text-xs font-bold italic uppercase">
                                    {{ \Carbon\Carbon::parse($payment->created_at)->format('d M Y') }}
                                </td>
                                <td class="py-5 px-4 text-right">
                                    @if($payment->bukti_pembayaran)
                                        <button type="button" 
                                            @click="imgSrc = '{{ asset('uploads/bukti/' . $payment->bukti_pembayaran) }}'; openModal = true" 
                                            class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all duration-300 flex items-center justify-center ml-auto shadow-sm">
                                            <i class="fas fa-image"></i>
                                        </button>
                                    @else
                                        <span class="text-slate-300 text-[10px] italic font-medium">No Image</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-20 bg-slate-50 rounded-[2rem] border-2 border-dashed border-slate-100">
                <i class="fas fa-wallet text-slate-200 text-5xl mb-4"></i>
                <p class="text-slate-500 font-black uppercase tracking-widest text-sm">Belum Ada Transaksi</p>
                <p class="text-slate-400 text-xs mt-1 font-medium italic">Silakan pilih program bimbingan untuk memulai pendaftaran.</p>
            </div>
        @endif
    </div>

    {{-- Modal Image --}}
    <div x-show="openModal" 
         x-cloak
         x-transition:enter="transition opacity-0 duration-300"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition opacity-100 duration-200"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-900/95 p-4 backdrop-blur-sm"
         @click="openModal = false">
        
        <div class="relative max-w-2xl w-full flex flex-col items-center" @click.stop>
            <div class="bg-white p-2 rounded-2xl shadow-2xl relative overflow-hidden">
                {{-- GAMBAR DILETAKKAN DISINI --}}
                <img :src="imgSrc" class="w-full h-auto max-h-[80vh] rounded-xl object-contain shadow-inner" @click.away="openModal = false">
            </div>
            
            <button @click="openModal = false" class="mt-6 px-8 py-3 bg-white text-slate-900 hover:bg-blue-500 hover:text-white rounded-full text-[10px] font-black uppercase tracking-widest transition-all shadow-xl">
                Tutup Pratinjau
            </button>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
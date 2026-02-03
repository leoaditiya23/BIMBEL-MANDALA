@extends('siswa.dashboard_siswa_layout')

@section('siswa_content')
<div x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4">
    <div class="mb-8">
        <h2 class="text-3xl font-black text-slate-800">Pembayaran</h2>
        <p class="text-sm text-slate-500">Kelola semua transaksi pembayaran Anda</p>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest">Total Pembayaran</p>
            <p class="text-3xl font-black text-green-600">Rp {{ number_format($billing['total_paid'] ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest">Tunggakan</p>
            <p class="text-3xl font-black text-red-600">Rp {{ number_format($billing['total_pending'] ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="bg-blue-600 p-6 rounded-[2rem] text-white shadow-lg">
            <p class="text-[10px] font-black text-blue-200 uppercase mb-2 tracking-widest">Total Komitmen</p>
            <p class="text-3xl font-black">Rp {{ number_format($billing['total_commitment'] ?? 0, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Billing Table -->
    <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
        <h3 class="font-black text-slate-800 mb-6">Riwayat Pembayaran</h3>
        
        @if($payments && count($payments) > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b-2 border-slate-100">
                            <th class="text-left py-4 px-4 font-black text-slate-600 text-sm">Program</th>
                            <th class="text-left py-4 px-4 font-black text-slate-600 text-sm">Total</th>
                            <th class="text-left py-4 px-4 font-black text-slate-600 text-sm">Metode</th>
                            <th class="text-left py-4 px-4 font-black text-slate-600 text-sm">Status</th>
                            <th class="text-left py-4 px-4 font-black text-slate-600 text-sm">Tanggal</th>
                            <th class="text-left py-4 px-4 font-black text-slate-600 text-sm">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                            <tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                                <td class="py-4 px-4 font-semibold text-slate-800">{{ $payment->program_name }}</td>
                                <td class="py-4 px-4 font-black text-green-600">Rp {{ number_format($payment->total_harga ?? 0, 0, ',', '.') }}</td>
                                <td class="py-4 px-4 text-slate-700">{{ $payment->metode === 'offline' ? 'Offline' : 'Transfer' }}</td>
                                <td class="py-4 px-4">
                                    @if($payment->status_pembayaran === 'verified')
                                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-lg text-xs font-bold">Terverifikasi</span>
                                    @elseif($payment->status_pembayaran === 'pending')
                                        <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-lg text-xs font-bold">Menunggu</span>
                                    @else
                                        <span class="bg-red-100 text-red-800 px-3 py-1 rounded-lg text-xs font-bold">Ditolak</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-slate-600">{{ $payment->created_at->format('d M Y') }}</td>
                                <td class="py-4 px-4">
                                    @if($payment->status_pembayaran === 'pending' && $payment->bukti_pembayaran)
                                        <a href="{{ asset('storage/' . $payment->bukti_pembayaran) }}" target="_blank" class="text-blue-600 hover:text-blue-800 font-bold text-sm">
                                            <i class="fas fa-image mr-1"></i>Lihat
                                        </a>
                                    @else
                                        <button class="text-slate-400 cursor-default text-sm">-</button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-inbox text-slate-300 text-4xl mb-4"></i>
                <p class="text-slate-500 font-semibold">Belum ada riwayat pembayaran</p>
            </div>
        @endif
    </div>
</div>
@endsection

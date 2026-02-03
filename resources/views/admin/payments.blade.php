@extends('admin.dashboard_admin')

@section('admin_content')
<div class="p-8">
    <div class="mb-8">
        <h2 class="text-3xl font-black text-slate-800">Verifikasi Pembayaran</h2>
        <p class="text-sm text-slate-500">Kelola dan verifikasi bukti pembayaran siswa</p>
    </div>

    <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
        @if($payments && count($payments) > 0)
            <div class="space-y-4">
                @foreach($payments as $payment)
                    <div class="border border-slate-100 rounded-2xl p-6 hover:shadow-lg transition flex justify-between items-center">
                        <div class="flex-1">
                            <p class="font-black text-slate-800">{{ $payment->user_name ?? 'N/A' }}</p>
                            <p class="text-sm text-slate-500 mt-1">{{ $payment->program_name ?? 'Program' }}</p>
                            <p class="font-black text-green-600 mt-2">Rp {{ number_format($payment->total_harga ?? 0, 0, ',', '.') }}</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            @if($payment->bukti_pembayaran)
                                <a href="{{ asset('storage/' . $payment->bukti_pembayaran) }}" target="_blank" class="text-blue-600 hover:text-blue-800 font-bold">
                                    <i class="fas fa-image text-2xl"></i>
                                </a>
                            @endif
                            <div class="flex space-x-2">
                                <button class="bg-green-100 text-green-800 px-4 py-2 rounded-lg font-bold text-sm hover:bg-green-200">✓ Verifikasi</button>
                                <button class="bg-red-100 text-red-800 px-4 py-2 rounded-lg font-bold text-sm hover:bg-red-200">✕ Tolak</button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-inbox text-slate-300 text-4xl mb-4"></i>
                <p class="text-slate-500 font-semibold">Tidak ada pembayaran untuk diverifikasi</p>
            </div>
        @endif
    </div>
</div>
@endsection
@extends('admin.dashboard_admin')

@section('admin_content')
<div class="p-8" x-data="{ openModal: false, imgSrc: '', paymentId: '', paymentName: '' }">
    <div class="mb-8 flex justify-between items-end">
        <div>
            <h2 class="text-3xl font-black text-slate-800">Verifikasi Pembayaran</h2>
            <p class="text-sm text-slate-500 mt-1 font-medium">Pastikan bukti transfer sesuai dengan nominal sebelum konfirmasi.</p>
        </div>
        <div class="bg-blue-50 px-4 py-2 rounded-2xl border border-blue-100">
            <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest text-center">Menunggu Review</p>
            <p class="text-xl font-black text-blue-700 text-center">{{ count($payments) }} Data</p>
        </div>
    </div>

    <div class="bg-white p-2 rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
        @if($payments && count($payments) > 0)
            <div class="divide-y divide-slate-50">
                @foreach($payments as $payment)
                    <div class="p-6 hover:bg-slate-50/80 transition-all flex flex-wrap md:flex-nowrap justify-between items-center group">
                        <div class="flex items-center space-x-4 flex-1">
                            <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
                                <i class="fas fa-file-invoice-dollar text-xl"></i>
                            </div>
                            <div>
                                <p class="font-black text-slate-800 text-lg tracking-tight">{{ $payment->user_name ?? 'N/A' }}</p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-[10px] font-black text-blue-500 uppercase">{{ $payment->program_name ?? 'Program' }}</span>
                                    <span class="text-slate-300">•</span>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">ID #{{ $payment->id }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="px-8 hidden md:block">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 text-center">Total Bayar</p>
                            <p class="font-black text-green-600 text-xl tracking-tighter">Rp {{ number_format($payment->total_harga ?? 0, 0, ',', '.') }}</p>
                        </div>

                        <div class="flex items-center gap-3 ml-auto mt-4 md:mt-0">
                            @if($payment->bukti_pembayaran)
                                <button type="button" 
                                    onclick="showImageModal('{{ asset('uploads/bukti/' . $payment->bukti_pembayaran) }}')"
                                    class="h-11 px-4 rounded-xl border-2 border-slate-100 bg-white text-slate-600 hover:border-blue-500 hover:text-blue-500 transition-all flex items-center gap-2 font-bold text-xs uppercase tracking-widest">
                                    <i class="fas fa-eye text-sm"></i> Bukti
                                </button>
                            @endif

                            <form action="{{ route('admin.payments.verify', $payment->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="h-11 px-6 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-black text-xs uppercase tracking-widest transition-all shadow-lg shadow-emerald-100 flex items-center">
                                    ✓ Konfirmasi
                                </button>
                            </form>

                            <form action="{{ route('admin.payments.reject', $payment->id) }}" method="POST" onsubmit="return confirm('Tolak pendaftaran ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="h-11 w-11 flex items-center justify-center rounded-xl border-2 border-red-50 hover:bg-red-50 text-red-400 hover:text-red-600 transition-all">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-24">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check-double text-slate-200 text-3xl"></i>
                </div>
                <p class="text-slate-500 font-black uppercase tracking-widest text-sm">Semua Beres!</p>
                <p class="text-slate-400 text-xs mt-1 font-medium">Tidak ada pembayaran yang perlu diverifikasi saat ini.</p>
            </div>
        @endif
    </div>

    <template x-if="openModal">
        <div class="fixed inset-0 z-[99] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-transition>
            <div class="bg-white rounded-[2.5rem] w-full max-w-xl overflow-hidden shadow-2xl" @click.away="openModal = false">
                <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                    <div>
                        <h3 class="font-black text-slate-800 uppercase tracking-tighter">Bukti Pembayaran</h3>
                        <p class="text-[10px] text-slate-400 font-bold uppercase" x-text="paymentName"></p>
                    </div>
                    <button @click="openModal = false" class="w-10 h-10 rounded-full hover:bg-slate-100 text-slate-400 transition-all">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-4 bg-slate-50">
                    <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-white overflow-hidden flex items-center justify-center">
                        <img :src="imgSrc" class="max-w-full h-auto object-contain max-h-[60vh]">
                    </div>
                </div>
                <div class="p-6 flex gap-3">
                    <button @click="openModal = false" class="flex-1 py-4 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-2xl font-black text-xs uppercase tracking-widest transition-all">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>

<div id="imageModal" class="fixed inset-0 z-[999] hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 transition-all" onclick="closeModal()">
    <div class="relative max-w-3xl w-full bg-white rounded-2xl overflow-hidden shadow-2xl" onclick="event.stopPropagation()">
        <div class="p-4 border-b flex justify-between items-center bg-slate-50">
            <h3 class="font-bold text-slate-800 uppercase text-xs tracking-widest">Bukti Pembayaran</h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-red-500 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-2 flex justify-center bg-slate-200">
            <img id="modalImage" src="" alt="Bukti Transfer" class="max-h-[70vh] object-contain shadow-lg rounded-lg">
        </div>
        <div class="p-4 bg-white text-center">
            <a id="downloadLink" href="" download class="inline-flex items-center gap-2 text-blue-600 font-bold text-xs uppercase hover:underline">
                <i class="fas fa-download"></i> Download Gambar
            </a>
        </div>
    </div>
</div>

<script>
    function showImageModal(src) {
        document.getElementById('modalImage').src = src;
        document.getElementById('downloadLink').href = src;
        document.getElementById('imageModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden'; 
    }

    function closeModal() {
        document.getElementById('imageModal').classList.add('hidden');
        document.body.style.overflow = 'auto'; 
    }
</script>
@endsection
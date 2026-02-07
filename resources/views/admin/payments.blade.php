@extends('admin.dashboard_admin')

@section('admin_content')
<div class="p-8" x-data="{ openModal: false, imgSrc: '', paymentId: '', paymentName: '' }">
    <div class="mb-8 flex justify-between items-end">
        <div>
            <h2 class="text-3xl font-black text-slate-800 tracking-tighter">Verifikasi Pembayaran</h2>
            <p class="text-sm text-slate-500 mt-1 font-medium">Kelola pendaftaran masuk. Anda bisa memverifikasi atau menolak pendaftar di sini.</p>
        </div>
        <div class="bg-blue-50 px-4 py-2 rounded-2xl border border-blue-100">
            <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest text-center">Total Antrean</p>
            <p class="text-xl font-black text-blue-700 text-center">{{ count($payments) }} Data</p>
        </div>
    </div>

    {{-- Alert Success/Error --}}
    @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-100 border border-emerald-200 text-emerald-700 rounded-2xl font-bold text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white p-2 rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
        @if($payments && count($payments) > 0)
            <div class="divide-y divide-slate-50">
                @foreach($payments as $payment)
                    <div class="p-6 hover:bg-slate-50/80 transition-all flex flex-wrap md:flex-nowrap justify-between items-center group">
                        <div class="flex items-center space-x-4 flex-1">
                            <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
                                <i class="fas fa-user-edit text-xl"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <p class="font-black text-slate-800 text-lg tracking-tight">{{ $payment->user_name ?? 'N/A' }}</p>
                                    
                                    @if($payment->bukti_pembayaran)
                                        <span class="px-2 py-0.5 bg-blue-100 text-blue-600 text-[9px] font-black rounded-md uppercase">Sudah Upload</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-amber-100 text-amber-600 text-[9px] font-black rounded-md uppercase">Belum Upload</span>
                                    @endif
                                </div>
                                
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-[10px] font-black text-blue-500 uppercase">{{ $payment->program_name ?? 'Program' }}</span>
                                    <span class="text-slate-300">•</span>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">ID #{{ $payment->id }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="px-8 hidden md:block">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 text-center">Nominal Tagihan</p>
                            <p class="font-black text-green-600 text-xl tracking-tighter">Rp {{ number_format($payment->total_harga ?? 0, 0, ',', '.') }}</p>
                        </div>

                        <div class="flex items-center gap-3 ml-auto mt-4 md:mt-0">
                            @if($payment->bukti_pembayaran)
                                <button type="button" 
                                    onclick="showImageModal('{{ asset('uploads/bukti/' . $payment->bukti_pembayaran) }}')"
                                    class="h-11 px-4 rounded-xl border-2 border-blue-100 bg-white text-blue-600 hover:bg-blue-50 transition-all flex items-center gap-2 font-bold text-xs uppercase tracking-widest">
                                    <i class="fas fa-image text-sm"></i> Lihat Bukti
                                </button>
                            @else
                                <button disabled class="h-11 px-4 rounded-xl border-2 border-slate-100 bg-slate-50 text-slate-300 flex items-center gap-2 font-bold text-xs uppercase tracking-widest cursor-not-allowed">
                                    <i class="fas fa-times-circle text-sm"></i> No Bukti
                                </button>
                            @endif

                            {{-- Form Verifikasi --}}
                            <form action="{{ route('admin.payments.verify', $payment->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="h-11 px-6 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-black text-xs uppercase tracking-widest transition-all shadow-lg shadow-emerald-100 flex items-center">
                                    ✓ Verifikasi
                                </button>
                            </form>

                            {{-- Form Reject --}}
                            <form action="{{ route('admin.payments.reject', $payment->id) }}" method="POST" onsubmit="return confirm('Tolak dan hapus pendaftaran ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="h-11 w-11 flex items-center justify-center rounded-xl border-2 border-red-50 hover:bg-red-50 text-red-400 hover:text-red-600 transition-all" title="Tolak Pendaftaran">
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
                <p class="text-slate-500 font-black uppercase tracking-widest text-sm">Belum Ada Data</p>
                <p class="text-slate-400 text-xs mt-1 font-medium">Semua pendaftar telah diproses atau belum ada yang mendaftar.</p>
            </div>
        @endif
    </div>
</div>

{{-- Modal Image --}}
<div id="imageModal" class="fixed inset-0 z-[999] hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 transition-all" onclick="closeModal()">
    <div class="relative max-w-3xl w-full bg-white rounded-2xl overflow-hidden shadow-2xl" onclick="event.stopPropagation()">
        <div class="p-4 border-b flex justify-between items-center bg-slate-50">
            <h3 class="font-bold text-slate-800 uppercase text-[10px] tracking-widest">Detail Bukti Transfer</h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-red-500 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-4 flex justify-center bg-slate-100">
            <img id="modalImage" src="" alt="Bukti Transfer" class="max-h-[70vh] object-contain rounded-lg shadow-md">
        </div>
    </div>
</div>

<script>
    function showImageModal(src) {
        document.getElementById('modalImage').src = src;
        document.getElementById('imageModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden'; 
    }
    function closeModal() {
        document.getElementById('imageModal').classList.add('hidden');
        document.body.style.overflow = 'auto'; 
    }
</script>
@endsection
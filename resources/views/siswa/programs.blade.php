@extends('siswa.dashboard_siswa_layout')
@section('siswa_content')
<div x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4">
    <div class="mb-8">
        <h2 class="text-3xl font-black text-slate-800">Kelas Saya</h2>
        <p class="text-sm text-slate-500">Kelola semua program pembelajaran Anda</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @if($programs && count($programs) > 0)
            @foreach($programs as $program)
                <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-lg transition">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <p class="font-black text-slate-800 text-lg">{{ $program->name }}</p>
                            <p class="text-xs text-slate-500 mt-1">{{ $program->jenjang ?? 'Program' }}</p>
                        </div>
                        <div class="bg-blue-100 text-blue-600 px-2 py-1 rounded-lg text-xs font-black">
                            {{ $program->status_pembayaran === 'verified' ? 'Aktif' : 'Pending' }}
                        </div>
                    </div>

                    <div class="py-4 border-y border-slate-100 my-4">
                        <p class="text-sm text-slate-600 font-bold">
                            <i class="fas fa-chalkboard-teacher mr-2 text-orange-500"></i>
                            {{ $program->mentor_name ?? 'Belum ada mentor' }}
                        </p>
                    </div>

                    <div class="mb-4">
                        <p class="text-sm font-bold text-slate-600 mb-2">Progress</p>
                        <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden">
                            <div class="bg-blue-500 h-full w-[45%]"></div>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">45% Selesai</p>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-center py-3 border-t border-slate-100">
                        <div>
                            <p class="text-2xl font-black text-blue-600">12</p>
                            <p class="text-[10px] text-slate-500 font-bold">Sesi</p>
                        </div>
                        <div>
                            <p class="text-2xl font-black text-green-600">Rp {{ number_format($program->total_harga ?? 0, 0, ',', '.') }}</p>
                            <p class="text-[10px] text-slate-500 font-bold">Total</p>
                        </div>
                    </div>

                    <button class="w-full mt-4 bg-blue-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-blue-700 transition">
                        Lihat Detail
                    </button>
                </div>
            @endforeach
        @else
            <div class="col-span-full text-center py-12">
                <i class="fas fa-inbox text-slate-300 text-4xl mb-4"></i>
                <p class="text-slate-500 font-semibold mb-4">Belum ada program</p>
                <a href="{{ route('home') }}" class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700">
                    Cari Program
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        
        // Cek apakah ada parameter pendaftaran=berhasil di URL
        if (urlParams.get('pendaftaran') === 'berhasil') {
            
            // 1. Matikan paksa loading yang mungkin masih nyangkut
            Swal.close();

            // 2. Munculkan pop-up sukses
            Swal.fire({
                title: 'PENDAFTARAN TERKIRIM!',
                html: `
                    <div class="text-center">
                        <p style="margin-bottom: 20px; color: #666;">Data kamu sudah kami terima. Silakan konfirmasi ke WhatsApp Admin untuk aktivasi akun.</p>
                        <a href="https://wa.me/628123456789?text=Halo%20Admin,%20saya%20sudah%20daftar%20program%20reguler" 
                           target="_blank" 
                           style="background-color: #25D366; color: white; padding: 12px 25px; border-radius: 10px; text-decoration: none; font-weight: bold; display: inline-block;">
                           KONFIRMASI VIA WHATSAPP
                        </a>
                    </div>
                `,
                icon: 'success',
                confirmButtonText: 'OKE, SAYA PAHAM',
                confirmButtonColor: '#2563eb',
                allowOutsideClick: false,
                customClass: {
                    popup: 'rounded-[2rem]'
                }
            }).then(() => {
                // 3. Hapus parameter di URL supaya kalau di-refresh pop-up gak muncul lagi
                window.history.replaceState({}, document.title, window.location.pathname);
            });
        }
    });
</script>
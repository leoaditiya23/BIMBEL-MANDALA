@extends('layouts.app')

@section('title', 'Beranda')

@section('content')

<style>
    /* Scale halaman home lebih kecil - 82% dari ukuran asli */
    html {
        zoom: 0.82 !important;
    }
</style>

{{-- 1. HERO SECTION --}}
<section class="min-h-[85vh] px-10 flex flex-col md:flex-row items-stretch justify-between bg-slate-50 overflow-hidden">
    <div class="md:w-1/2 space-y-8 py-12 flex flex-col justify-center">
        <div class="inline-block px-4 py-2 bg-blue-100 text-blue-700 rounded-full text-sm font-bold tracking-wide w-fit">
            🚀 #1 BIMBEL DIGITAL TERPERCAYA
        </div>
        <h1 class="text-5xl lg:text-7xl font-black leading-tight text-slate-900">
            Siap Jadi Juara <br>
            <span class="text-blue-600">Kelas Tahun Ini!</span> <br>
            <span class="text-orange-500 text-xl lg:text-4xl font-bold tracking-wide block mt-2">
                Akhlak, Prestasi, Kreatif.
            </span>
        </h1>
        <p class="text-xl text-slate-600 leading-relaxed max-w-lg font-medium">
            Les Privat untuk Anak TK - SMA dan Dapatkan bimbingan belajar eksklusif dengan mentor profesional yang siap membantumu meraih nilai impian.
        </p>
        <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-5 pt-4">
            <a href="{{ route('program.reguler') }}" class="px-10 py-4 bg-orange-500 text-white rounded-2xl font-black text-center shadow-lg hover:bg-orange-600 hover:scale-105 transition-all duration-300">
                LIHAT PROGRAM
            </a>
            <a href="{{ route('faq.index') }}" class="px-10 py-4 bg-white border-2 border-blue-600 text-blue-600 rounded-2xl font-black text-center hover:bg-blue-50 transition-all duration-300">
                TANYA JAWAB
            </a>
        </div>
    </div>

   <div class="md:w-1/2 relative flex items-end justify-center min-h-[450px] lg:min-h-[600px]">
        {{-- REVISI: h-[85%] untuk menurunkan posisi kepala, h-[85%] di desktop agar tetap proporsional --}}
        <img src="{{ asset('images/talent.png') }}" 
             alt="Talent Mandala Bimbel" 
             class="absolute bottom-0 h-[85%] md:h-[90%] w-auto max-w-none z-10 drop-shadow-2xl object-contain object-bottom scale-110 origin-bottom">

        <div class="absolute bottom-20 -left-10 bg-white p-5 rounded-3xl shadow-2xl border-l-8 border-orange-500 hidden lg:block z-20 transform -rotate-2">
            <div class="flex items-center space-x-4">
                <div class="bg-orange-100 p-3 rounded-xl text-orange-600 font-black text-xl">10k+</div>
                <div>
                    <p class="font-bold text-slate-800 leading-none text-sm">Siswa Aktif</p>
                    <p class="text-[10px] text-slate-500 mt-1 uppercase font-bold">Seluruh Indonesia</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- 2. PROGRAM KAMI --}}
<section class="py-20 bg-white px-10">
    <div class="container mx-auto text-center mb-16">
        <h2 class="text-4xl font-black text-blue-600">Program <span class="text-orange-500">Kami.</span></h2>
    </div>

    <div class="container mx-auto grid md:grid-cols-3 gap-6 max-w-6xl">
        <div class="border-2 border-blue-600 rounded-[35px] p-8 flex flex-col bg-white hover:scale-105 hover:shadow-2xl transition-all duration-300 cursor-pointer group active:scale-95">
            <h3 class="text-2xl font-black text-blue-600 mb-6 group-hover:text-blue-700 transition-colors">Reguler Online</h3>
            <ul class="space-y-4 text-slate-700 font-bold text-md">
                <li class="flex items-start"><span class="mr-3">1.</span> Akses Video Pembelajaran</li>
                <li class="flex items-start"><span class="mr-3">2.</span> Pustaka Materi (PDF)</li>
                <li class="flex items-start"><span class="mr-3">3.</span> Live Class (Zoom Meeting)</li>
                <li class="flex items-start"><span class="mr-3">4.</span> Belajar Fleksibel</li>
            </ul>
        </div>

        <div class="border-2 border-orange-500 rounded-[35px] p-8 flex flex-col bg-white hover:scale-105 hover:shadow-2xl transition-all duration-300 cursor-pointer group active:scale-95">
            <h3 class="text-2xl font-black text-orange-500 mb-6 group-hover:text-orange-600 transition-colors">Reguler Offline</h3>
            <ul class="space-y-4 text-slate-700 font-bold text-md">
                <li class="flex items-start"><span class="mr-3">1.</span> Tentor Ke Rumah</li>
                <li class="flex items-start"><span class="mr-3">2.</span> Bimbingan Mengaji (Opsional)</li>
                <li class="flex items-start"><span class="mr-3">3.</span> Laporan Progres Personal</li>
                <li class="flex items-start"><span class="mr-3">4.</span> Gratis Trial Class 1X</li>
            </ul>
        </div>

        <div class="border-2 border-blue-600 rounded-[35px] p-8 flex flex-col bg-white hover:scale-105 hover:shadow-2xl transition-all duration-300 cursor-pointer group active:scale-95">
            <h3 class="text-2xl font-black text-blue-600 mb-6 group-hover:text-blue-700 transition-colors">Kelas Intensif</h3>
            <ul class="space-y-4 text-slate-700 font-bold text-md">
                <li class="flex items-start"><span class="mr-3">1.</span> Online / Offline</li>
                <li class="flex items-start"><span class="mr-3">2.</span> Fokus TKA/UTBK</li>
                <li class="flex items-start"><span class="mr-3">3.</span> Durasi 4 Bulan</li>
                <li class="flex items-start"><span class="mr-3">4.</span> Campuran Materi Lengkap</li>
            </ul>
        </div>
    </div>
</section>

{{-- 3. FAQ --}}
<section class="bg-slate-50 py-16 px-10">
    <div class="max-w-4xl mx-auto">
        <h2 class="text-4xl font-black text-center mb-12 text-blue-600">FAQ</h2>
        <div class="space-y-4">
            <details class="group bg-white p-6 rounded-2xl border-l-8 border-orange-500 shadow-sm" open>
                <summary class="font-bold text-lg cursor-pointer list-none flex justify-between items-center">
                    Apakah bisa bayar cicilan?
                    <i class="fas fa-chevron-down group-open:rotate-180 transition"></i>
                </summary>
                <p class="mt-4 text-slate-600">Tentu! Khusus untuk Program Intensif, kami menyediakan layanan cicilan 2x.</p>
            </details>
            
            <details class="group bg-white p-6 rounded-2xl border-l-8 border-blue-500 shadow-sm">
                <summary class="font-bold text-lg cursor-pointer list-none flex justify-between items-center">
                    Bagaimana cara memilih mentor?
                    <i class="fas fa-chevron-down group-open:rotate-180 transition"></i>
                </summary>
                <p class="mt-4 text-slate-600">Setelah mendaftar Program Reguler, Anda akan diberikan daftar mentor yang tersedia sesuai jadwal Anda.</p>
            </details>
        </div>
    </div>
</section>

{{-- 4. TENTANG KAMI --}}
<section class="py-20 px-10 bg-white">
    <div class="container mx-auto max-w-4xl bg-slate-50 p-12 rounded-[40px] shadow-xl border border-slate-100">
        <h2 class="text-4xl font-black text-blue-600 mb-6">Tentang Mandala<span class="text-orange-500">Bimbel</span></h2>
        <div class="space-y-6 text-slate-600 leading-relaxed text-lg">
            <p>MandalaBimbel adalah lembaga bimbingan belajar modern yang fokus pada pengembangan potensi akademik siswa melalui metode pembelajaran yang interaktif dan adaptif.</p>
            <div class="grid md:grid-cols-2 gap-8 py-4">
                <div class="bg-blue-50 p-6 rounded-2xl">
                    <h3 class="font-bold text-blue-600 mb-2 italic">Misi Kami</h3>
                    <p class="text-sm italic text-slate-700 leading-relaxed font-medium">Memberikan akses pendidikan berkualitas tinggi dengan harga terjangkau bagi seluruh pelajar Indonesia.</p>
                </div>
                <div class="bg-orange-50 p-6 rounded-2xl">
                    <h3 class="font-bold text-orange-500 mb-2 italic">Visi Kami</h3>
                    <p class="text-sm italic text-slate-700 leading-relaxed font-medium">Menjadi platform edukasi digital nomor satu yang mencetak generasi unggul dan berkarakter.</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- 5. MENTOR KAMI --}}
<section class="py-20 bg-slate-50 px-10">
    <div class="container mx-auto text-center mb-16">
        <h2 class="text-4xl font-black text-blue-600 mb-4">Mentor <span class="text-orange-500">Kami.</span></h2>
        <p class="text-slate-600 leading-relaxed text-lg font-medium">Belajar langsung dari ahli yang berpengalaman di bidangnya.</p>
    </div>

    <div class="container mx-auto flex overflow-x-auto gap-8 pb-10 max-w-7xl snap-x snap-mandatory scrollbar-hide">
        
        <div class="min-w-[220px] group relative snap-center">
            <div class="relative bg-white rounded-[1.5rem] p-3 shadow-lg transition-all duration-300 group-hover:-translate-y-3 border-b-4 border-blue-600">
                <div class="aspect-square overflow-hidden rounded-xl bg-blue-50 mb-4">
                    <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Ahmad&mood=happy" alt="Mentor" class="w-full h-full object-cover">
                </div>
                <div class="text-center pb-2">
                    <h3 class="text-lg font-black text-slate-800 leading-tight">Kak Ahmad S.</h3>
                    <p class="text-blue-600 font-bold text-[10px] uppercase tracking-wider mt-1">Pakar Matematika</p>
                </div>
            </div>
        </div>

        <div class="min-w-[220px] group relative snap-center">
            <div class="relative bg-white rounded-[1.5rem] p-3 shadow-lg transition-all duration-300 group-hover:-translate-y-3 border-b-4 border-orange-500">
                <div class="aspect-square overflow-hidden rounded-xl bg-orange-50 mb-4">
                    <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Sarah&accessories=eyepatch" alt="Mentor" class="w-full h-full object-cover">
                </div>
                <div class="text-center pb-2">
                    <h3 class="text-lg font-black text-slate-800 leading-tight">Kak Sarah M.</h3>
                    <p class="text-orange-500 font-bold text-[10px] uppercase tracking-wider mt-1">Tutor Bahasa Inggris</p>
                </div>
            </div>
        </div>

        <div class="min-w-[220px] group relative snap-center">
            <div class="relative bg-white rounded-[1.5rem] p-3 shadow-lg transition-all duration-300 group-hover:-translate-y-3 border-b-4 border-blue-600">
                <div class="aspect-square overflow-hidden rounded-xl bg-blue-50 mb-4">
                    <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Budi&mood=happy" alt="Mentor" class="w-full h-full object-cover">
                </div>
                <div class="text-center pb-2">
                    <h3 class="text-lg font-black text-slate-800 leading-tight">Kak Budi H.</h3>
                    <p class="text-blue-600 font-bold text-[10px] uppercase tracking-wider mt-1">Mentor Fisika</p>
                </div>
            </div>
        </div>

        <div class="min-w-[220px] group relative snap-center">
            <div class="relative bg-white rounded-[1.5rem] p-3 shadow-lg transition-all duration-300 group-hover:-translate-y-3 border-b-4 border-orange-500">
                <div class="aspect-square overflow-hidden rounded-xl bg-orange-50 mb-4">
                    <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Linda&clothing=graphicShirt" alt="Mentor" class="w-full h-full object-cover">
                </div>
                <div class="text-center pb-2">
                    <h3 class="text-lg font-black text-slate-800 leading-tight">Kak Linda P.</h3>
                    <p class="text-orange-500 font-bold text-[10px] uppercase tracking-wider mt-1">Tutor Biologi</p>
                </div>
            </div>
        </div>

        <div class="min-w-[220px] group relative snap-center">
            <div class="relative bg-white rounded-[1.5rem] p-3 shadow-lg transition-all duration-300 group-hover:-translate-y-3 border-b-4 border-blue-600">
                <div class="aspect-square overflow-hidden rounded-xl bg-blue-50 mb-4">
                    <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Dewi&mouth=smile" alt="Mentor" class="w-full h-full object-cover">
                </div>
                <div class="text-center pb-2">
                    <h3 class="text-lg font-black text-slate-800 leading-tight">Kak Dewi A.</h3>
                    <p class="text-blue-600 font-bold text-[10px] uppercase tracking-wider mt-1">Guru Kimia</p>
                </div>
            </div>
        </div>

        <div class="min-w-[220px] group relative snap-center">
            <div class="relative bg-white rounded-[1.5rem] p-3 shadow-lg transition-all duration-300 group-hover:-translate-y-3 border-b-4 border-orange-500">
                <div class="aspect-square overflow-hidden rounded-xl bg-orange-50 mb-4">
                    <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Rizky&mood=happy" alt="Mentor" class="w-full h-full object-cover">
                </div>
                <div class="text-center pb-2">
                    <h3 class="text-lg font-black text-slate-800 leading-tight">Kak Rizky F.</h3>
                    <p class="text-orange-500 font-bold text-[10px] uppercase tracking-wider mt-1">Pakar Sejarah</p>
                </div>
            </div>
        </div>

        <div class="min-w-[220px] group relative snap-center">
            <div class="relative bg-white rounded-[1.5rem] p-3 shadow-lg transition-all duration-300 group-hover:-translate-y-3 border-b-4 border-blue-600">
                <div class="aspect-square overflow-hidden rounded-xl bg-blue-50 mb-4">
                    <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Nina&glassesType=round" alt="Mentor" class="w-full h-full object-cover">
                </div>
                <div class="text-center pb-2">
                    <h3 class="text-lg font-black text-slate-800 leading-tight">Kak Nina W.</h3>
                    <p class="text-blue-600 font-bold text-[10px] uppercase tracking-wider mt-1">Tutor Geografi</p>
                </div>
            </div>
        </div>

    </div>
</section>

{{-- 6. HUBUNGI KAMI --}}
<section class="pt-16 pb-8 bg-white px-10"> {{-- Padding bawah dikurangi drastis --}}
    <div class="container mx-auto max-w-4xl text-center">
        <h2 class="text-4xl font-black text-blue-600 mb-4">Hubungi <span class="text-orange-500">Kami.</span></h2>
        <p class="text-slate-600 leading-relaxed text-lg font-medium max-w-2xl mx-auto mb-10">
            Customer servis kami siap membantu Anda untuk Mendapatkan Informasi Lebih Lanjut Mengenai Program Mandala Bimbel
        </p>

        <div class="flex justify-center mb-16 px-4">
            <a href="https://wa.me/6285540000900" target="_blank" 
               class="flex items-center space-x-3 px-8 py-4 bg-[#A7FFA7] text-[#1E5F1E] rounded-2xl font-black text-lg md:text-xl shadow-xl hover:scale-105 transition-all duration-300 group border-b-4 border-[#7ae37a]">
                
                <div class="bg-white p-1.5 rounded-full shadow-sm group-hover:rotate-12 transition-transform">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" class="w-6 h-6" alt="WA">
                </div>
                
                <span>Hubungi Kami Via WhatsApp</span>
            </a>
        </div>

        <div class="grid md:grid-cols-2 gap-8 pt-10 border-t border-slate-100">
            <div class="text-left bg-slate-50 p-8 rounded-3xl">
                <div class="bg-blue-600 w-12 h-12 rounded-xl flex items-center justify-center text-white mb-4">
                    <i class="fas fa-map-marker-alt text-xl"></i>
                </div>
                <h4 class="font-black text-slate-800 text-lg mb-2">Alamat Kantor</h4>
                <p class="text-slate-500 font-medium leading-relaxed text-sm">Tamansari Hills Residence Blok B01 No.10, Mangunharjo, Kec. Banyumanik, Kota Semarang, Jawa Tengah 50272</p>
            </div>
            <div class="text-left bg-slate-50 p-8 rounded-3xl">
                <div class="bg-orange-500 w-12 h-12 rounded-xl flex items-center justify-center text-white mb-4">
                    <i class="fas fa-envelope text-xl"></i>
                </div>
                <h4 class="font-black text-slate-800 text-lg mb-2">Email Support</h4>
                <p class="text-slate-500 font-medium text-lg">halo@mandalabimbel.com</p>
                <p class="text-slate-500 font-medium text-sm">+62 855-4000-0900</p>
            </div>
        </div>
    </div>
</section>
@endsection
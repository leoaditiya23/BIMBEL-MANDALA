<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Mentor; 
use App\Models\Program;
use Carbon\Carbon;

class PageController extends Controller
{
    /**
     * ==========================================
     * 1. HALAMAN PUBLIK
     * ==========================================
     */
    public function index() {
        $programs = Program::all(); 
        $faqs = DB::table('faqs')->orderBy('created_at', 'asc')->get();
        $mentors = Mentor::all(); // Diperbaiki: Langsung panggil Mentor::all()

        return view('pages.home', compact('programs', 'faqs', 'mentors'));
    }

    public function faq() { 
        $faqs = DB::table('faqs')->orderBy('created_at', 'asc')->get();
        return view('pages.faq', compact('faqs')); 
    }

    public function about() { return view('pages.about'); }
    public function contact() { return view('pages.contact'); }

    // Cari function reguler di PageController.php
// Cari fungsi reguler di PageController.php
public function reguler(Request $request) {
    // 1. Ambil semua data program untuk kalkulasi harga di pendaftaran
    $programs = DB::table('programs')->get(); // Diubah dari filter 'reguler' saja agar data jenjang lengkap
    
    // 2. Ambil data mata pelajaran
    $subjects = DB::table('subjects')->get();

    // 3. Kelompokkan mapel berdasarkan jenjang
    $mapelByJenjang = $subjects->groupBy('jenjang')->map(function ($items) {
        return $items->pluck('name');
    });

    $requestedStep = (int) $request->query('step', 1);
    if (!in_array($requestedStep, [1, 2, 3], true)) {
        $requestedStep = 1;
    }

    // Draft hanya boleh dipulihkan untuk alur resume pembayaran/login
    // atau ketika backend mengembalikan error setelah submit.
    $restoreFromSession = $request->boolean('resume') || $request->session()->has('preserve_reguler_state');
    $step = $restoreFromSession ? $requestedStep : 1;
    
    return view('pages.program.reguler', compact('programs', 'subjects', 'mapelByJenjang', 'step', 'restoreFromSession'));
}

    public function intensif(Request $request) {
        $programs = DB::table('programs')->where('type', 'intensif')->get();
        $programsByName = $programs->keyBy('name')->toArray();
        $step = $request->query('step', 1);
        return view('pages.program.intensif', compact('programs', 'programsByName', 'step'));
    }

    /**
     * ==========================================
     * 2. LOGIKA PENDAFTARAN (REVISI KEAMANAN & ATOMIK)
     * ==========================================
     */

    /**
     * ==========================================
     * 3. AUTENTIKASI & REDIRECT LOGIC
     * ==========================================
     */
public function login() 
    { 
        return view('pages.login'); 
    }

    public function register(Request $request)
    {
        // 1. Ambil semua program untuk pilihan di form (opsional)
        $programs = Program::all();

        // 2. Ambil data dari URL jika ada (misal dari tombol daftar di halaman reguler/intensif)
        $selectedType = $request->query('type'); // reguler atau intensif
        $selectedProgram = $request->query('program_id');

        // 3. Arahkan ke file view yang benar sesuai struktur folder Anda (pages/register.blade.php)
        return view('pages.register', compact('programs', 'selectedType', 'selectedProgram'));
    }
    public function registerStore(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'birth_date' => 'required|date',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'phone' => 'required|numeric',
            'school' => 'required|string|max:255',
        ]);

        // 1. Simpan data user ke variabel $user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'siswa', 
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'whatsapp' => $request->phone,
            'school' => $request->school,
            'referral' => $request->referral,
        ]);

        // 2. Otomatis login pengguna yang baru mendaftar
        Auth::login($user);

        // 3. Alihkan langsung ke dashboard utama
        return redirect()->route('dashboard')->with('success', 'Pendaftaran berhasil! Selamat datang.');
    }

    public function authenticate(Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Perbaikan: Gunakan route('dashboard') sebagai fallback yang konsisten
            return redirect()->intended(route('dashboard'));
        }
        return back()->withErrors(['loginError' => 'Email atau Password salah!'])->onlyInput('email');
    }

    public function pendaftaranLanjut(Request $request) {
        $type = $request->query('type', 'intensif');
        $targetUrl = ($type === 'reguler') 
            ? route('program.reguler', ['step' => 3, 'resume' => 1]) 
            : route('program.intensif', ['step' => 3, 'resume' => 1]);
        
        session(['url.intended' => $targetUrl]);
        return redirect()->route('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    /**
     * ==========================================
     * 4. LOGIKA DASHBOARD (REDIRECTOR)
     * ==========================================
     */
   public function dashboard() {
        if (!Auth::check()) return redirect()->route('login');
        
        $role = Auth::user()->role;
        
        return match($role) {
            'admin' => redirect()->route('admin.overview'),
            'mentor' => redirect()->route('mentor.overview'),
            default => redirect()->route('siswa.overview'),
        };
    }
    /**
     * ==========================================
     * 4. FITUR ADMIN (VIEWS & CRUD)
     * ==========================================
     */

    // --- DASHBOARD & PEMBAYARAN ---
public function adminOverview(Request $request) { 
    try {
        $currentYear = (int) now()->year;

        $availableYears = DB::table('enrollments')
            ->where('status_pembayaran', 'verified')
            ->whereNotNull('created_at')
            ->selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->filter()
            ->map(fn ($year) => (int) $year)
            ->values()
            ->all();

        if (!in_array($currentYear, $availableYears, true)) {
            $availableYears[] = $currentYear;
            rsort($availableYears);
        }

        if (empty($availableYears)) {
            $availableYears = [$currentYear];
        }

        $filterTahun = (int) $request->query('tahun', $currentYear);
        if (!in_array($filterTahun, $availableYears, true)) {
            $filterTahun = (int) $availableYears[0];
        }

        $filterBulanInput = $request->query('bulan', 'semua');
        $filterBulan = $filterBulanInput === 'semua' ? 'semua' : (int) $filterBulanInput;
        if ($filterBulan !== 'semua' && ($filterBulan < 1 || $filterBulan > 12)) {
            $filterBulan = 'semua';
        }

        $queryPendapatan = DB::table('enrollments')
            ->where('status_pembayaran', 'verified')
            ->whereYear('created_at', $filterTahun);

        if ($filterBulan !== 'semua') {
            $queryPendapatan->whereMonth('created_at', $filterBulan);
        }

        $stats = [
            'total_pendapatan' => (clone $queryPendapatan)->sum('total_harga'),
            'total_siswa'     => DB::table('enrollments')->where('status_pembayaran', 'verified')->distinct('user_id')->count(), 
            'total_mentor'    => User::where('role', 'mentor')->count(),
            'total_program'   => DB::table('programs')->count(),
        ];

        if ($filterBulan === 'semua') {
            $trenRaw = (clone $queryPendapatan)
                ->selectRaw('MONTH(created_at) as label_num, SUM(total_harga) as total')
                ->groupBy('label_num')
                ->orderBy('label_num')
                ->get()
                ->keyBy('label_num');

            $trenLabels = [];
            $trenData = [];
            for ($bulan = 1; $bulan <= 12; $bulan++) {
                $trenLabels[] = Carbon::createFromDate($filterTahun, $bulan, 1)->translatedFormat('M');
                $trenData[] = (float) ($trenRaw[$bulan]->total ?? 0);
            }

            $grafikPendapatan = [
                'title' => "Tren Pendapatan Tahun {$filterTahun}",
                'labels' => $trenLabels,
                'data' => $trenData,
            ];

            $tahunSebelumnya = $filterTahun - 1;
            $perbandinganTahunIni = DB::table('enrollments')
                ->where('status_pembayaran', 'verified')
                ->whereYear('created_at', $filterTahun)
                ->selectRaw('MONTH(created_at) as label_num, SUM(total_harga) as total')
                ->groupBy('label_num')
                ->orderBy('label_num')
                ->get()
                ->keyBy('label_num');

            $perbandinganTahunLalu = DB::table('enrollments')
                ->where('status_pembayaran', 'verified')
                ->whereYear('created_at', $tahunSebelumnya)
                ->selectRaw('MONTH(created_at) as label_num, SUM(total_harga) as total')
                ->groupBy('label_num')
                ->orderBy('label_num')
                ->get()
                ->keyBy('label_num');

            $labelsPerbandingan = [];
            $dataTahunIni = [];
            $dataTahunLalu = [];

            for ($bulan = 1; $bulan <= 12; $bulan++) {
                $labelsPerbandingan[] = Carbon::createFromDate($filterTahun, $bulan, 1)->translatedFormat('M');
                $dataTahunIni[] = (float) ($perbandinganTahunIni[$bulan]->total ?? 0);
                $dataTahunLalu[] = (float) ($perbandinganTahunLalu[$bulan]->total ?? 0);
            }

            $grafikPerbandingan = [
                'title' => "Perbandingan {$filterTahun} vs {$tahunSebelumnya}",
                'labels' => $labelsPerbandingan,
                'datasets' => [
                    [
                        'label' => "Tahun {$filterTahun}",
                        'data' => $dataTahunIni,
                        'backgroundColor' => 'rgba(37, 99, 235, 0.55)',
                        'borderColor' => '#2563eb',
                    ],
                    [
                        'label' => "Tahun {$tahunSebelumnya}",
                        'data' => $dataTahunLalu,
                        'backgroundColor' => 'rgba(249, 115, 22, 0.55)',
                        'borderColor' => '#f97316',
                    ],
                ],
            ];
        } else {
            $periodeSaatIni = Carbon::createFromDate($filterTahun, $filterBulan, 1);
            $periodeSebelumnya = (clone $periodeSaatIni)->subMonthNoOverflow();
            $maxHari = max($periodeSaatIni->daysInMonth, $periodeSebelumnya->daysInMonth);

            $trenRaw = (clone $queryPendapatan)
                ->selectRaw('DAY(created_at) as label_num, SUM(total_harga) as total')
                ->groupBy('label_num')
                ->orderBy('label_num')
                ->get()
                ->keyBy('label_num');

            $perbandinganSebelumnya = DB::table('enrollments')
                ->where('status_pembayaran', 'verified')
                ->whereYear('created_at', $periodeSebelumnya->year)
                ->whereMonth('created_at', $periodeSebelumnya->month)
                ->selectRaw('DAY(created_at) as label_num, SUM(total_harga) as total')
                ->groupBy('label_num')
                ->orderBy('label_num')
                ->get()
                ->keyBy('label_num');

            $trenLabels = [];
            $trenData = [];
            $dataSaatIni = [];
            $dataSebelumnya = [];

            for ($hari = 1; $hari <= $maxHari; $hari++) {
                $trenLabels[] = (string) $hari;
                $trenData[] = (float) ($trenRaw[$hari]->total ?? 0);
                $dataSaatIni[] = (float) ($trenRaw[$hari]->total ?? 0);
                $dataSebelumnya[] = (float) ($perbandinganSebelumnya[$hari]->total ?? 0);
            }

            $grafikPendapatan = [
                'title' => 'Tren Pendapatan ' . $periodeSaatIni->translatedFormat('F Y'),
                'labels' => $trenLabels,
                'data' => $trenData,
            ];

            $grafikPerbandingan = [
                'title' => 'Perbandingan ' . $periodeSaatIni->translatedFormat('F Y') . ' vs ' . $periodeSebelumnya->translatedFormat('F Y'),
                'labels' => $trenLabels,
                'datasets' => [
                    [
                        'label' => $periodeSaatIni->translatedFormat('F Y'),
                        'data' => $dataSaatIni,
                        'backgroundColor' => 'rgba(37, 99, 235, 0.55)',
                        'borderColor' => '#2563eb',
                    ],
                    [
                        'label' => $periodeSebelumnya->translatedFormat('F Y'),
                        'data' => $dataSebelumnya,
                        'backgroundColor' => 'rgba(249, 115, 22, 0.55)',
                        'borderColor' => '#f97316',
                    ],
                ],
            ];
        }

        $recent_enrollments = DB::table('enrollments')
            ->join('users', 'enrollments.user_id', '=', 'users.id')
            ->join('programs', 'enrollments.program_id', '=', 'programs.id')
            ->select(
                'enrollments.*',
                'users.name as user_name',
                'programs.jenjang as program_jenjang',
                'programs.name as program_name'
            )
            ->orderBy('enrollments.created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // TRANSFORMASI DATA UNTUK TAMPILAN DASHBOARD & POP-UP RINCI
        $recent_enrollments->getCollection()->transform(function($item) {
            // 1. Logika Lokasi
            $alamatSemarang = trim($item->alamat_semarang ?? '');
            $cabang = trim($item->lokasi_cabang ?? '');
            
            $hasRealAlamat = !empty($alamatSemarang) && $alamatSemarang !== '-';
            $hasRealCabang = !empty($cabang) && $cabang !== '-';

            if ($hasRealAlamat || $hasRealCabang) {
                $item->display_lokasi = $hasRealAlamat ? $item->alamat_semarang : $item->lokasi_cabang;
                $item->is_online = false;
            } else {
                $item->display_lokasi = "VIA ZOOM (ONLINE)";
                $item->is_online = true;
            }
            
            // 2. Logika Nama Program/Mapel (Untuk Detail di Pop-up)
            $mapelRaw = $item->mapel;
            $mapelIds = json_decode($mapelRaw, true);
            
            if (!is_array($mapelIds)) {
                $mapelIds = array_filter(explode(',', $mapelRaw));
            }

            $mapelNames = DB::table('programs')
                ->whereIn('id', $mapelIds)
                ->pluck('name')
                ->toArray();

            $item->display_program = count($mapelNames) > 0 
                ? implode(', ', $mapelNames) 
                : ( (!empty($mapelRaw) && $mapelRaw !== '0') ? $mapelRaw : $item->program_name );
            
            // 3. Detail Tambahan untuk Pop-up
            $item->display_kelas = $item->kelas ?: '-';
            $item->format_harga = 'Rp ' . number_format($item->total_harga, 0, ',', '.');
            
            return $item;
        });

        // Mengirimkan semua variabel yang dibutuhkan ke View
        return view('admin.overview', compact(
            'stats', 
            'recent_enrollments', 
            'grafikPendapatan', 
            'grafikPerbandingan',
            'filterBulan',
            'filterTahun',
            'availableYears'
        ));
        
    } catch (\Exception $e) {
        return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}
    public function adminPayments() {
    $payments = DB::table('enrollments')
        ->join('users', 'enrollments.user_id', '=', 'users.id')
        ->join('programs', 'enrollments.program_id', '=', 'programs.id')
        ->select(
            'enrollments.*', 
            'users.name as user_name', 
            'users.whatsapp as user_wa',
            'programs.name as program_name_ref'
        )
        ->where('enrollments.status_pembayaran', 'pending')
        ->orderBy('enrollments.created_at', 'desc')
        ->get()
        ->map(function($item) {
            $item->is_midtrans_payment = ($item->payment_method ?? 'manual') === 'midtrans';

            // Kode unik hanya dipakai untuk transfer manual.
            if ($item->is_midtrans_payment) {
                $item->kode_unik_tampil = null;
            } else {
                $cleanPhone = preg_replace('/[^0-9]/', '', $item->user_wa ?? '000');
                $item->kode_unik_tampil = substr($cleanPhone, -3);
            }
            
            // Format Lokasi
            if (str_contains(strtolower($item->metode), 'online')) {
                $item->lokasi_info = "ONLINE (ZOOM)";
            } else {
                $item->lokasi_info = $item->alamat_siswa ?: $item->lokasi_cabang;
            }
            
            return $item;
        });

    return view('admin.payments', compact('payments'));
}

    public function verifyEnrollment($id) {
        $enrollment = DB::table('enrollments')->where('id', $id)->first();
        if (!$enrollment) {
            return back()->with('error', 'Data pembayaran tidak ditemukan.');
        }

        $updatePayload = [
            'status_pembayaran' => 'verified',
            'paid_at' => $enrollment->paid_at ?? now(),
            'updated_at' => now(),
        ];

        // Untuk Midtrans, izinkan admin override saat status gateway belum settlement.
        if (($enrollment->payment_method ?? 'manual') === 'midtrans') {
            $gatewayStatus = strtolower((string) ($enrollment->midtrans_transaction_status ?? ''));
            if (!in_array($gatewayStatus, ['capture', 'settlement'], true)) {
                $updatePayload['midtrans_transaction_status'] = 'manual_verified';
            }
        }

        DB::table('enrollments')->where('id', $id)->update($updatePayload);
        return back()->with('success', 'Pembayaran berhasil diverifikasi!');
    }

    public function rejectPayment($id) {
        $enrollment = DB::table('enrollments')->where('id', $id)->first();
        if ($enrollment && $enrollment->bukti_pembayaran) {
            Storage::disk('public')->delete($enrollment->bukti_pembayaran);
        }
        DB::table('enrollments')->where('id', $id)->delete();
        return back()->with('success', 'Pendaftaran ditolak dan data dihapus.');
    }

    public function updateJadwal(Request $request, $id) {
        return $this->assignMentorToEnrollment($request, $id);
    }

    public function adminMentorPlacements(Request $request) {
        $statusFilter = $request->query('status', 'butuh_aksi');

        $placementsQuery = DB::table('enrollments')
            ->join('users as students', 'enrollments.user_id', '=', 'students.id')
            ->join('programs', 'enrollments.program_id', '=', 'programs.id')
            ->leftJoin('users as mentors', 'enrollments.mentor_id', '=', 'mentors.id')
            ->where('enrollments.status_pembayaran', 'verified')
            ->select(
                'enrollments.id',
                'enrollments.user_id',
                'enrollments.mentor_id',
                'enrollments.jadwal_detail',
                'enrollments.kelas',
                'enrollments.lokasi_cabang',
                'enrollments.metode',
                'enrollments.mentor_assignment_status',
                'enrollments.mentor_assignment_note',
                'enrollments.mentor_requested_at',
                'enrollments.mentor_responded_at',
                'students.name as student_name',
                'programs.name as program_name',
                'programs.jenjang as program_jenjang',
                'mentors.name as mentor_name'
            );

        if ($statusFilter === 'butuh_aksi') {
            $placementsQuery->where(function ($query) {
                $query->whereNull('enrollments.mentor_id')
                    ->orWhereIn('enrollments.mentor_assignment_status', ['pending', 'rejected']);
            });
        } elseif ($statusFilter === 'unassigned') {
            $placementsQuery->whereNull('enrollments.mentor_id');
        } elseif ($statusFilter === 'approved') {
            $placementsQuery->where('enrollments.mentor_assignment_status', 'approved')
                ->whereNotNull('enrollments.mentor_id');
        } elseif (in_array($statusFilter, ['pending', 'rejected'], true)) {
            $placementsQuery->where('enrollments.mentor_assignment_status', $statusFilter);
        }

        $placements = $placementsQuery
            ->orderByRaw("CASE WHEN enrollments.mentor_assignment_status = 'pending' THEN 0 WHEN enrollments.mentor_assignment_status = 'rejected' THEN 1 ELSE 2 END")
            ->orderByDesc('enrollments.created_at')
            ->paginate(12)
            ->withQueryString();

        $placements->getCollection()->transform(function ($item) {
            $item->mentor_assignment_status = $item->mentor_assignment_status
                ?: ($item->mentor_id ? 'approved' : 'unassigned');

            $item->potential_conflicts = [];
            if ($item->mentor_id && !empty($item->jadwal_detail)) {
                $item->potential_conflicts = $this->getMentorScheduleConflicts((int) $item->mentor_id, $item->jadwal_detail, (int) $item->id);
            }

            return $item;
        });

        $placementStats = [
            'pending' => DB::table('enrollments')->where('status_pembayaran', 'verified')->where('mentor_assignment_status', 'pending')->count(),
            'unassigned' => DB::table('enrollments')->where('status_pembayaran', 'verified')->whereNull('mentor_id')->count(),
            'approved' => DB::table('enrollments')->where('status_pembayaran', 'verified')->where('mentor_assignment_status', 'approved')->whereNotNull('mentor_id')->count(),
            'rejected' => DB::table('enrollments')->where('status_pembayaran', 'verified')->where('mentor_assignment_status', 'rejected')->count(),
        ];

        $mentors = User::where('role', 'mentor')
            ->select('id', 'name', 'specialization')
            ->orderBy('name')
            ->get();

        return view('admin.mentor_placements', compact('placements', 'mentors', 'placementStats', 'statusFilter'));
    }

    public function assignMentorToEnrollment(Request $request, $id) {
        $request->validate([
            'mentor_id' => 'required|exists:users,id',
        ]);

        $mentor = User::where('id', $request->mentor_id)->where('role', 'mentor')->first();
        if (!$mentor) {
            return back()->with('error', 'Mentor tidak valid.');
        }

        $enrollment = DB::table('enrollments')
            ->where('id', $id)
            ->where('status_pembayaran', 'verified')
            ->first();

        if (!$enrollment) {
            return back()->with('error', 'Data siswa tidak ditemukan atau belum diverifikasi.');
        }

        $jadwalDetail = trim((string) ($enrollment->jadwal_detail ?? ''));
        if ($jadwalDetail !== '') {
            $conflicts = $this->getMentorScheduleConflicts((int) $mentor->id, $jadwalDetail, (int) $enrollment->id);
            if (!empty($conflicts)) {
                $firstConflict = $conflicts[0];
                return back()->with('error', 'Penempatan ditolak. Jadwal tabrakan dengan siswa ' . $firstConflict['student_name'] . ' (' . $firstConflict['day'] . ' ' . $firstConflict['time_range'] . ').');
            }
        }

        DB::table('enrollments')->where('id', $id)->update([
            'mentor_id' => $mentor->id,
            'mentor_assignment_status' => 'pending',
            'mentor_assignment_note' => null,
            'mentor_requested_at' => now(),
            'mentor_responded_at' => null,
            'assigned_by_admin_id' => Auth::id(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Penempatan mentor dikirim. Menunggu persetujuan mentor.');
    }

    // --- MANAJEMEN PROGRAM ---

    public function adminPrograms(Request $request, $type = null) {
    $query = DB::table('programs')
        ->leftJoin('users', 'programs.mentor_id', '=', 'users.id')
        ->select('programs.*', 'users.name as mentor_name');

    if ($type) {
        $query->where('programs.type', $type);
        $title = "Manajemen Program " . ucfirst($type);
    } else {
        $title = "Katalog Semua Program";
    }

    $programs = $query->get()->map(function($program) {
        $program->jumlah_peserta = DB::table('enrollments')
            ->where('program_id', $program->id)
            ->where('status_pembayaran', 'verified')
            ->count();
        
        $program->mentor = (object) ['name' => $program->mentor_name ?? 'Belum Ada'];
        return $program;
    });

    // REVISI: Tambahkan daftar jenjang untuk dikirim ke view
    $list_jenjang = ['TK', 'SD', 'SMP', 'SMA', 'Umum'];
    $mentors = User::where('role', 'mentor')->get();
    $subjects = DB::table('subjects')->orderBy('name', 'asc')->get();

    return view('admin.programs', compact('programs', 'mentors', 'subjects', 'title', 'type', 'list_jenjang'));
}

    public function storeProgram(Request $request) {
    $request->validate([
        'name' => 'required|string|max:255',
        // BAGIAN YANG DIREVISI: Menambahkan 'TK' ke dalam daftar yang diizinkan
        'jenjang' => 'required|in:TK,SD,SMP,SMA,Umum', 
        'type' => 'required|in:reguler,intensif',
        'price' => 'required|numeric|min:0',
        'extra_meeting_price' => 'required|numeric|min:0',
        'quran_price' => 'required|numeric|min:0',
    ]);

        DB::table('programs')->insert(array_merge($request->only([
            'name', 'jenjang', 'type', 'price', 'extra_meeting_price', 'quran_price', 'mentor_id', 'description'
        ]), ['created_at' => now(), 'updated_at' => now()]));

        return back()->with('success', 'Program baru berhasil ditambahkan!');
    }

    public function updateProgram(Request $request, $id) {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        DB::table('programs')->where('id', $id)->update(array_merge($request->only([
            'name', 'price', 'extra_meeting_price', 'quran_price', 'description', 'mentor_id'
        ]), ['updated_at' => now()]));

        return back()->with('success', 'Data program berhasil diperbarui!');
    }

    public function deleteProgram($id) {
        if (DB::table('enrollments')->where('program_id', $id)->exists()) {
            return back()->with('error', 'Gagal! Program ini masih memiliki siswa terdaftar.');
        }

        DB::table('programs')->where('id', $id)->delete();
        return back()->with('success', 'Program berhasil dihapus.');
    }

    // --- MANAJEMEN MENTOR ---

    public function adminMentors() {
        // Menghapus logika sinkronisasi otomatis di sini untuk performa lebih baik.
        // Sinkronisasi sebaiknya dilakukan saat pembuatan mentor baru saja.
        $mentors = Mentor::orderBy('created_at', 'desc')->get();
        return view('admin.mentors', compact('mentors'));
    }

    public function storeMentor(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'specialist' => 'required',
            'whatsapp' => 'required',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $user = User::create([
                    'name' => $request->name,
                    'username' => $request->username,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'role' => 'mentor',
                    'whatsapp' => $request->whatsapp,
                    'specialization' => $request->specialist
                ]);

                $photoPath = $request->hasFile('photo') ? $request->file('photo')->store('mentors', 'public') : null;

                Mentor::create([
                    'user_id' => $user->id,
                    'name' => $request->name,
                    'specialist' => $request->specialist,
                    'whatsapp' => $request->whatsapp,
                    'photo' => $photoPath,
                ]);
            });
            return back()->with('success', 'Akun Mentor berhasil dibuat!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function updateMentor(Request $request, $id) {
        $mentor = Mentor::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'specialist' => 'required',
            'whatsapp' => 'required',
            'photo' => 'nullable|image|max:2048',
        ]);

        DB::transaction(function () use ($request, $mentor) {
            User::where('id', $mentor->user_id)->update([
                'name' => $request->name,
                'whatsapp' => $request->whatsapp,
                'specialization' => $request->specialist
            ]);

            $data = $request->only(['name', 'specialist', 'whatsapp']);
            if ($request->hasFile('photo')) {
                if ($mentor->photo) Storage::disk('public')->delete($mentor->photo);
                $data['photo'] = $request->file('photo')->store('mentors', 'public');
            }
            $mentor->update($data);
        });

        return back()->with('success', 'Data mentor berhasil diperbarui!');
    }

    public function deleteMentor($id) {
        $mentor = Mentor::findOrFail($id);
        $userId = $mentor->user_id;

        DB::transaction(function () use ($mentor, $userId) {
            if ($mentor->photo) Storage::disk('public')->delete($mentor->photo);
            $mentor->delete();
            if ($userId) User::where('id', $userId)->delete();
        });

        return back()->with('success', 'Mentor dan akun login berhasil dihapus.');
    }

    // --- PESAN & FAQ ---

    public function adminMessages() {
        $messages = DB::table('messages')->orderBy('created_at', 'desc')->get()
            ->map(function($msg) {
                $msg->created_at = Carbon::parse($msg->created_at);
                return $msg;
            });
        return view('admin.messages', compact('messages'));
    }

    public function deleteMessage($id) {
        DB::table('messages')->where('id', $id)->delete();
        return back()->with('success', 'Pesan berhasil dihapus.');
    }

    public function adminFaqs(Request $request) {
        $faqs = DB::table('faqs')->orderBy('created_at', 'desc')->get();
        $from_message = $request->has('from_msg_id') ? DB::table('messages')->where('id', $request->from_msg_id)->first() : null;
        return view('admin.faqs', compact('faqs', 'from_message'));
    }

    public function storeFaq(Request $request) {
        $request->validate(['question' => 'required', 'answer' => 'required']);

        DB::table('faqs')->insert([
            'question' => $request->question,
            'answer' => $request->answer,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($request->filled('message_id')) {
            DB::table('messages')->where('id', $request->message_id)->delete();
            return redirect()->route('admin.messages')->with('success', 'Pesan dijawab & masuk FAQ!');
        }

        return redirect()->route('admin.faqs')->with('success', 'FAQ berhasil diterbitkan!');
    }

    public function updateFaq(Request $request, $id) {
        $request->validate(['question' => 'required', 'answer' => 'required']);
        DB::table('faqs')->where('id', $id)->update([
            'question' => $request->question,
            'answer' => $request->answer,
            'updated_at' => now()
        ]);
        return back()->with('success', 'FAQ berhasil diperbarui!');
    }

    public function deleteFaq($id) {
        DB::table('faqs')->where('id', $id)->delete();
        return back()->with('success', 'FAQ berhasil dihapus!');
    }

    public function messageToFaq($id) {
        return redirect()->route('admin.faqs', ['from_msg_id' => $id]);
    }

    // --- MATA PELAJARAN & SETTINGS ---

    public function adminSubjects() {
        $subjects = DB::table('subjects')->orderBy('jenjang', 'asc')->get();
        return view('admin.subjects', compact('subjects'));
    }

    public function storeSubject(Request $request) {
        $request->validate(['name' => 'required|string', 'jenjang' => 'required']);
        DB::table('subjects')->insert([
            'name' => strtoupper($request->name),
            'jenjang' => $request->jenjang,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return back()->with('success', 'Mata pelajaran berhasil ditambahkan!');
    }

    public function updateSubject(Request $request, $id) {
        $request->validate(['name' => 'required|string', 'jenjang' => 'required']);
        DB::table('subjects')->where('id', $id)->update([
            'name' => strtoupper($request->name),
            'jenjang' => $request->jenjang,
            'updated_at' => now()
        ]);
        return back()->with('success', 'Mata pelajaran berhasil diperbarui!');
    }

    public function deleteSubject($id) {
        DB::table('subjects')->where('id', $id)->delete();
        return back()->with('success', 'Mata pelajaran berhasil dihapus!');
    }

    public function adminSettings() { return view('admin.settings'); }

/**
     * ==========================================
     * 5. FITUR SISWA
     * ==========================================
     */
   public function siswaOverview() {
    $user = Auth::user();

    $recent_programs = DB::table('enrollments')
        ->join('programs', 'enrollments.program_id', '=', 'programs.id')
        ->leftJoin('users as mentors', 'enrollments.mentor_id', '=', 'mentors.id')
        ->where('enrollments.user_id', $user->id)
        ->where('enrollments.status_pembayaran', 'verified')
        ->select(
            'enrollments.id as enrollment_id',
            'programs.name as base_program_name', 
            'mentors.name as mentor_name',
            'enrollments.lokasi_cabang', 
            'enrollments.alamat_siswa',  
            'enrollments.kelas', // REVISI: Tambahkan kolom kelas
            'enrollments.mapel as selected_mapel_raw',
            'enrollments.tipe_paket',
            'enrollments.jadwal_detail as sesi_jadwal',
            'enrollments.jumlah_pertemuan', 
            'enrollments.pertemuan_selesai',
            'enrollments.program_id as original_program_id'
        )
        ->get()
        ->map(function($program) use ($user) {
            // PROSES NAMA MAPEL (Sync Intensif & Reguler)
            if ($program->tipe_paket === 'intensif') {
                $program->display_mapel = $program->selected_mapel_raw ?: $program->base_program_name;
            } else {
                $mapelIds = json_decode($program->selected_mapel_raw, true);
                if (!is_array($mapelIds)) { $mapelIds = array_filter(explode(',', $program->selected_mapel_raw)); }
                $mapelNames = DB::table('programs')->whereIn('id', $mapelIds)->pluck('name')->toArray();
                $program->display_mapel = count($mapelNames) > 0 ? implode(', ', $mapelNames) : $program->base_program_name;
            }

            $program->materials = DB::table('program_materials')
                ->where('program_id', $program->original_program_id)
                ->orderBy('session_number', 'asc')
                ->get();
            
            foreach($program->materials as $m) {
                $m->is_attended = DB::table('attendances')
                    ->where('student_id', $user->id)
                    ->where('program_id', $program->enrollment_id)
                    ->where('date', date('Y-m-d')) 
                    ->exists();
            }
            return $program;
        });

    $my_assignments = DB::table('assignments')
        ->join('users as mentors', 'assignments.mentor_id', '=', 'mentors.id')
        ->where('assignments.student_id', $user->id)
        ->select('assignments.*', 'mentors.name as mentor_name')
        ->orderBy('assignments.created_at', 'desc')
        ->limit(5)->get();

    $activities = DB::table('enrollments')
        ->where('enrollments.user_id', $user->id)
        ->select(
            'enrollments.mapel', 
            'enrollments.tipe_paket',
            'enrollments.kelas', // REVISI: Tambahkan kolom kelas di aktivitas
            'enrollments.jadwal_detail', 
            'enrollments.lokasi_cabang',
            'enrollments.per_minggu',
            'enrollments.status_pembayaran as status', 
            'enrollments.created_at'
        )
        ->orderBy('enrollments.created_at', 'desc')
        ->limit(5)->get()
        ->map(function($item) {
            // SINKRONISASI TAMPILAN AKTIVITAS TERBARU
            if ($item->tipe_paket === 'intensif') {
                $namaTampil = $item->mapel ?: 'PROGRAM INTENSIF';
                $item->title = "Pendaftaran Les (" . ($item->kelas ?? '-') . "): " . strtoupper($namaTampil);
            } else {
                $mapelIds = json_decode($item->mapel, true);
                if (!is_array($mapelIds)) { $mapelIds = array_filter(explode(',', $item->mapel)); }
                $mapelNames = DB::table('programs')->whereIn('id', $mapelIds)->pluck('name')->toArray();
                $namaTampil = count($mapelNames) > 0 ? implode(', ', $mapelNames) : 'Program Reguler';
                $item->title = "Pendaftaran Les (" . ($item->kelas ?? '-') . "): " . strtoupper($namaTampil);
            }
            
            // DETEKSI METODE ONLINE/OFFLINE
            $isOnline = str_contains(strtolower($item->lokasi_cabang ?? ''), 'online');
            $metode = $isOnline ? "Online (Zoom)" : "Offline (" . ($item->lokasi_cabang ?: 'Cabang') . ")";
            
            $item->description = "Jadwal: " . ($item->jadwal_detail ?: '-') . " | Lokasi: " . $metode . " | Frekuensi: " . ($item->per_minggu ?: '0') . "x Seminggu";
            
            return $item;
        });

    $stats = [
        'completed_tasks' => DB::table('task_submissions')->where('user_id', $user->id)->count(),
        'total_tasks'     => DB::table('assignments')->where('student_id', $user->id)->count(),
        'average_score'   => DB::table('task_submissions')->where('user_id', $user->id)->avg('score') ?? 0,
        'attendance'      => DB::table('attendances')->where('student_id', $user->id)->count(),
    ];

    return view('siswa.overview', compact('recent_programs', 'my_assignments', 'activities', 'stats'));
}

    public function siswaPrograms() {
    $userId = Auth::id();

    $my_programs = DB::table('enrollments')
        ->join('programs', 'enrollments.program_id', '=', 'programs.id')
        ->leftJoin('users as mentors', 'enrollments.mentor_id', '=', 'mentors.id')
        ->where('enrollments.user_id', $userId)
        ->select(
            'enrollments.id as enrollment_id', 
            'programs.name as base_name', 
            'mentors.name as mentor_name', 
            'enrollments.status_pembayaran', 
            'enrollments.mapel', 
            'enrollments.tipe_paket',
            'enrollments.kelas', // Kolom kelas untuk tingkatan siswa
            'enrollments.lokasi_cabang', // REVISI: Tambahkan kolom ini agar bisa diakses View
            'enrollments.alamat_siswa',  // REVISI: Tambahkan kolom ini untuk detail lokasi offline
            'enrollments.jumlah_pertemuan', 
            'enrollments.pertemuan_selesai', 
            'enrollments.is_absen_active',
            'enrollments.program_id as base_program_id'
        )
        ->get()
        ->map(function($item) use ($userId) {
            // 1. Judul Modal
            $item->display_mapel = $item->mapel ?: ($item->tipe_paket === 'intensif' ? $item->base_name : "Program Reguler");

            // 2. AMBIL MATERI (Menyesuaikan input database Anda yang menggunakan ID Enrollment)
            $item->materials = DB::table('program_materials')
                ->where('program_id', $item->enrollment_id) // Mengikuti Screenshot (1268) di mana isinya angka 10
                ->orderBy('session_number', 'asc')
                ->get()
                ->map(function($mat) use ($item, $userId) {
                    // 3. AMBIL NILAI & REVIEW (Mengikuti Screenshot (1269) di mana isinya angka 10)
                    $mat->grade = DB::table('grades')
                        ->where('program_id', $item->enrollment_id) 
                        ->where('student_id', $userId)
                        ->where(function($q) use ($mat) {
                            // Mencocokkan berdasarkan angka sesi atau judul
                            $q->where('title', 'like', '%' . $mat->session_number . '%')
                              ->orWhere('title', 'like', '%' . $mat->title . '%');
                        })
                        ->first();
                    return $mat;
                });
            return $item;
        });

    return view('siswa.programs', ['my_programs' => $my_programs]);
}

    public function siswaSchedule() {
    $schedules = DB::table('enrollments')
        ->join('programs', 'enrollments.program_id', '=', 'programs.id')
        ->leftJoin('users as mentors', 'enrollments.mentor_id', '=', 'mentors.id')
        ->where('enrollments.user_id', Auth::id())
        ->where('enrollments.status_pembayaran', 'verified')
        ->select(
            'programs.name as program_name', 
            'mentors.name as mentor_name', 
            'mentors.whatsapp as mentor_wa', // Tambahkan WhatsApp Mentor
            'enrollments.mapel', 
            'enrollments.jadwal_detail',
            'enrollments.lokasi_cabang', 
            'enrollments.alamat_siswa',
            'enrollments.jenjang'
        )
        ->get()
        ->map(function($item) {
            $mapelIds = json_decode($item->mapel, true);
            if (!is_array($mapelIds)) { $mapelIds = [$item->mapel]; }

            $mapelNames = DB::table('programs')->whereIn('id', $mapelIds)->pluck('name')->toArray();
            $item->display_mapel = count($mapelNames) > 0 ? implode(', ', $mapelNames) : $item->program_name;

            // REVISI: Ganti status Zoom dengan informasi koordinasi
            if (empty($item->lokasi_cabang) && (empty($item->alamat_siswa) || $item->alamat_siswa == '-')) {
                $item->metode = 'DARING (ONLINE)';
                $item->lokasi_display = 'Koordinasi via WhatsApp Group / Mentor';
            } else {
                $item->metode = 'TATAP MUKA (OFFLINE)';
                $item->lokasi_display = $item->alamat_siswa ?: $item->lokasi_cabang;
            }

            return $item;
        });

    return view('siswa.schedule', compact('schedules'));
}

    public function siswaBilling() {
    $payments = DB::table('enrollments')
        ->join('programs', 'enrollments.program_id', '=', 'programs.id')
        ->where('enrollments.user_id', Auth::id())
        ->select(
            'enrollments.*', 
            'programs.name as base_program',
            'enrollments.mapel as mapel_raw',
            'enrollments.kelas' // REVISI: Tambahkan kolom kelas agar bisa muncul di riwayat pembayaran
        )
        ->orderBy('enrollments.created_at', 'desc')
        ->get()
        ->map(function($item) {
            // 1. KONVERSI ID MAPEL KE NAMA ASLI
            $mapelIds = json_decode($item->mapel_raw, true);
            if (!is_array($mapelIds)) {
                $mapelIds = [$item->mapel_raw];
            }

            $mapelNames = DB::table('programs')
                ->whereIn('id', $mapelIds)
                ->pluck('name')
                ->toArray();

            // Properti untuk nama program yang akan ditampilkan
            $item->nama_program = count($mapelNames) > 0 ? implode(', ', $mapelNames) : $item->base_program;

            // 2. FLAG LOKASI UNTUK UI
            $item->is_online = empty($item->lokasi_cabang) && (empty($item->alamat_siswa) || $item->alamat_siswa == '-');
            
            return $item;
        });

    return view('siswa.billing', compact('payments'));
}

    public function submitTask(Request $request) {
        // Validasi diperlonggar agar tidak terlalu sensitif terhadap link
        $request->validate([
            'material_id' => 'required',
            'link' => 'nullable', 
            'file' => 'nullable|file|mimes:pdf|max:10240'
        ]);

        try {
            $userId = Auth::id();
            
            // Cari data lama
            $submission = DB::table('task_submissions')
                ->where('user_id', $userId)
                ->where('material_id', $request->material_id)
                ->first();

            $data = [
                'user_id' => $userId,
                'material_id' => $request->material_id,
                'updated_at' => now()
            ];

            if ($request->filled('link')) {
                $data['task_link'] = $request->link;
            }

            if ($request->hasFile('file')) {
                if ($submission && $submission->file_path) {
                    Storage::disk('public')->delete($submission->file_path);
                }
                $data['file_path'] = $request->file('file')->store('task_submissions', 'public');
            }

            if ($submission) {
                DB::table('task_submissions')->where('id', $submission->id)->update($data);
            } else {
                $data['created_at'] = now();
                DB::table('task_submissions')->insert($data);
            }

            return response()->json(['success' => true, 'message' => 'Tugas berhasil dikirim!']);
        } catch (\Exception $e) {
            // Tangkap error database dan kirimkan ke depan sebagai JSON agar tidak muncul 'Server Error'
            return response()->json(['success' => false, 'message' => 'Gagal simpan: ' . $e->getMessage()], 500);
        }
    }

    public function siswaAbsen(Request $request) {
        $request->validate([
            'material_id' => 'required',
            'status' => 'required|in:Hadir,Izin,Sakit' // REVISI: Tambahkan status
        ]);
        
        // REVISI: Di sini kita gunakan ID Pendaftaran (Enrollment) sebagai acuan
        $enrollmentId = $request->material_id; 
        $enrollment = DB::table('enrollments')->where('id', $enrollmentId)->first();
        
        if (!$enrollment || !$enrollment->is_absen_active) {
            return response()->json(['success' => false, 'message' => 'Absensi belum dibuka oleh mentor.'], 403);
        }

        $today = date('Y-m-d');
        $alreadyAttended = DB::table('attendances')
            ->where('student_id', Auth::id())
            ->where('program_id', $enrollmentId)
            ->where('date', $today)
            ->exists();

        if ($alreadyAttended) {
            return response()->json(['success' => false, 'message' => 'Anda sudah melakukan absensi hari ini.'], 400);
        }

        // REVISI: Gunakan Transaksi agar data absen & progress sesi sinkron
        DB::transaction(function () use ($enrollmentId, $today, $request) {
            // 1. Simpan ke tabel absensi
            DB::table('attendances')->insert([
                'student_id' => Auth::id(),
                'program_id' => $enrollmentId,
                'date' => $today,
                'status' => $request->status, // Gunakan status yang dipilih siswa
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // 2. Update progress: Tambah 1 pada kolom pertemuan_selesai
            DB::table('enrollments')->where('id', $enrollmentId)->increment('pertemuan_selesai');
        });

        return response()->json(['success' => true, 'message' => 'Berhasil absen!']);
    }

   public function enrollProgram(Request $request) {
    $paymentMethod = $request->input('payment_method', 'manual');
    if (!in_array($paymentMethod, ['manual', 'midtrans'], true)) {
        $paymentMethod = 'manual';
    }
    $request->merge(['payment_method' => $paymentMethod]);

    $request->validate([
        'program_id' => 'required',
        'jenjang' => 'required',
        'kelas' => 'required',
        'tipe_paket' => 'required',
        'per_minggu' => 'required',
        'jadwal_detail' => 'required',
        'bukti_pembayaran' => $paymentMethod === 'manual'
            ? 'required|image|mimes:jpg,jpeg,png,webp|max:2048'
            : 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'total_harga' => 'required',
        'payment_method' => 'required|in:manual,midtrans',
    ]);

    try {
        DB::beginTransaction();
        $user = Auth::user();

        $program = DB::table('programs')->where('id', $request->program_id)->first();
        if (!$program) {
            return redirect()->back()->with('error', 'Gagal: Program ID ('.$request->program_id.') tidak ditemukan di database.');
        }

        $baseAmount = (int) preg_replace('/[^0-9]/', '', $request->total_harga);
        $finalAmount = $baseAmount;

        if ($paymentMethod === 'manual') {
            // Kode unik dipakai untuk transfer manual agar rekonsiliasi lebih mudah.
            $cleanPhone = preg_replace('/[^0-9]/', '', $user->whatsapp ?? '000');
            $uniqueCode = (int) substr($cleanPhone, -3);
            $finalAmount += $uniqueCode;
        }

        $buktiPembayaranPath = null;
        if ($paymentMethod === 'manual') {
            $path = $request->file('bukti_pembayaran')->store('pembayaran/reguler', 'public');
            $buktiPembayaranPath = 'pembayaran/reguler/' . basename($path);
        }

        if ($request->tipe_paket === 'intensif') {
            $mapelString = $request->mapel ?? 'Program Intensif';
        } else {
            $mapelIds = json_decode($request->selected_subjects ?? '[]', true);
            if (!is_array($mapelIds)) {
                $mapelIds = [$request->program_id];
            }

            $mapelNames = DB::table('programs')
                ->whereIn('id', $mapelIds)
                ->pluck('name')
                ->toArray();

            $mapelString = count($mapelNames) > 0 ? implode(', ', $mapelNames) : 'Program Reguler';
        }

        $isOnline = str_contains(strtolower($request->lokasi_cabang ?? ''), 'online');

        $enrollmentId = DB::table('enrollments')->insertGetId([
            'user_id' => $user->id,
            'program_id' => $request->program_id,
            'jenjang' => $request->jenjang,
            'kelas' => $request->kelas,
            'tipe_paket' => $request->tipe_paket,
            'per_minggu' => $request->per_minggu,
            'extra_hours' => $request->extra_hours ?? 0,
            'is_mengaji' => $request->is_mengaji ?? 0,
            'jadwal_detail' => $request->jadwal_detail,
            'mapel' => $mapelString,
            'metode' => $isOnline ? 'online' : 'offline',
            'lokasi_cabang' => $request->lokasi_cabang,
            'alamat_semarang' => $request->alamat_siswa ?? '-',
            'total_harga' => $finalAmount,
            'status_pembayaran' => 'pending',
            'payment_method' => $paymentMethod,
            'bukti_pembayaran' => $buktiPembayaranPath,
            'jumlah_pertemuan' => ($request->tipe_paket === 'intensif') ? 12 : 8,
            'pertemuan_selesai' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $snapRedirectUrl = null;

        if ($paymentMethod === 'midtrans') {
            $serverKey = config('midtrans.server_key');
            $clientKey = config('midtrans.client_key');

            if (
                empty($serverKey)
                || empty($clientKey)
                || str_contains($serverKey, 'REPLACE_WITH_YOUR_SANDBOX_SERVER_KEY')
                || str_contains($clientKey, 'REPLACE_WITH_YOUR_SANDBOX_CLIENT_KEY')
            ) {
                Log::error('Midtrans server key is missing in configuration.');
                throw new \Exception('Pembayaran online belum aktif. Silakan hubungi admin untuk mengaktifkan gateway pembayaran.');
            }

            $orderId = 'MANDALA-ENR-' . $enrollmentId . '-' . now()->format('YmdHis');

            DB::table('enrollments')->where('id', $enrollmentId)->update([
                'midtrans_order_id' => $orderId,
                'midtrans_transaction_status' => 'initiated',
                'updated_at' => now(),
            ]);

            $midtransBaseUrl = config('midtrans.is_production')
                ? 'https://app.midtrans.com'
                : 'https://app.sandbox.midtrans.com';

            $payload = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) $finalAmount,
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->whatsapp,
                ],
                'item_details' => [
                    [
                        'id' => (string) $program->id,
                        'price' => (int) $finalAmount,
                        'quantity' => 1,
                        'name' => substr(($program->name ?? 'Program Reguler') . ' - ' . ($request->kelas ?? '-'), 0, 50),
                    ],
                ],
                'callbacks' => [
                    'finish' => route('midtrans.finish') . '?order_id=' . $orderId,
                    'unfinish' => route('midtrans.unfinish') . '?order_id=' . $orderId,
                    'error' => route('midtrans.error') . '?order_id=' . $orderId,
                ],
                'custom_field1' => 'enrollment_id:' . $enrollmentId,
            ];

            $http = Http::withBasicAuth($serverKey, '')
                ->acceptJson()
                ->asJson();

            if (config('midtrans.disable_ssl_verification')) {
                $http = $http->withoutVerifying();
            }

            $response = $http->post($midtransBaseUrl . '/snap/v1/transactions', $payload);
            if (!$response->successful()) {
                Log::error('Midtrans create transaction failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                if ($response->status() === 401) {
                    throw new \Exception('Access Key Midtrans tidak valid atau tidak sesuai mode (Sandbox/Production).');
                }

                throw new \Exception('Gagal membuat transaksi Midtrans. Coba lagi beberapa saat.');
            }

            $snapData = $response->json();
            $snapRedirectUrl = $snapData['redirect_url'] ?? null;

            if (empty($snapRedirectUrl)) {
                throw new \Exception('Midtrans tidak mengembalikan redirect URL.');
            }

            DB::table('enrollments')->where('id', $enrollmentId)->update([
                'midtrans_snap_token' => $snapData['token'] ?? null,
                'midtrans_payload' => json_encode($snapData),
                'updated_at' => now(),
            ]);
        }

        DB::commit();

        if ($paymentMethod === 'midtrans' && $snapRedirectUrl) {
            return redirect()->away($snapRedirectUrl);
        }

        return redirect()->route('siswa.overview')->with('success', 'Pendaftaran berhasil! Menunggu verifikasi admin.');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Program enrollment failed', [
            'user_id' => Auth::id(),
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        $safePaymentMessages = [
            'Pembayaran online belum aktif. Silakan hubungi admin untuk mengaktifkan gateway pembayaran.',
            'Access Key Midtrans tidak valid atau tidak sesuai mode (Sandbox/Production).',
            'Gagal membuat transaksi Midtrans. Coba lagi beberapa saat.',
            'Koneksi SSL ke Midtrans gagal di server lokal. Aktifkan MIDTRANS_DISABLE_SSL_VERIFICATION=true untuk testing lokal.',
        ];

        if (
            str_contains($e->getMessage(), 'cURL error 60')
            || str_contains($e->getMessage(), 'SSL certificate problem')
        ) {
            $e = new \Exception('Koneksi SSL ke Midtrans gagal di server lokal. Aktifkan MIDTRANS_DISABLE_SSL_VERIFICATION=true untuk testing lokal.');
        }

        $userMessage = in_array($e->getMessage(), $safePaymentMessages, true)
            ? $e->getMessage()
            : 'Pendaftaran belum berhasil diproses. Silakan coba lagi.';

        return redirect()->back()
            ->with('error', $userMessage)
            ->with('preserve_reguler_state', true);
    }
}

    public function midtransCallback(Request $request)
    {
        $payload = $request->all();
        if (empty($payload)) {
            $payload = json_decode($request->getContent(), true) ?: [];
        }

        $orderId = $payload['order_id'] ?? null;
        $statusCode = (string) ($payload['status_code'] ?? '');
        $grossAmount = (string) ($payload['gross_amount'] ?? '');
        $signatureKey = (string) ($payload['signature_key'] ?? '');
        $serverKey = config('midtrans.server_key');

        if (!$orderId || !$statusCode || !$grossAmount || !$signatureKey || !$serverKey) {
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        $generatedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        if (!hash_equals($generatedSignature, $signatureKey)) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $this->syncMidtransStatusToEnrollment($payload);

        return response()->json(['message' => 'ok']);
    }

    public function midtransFinish(Request $request)
    {
        $orderId = $request->query('order_id');

        $enrollment = null;
        if ($orderId) {
            $statusPayload = $this->fetchMidtransStatus($orderId);
            if ($statusPayload) {
                $this->syncMidtransStatusToEnrollment($statusPayload);
            }

            $enrollment = DB::table('enrollments')
                ->where('midtrans_order_id', $orderId)
                ->first();
        }

        if ($enrollment && $enrollment->status_pembayaran === 'verified') {
            return redirect()->route('siswa.overview')->with('success', 'Pembayaran berhasil. Selamat datang di dashboard siswa!');
        }

        if ($enrollment && $enrollment->status_pembayaran === 'pending') {
            return redirect()->route('siswa.billing')->with('success', 'Pembayaran sedang diproses. Status akan ter-update otomatis.');
        }

        if ($enrollment && $enrollment->status_pembayaran === 'rejected') {
            return redirect()->route('siswa.billing')->with('error', 'Pembayaran tidak berhasil. Silakan coba metode pembayaran lain.');
        }

        return redirect()->route('siswa.overview')->with('success', 'Transaksi telah diproses.');
    }

    public function adminSyncMidtrans(Request $request)
    {
        $orderId = $request->query('order_id');
        if (empty($orderId)) {
            return redirect()->route('admin.payments')->with('error', 'Order ID Midtrans tidak ditemukan.');
        }

        $statusPayload = $this->fetchMidtransStatus($orderId);
        if (!$statusPayload) {
            return redirect()->route('admin.payments')->with('error', 'Gagal sinkron status Midtrans. Coba lagi beberapa saat.');
        }

        $this->syncMidtransStatusToEnrollment($statusPayload);

        $enrollment = DB::table('enrollments')
            ->where('midtrans_order_id', $orderId)
            ->first();

        if (!$enrollment) {
            return redirect()->route('admin.payments')->with('error', 'Data enrollment untuk order Midtrans tidak ditemukan.');
        }

        if ($enrollment->status_pembayaran === 'verified') {
            return redirect()->route('admin.payments')->with('success', 'Status Midtrans berhasil disinkronkan: pembayaran sudah verified.');
        }

        if ($enrollment->status_pembayaran === 'rejected') {
            return redirect()->route('admin.payments')->with('error', 'Status Midtrans: pembayaran ditolak/expire/cancel.');
        }

        return redirect()->route('admin.payments')->with('success', 'Status Midtrans berhasil disinkronkan: transaksi masih pending.');
    }

    public function midtransUnfinish()
    {
        return redirect()->route('siswa.billing')->with('error', 'Pembayaran belum selesai. Silakan lanjutkan transaksi dari halaman tagihan.');
    }

    public function midtransError()
    {
        return redirect()->route('siswa.billing')->with('error', 'Terjadi kendala pada pembayaran Midtrans. Silakan coba kembali.');
    }

    private function fetchMidtransStatus(string $orderId): ?array
    {
        $serverKey = config('midtrans.server_key');
        if (empty($serverKey)) {
            return null;
        }

        $midtransBaseUrl = config('midtrans.is_production')
            ? 'https://api.midtrans.com'
            : 'https://api.sandbox.midtrans.com';

        $http = Http::withBasicAuth($serverKey, '')
            ->acceptJson();

        if (config('midtrans.disable_ssl_verification')) {
            $http = $http->withoutVerifying();
        }

        $response = $http->get($midtransBaseUrl . '/v2/' . $orderId . '/status');
        if (!$response->successful()) {
            return null;
        }

        return $response->json();
    }

    private function syncMidtransStatusToEnrollment(array $payload): void
    {
        $orderId = $payload['order_id'] ?? null;
        if (!$orderId) {
            return;
        }

        $enrollment = DB::table('enrollments')
            ->where('midtrans_order_id', $orderId)
            ->first();
        if (!$enrollment) {
            return;
        }

        $transactionStatus = strtolower((string) ($payload['transaction_status'] ?? 'pending'));
        $fraudStatus = strtolower((string) ($payload['fraud_status'] ?? 'accept'));
        $paymentType = $payload['payment_type'] ?? null;

        $statusPembayaran = 'pending';

        if (in_array($transactionStatus, ['capture', 'settlement'], true)) {
            $statusPembayaran = ($fraudStatus === 'challenge') ? 'pending' : 'verified';
        } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure', 'refund', 'partial_refund'], true)) {
            $statusPembayaran = 'rejected';
        }

        // Jika sudah diverifikasi manual/admin, jangan diturunkan lagi ke pending/rejected.
        if (($enrollment->status_pembayaran ?? null) === 'verified' && $statusPembayaran !== 'verified') {
            $statusPembayaran = 'verified';
        }

        DB::table('enrollments')
            ->where('id', $enrollment->id)
            ->update([
                'status_pembayaran' => $statusPembayaran,
                'midtrans_transaction_status' => $transactionStatus,
                'midtrans_payment_type' => $paymentType,
                'midtrans_payload' => json_encode($payload),
                'paid_at' => $statusPembayaran === 'verified' ? ($enrollment->paid_at ?? now()) : null,
                'updated_at' => now(),
            ]);
    }
    
    /**
     * ==========================================
     * FITUR MENTOR (OTOMATIS BERDASARKAN ADMIN)
     * ==========================================
     */
    public function mentorOverview() {
    $user = Auth::user();
    $hari_ini = Carbon::now()->locale('id')->dayName;

    $pendingApprovals = DB::table('enrollments')
        ->join('users', 'enrollments.user_id', '=', 'users.id')
        ->leftJoin('programs', 'enrollments.program_id', '=', 'programs.id')
        ->where('enrollments.mentor_id', $user->id)
        ->where('enrollments.status_pembayaran', 'verified')
        ->where('enrollments.mentor_assignment_status', 'pending')
        ->select(
            'enrollments.id',
            'enrollments.jadwal_detail',
            'enrollments.kelas',
            'enrollments.mapel',
            'users.name as student_name',
            'programs.name as program_name',
            'programs.jenjang as program_jenjang'
        )
        ->orderByDesc('enrollments.mentor_requested_at')
        ->get();

    $today_schedule = DB::table('enrollments')
        ->join('users', 'enrollments.user_id', '=', 'users.id')
        ->where('enrollments.mentor_id', $user->id)
        ->where('enrollments.status_pembayaran', 'verified')
        ->where(function ($query) {
            $query->where('enrollments.mentor_assignment_status', 'approved')
                ->orWhereNull('enrollments.mentor_assignment_status');
        })
        ->select(
            'enrollments.id', 
            'enrollments.mapel as program_name', 
            'enrollments.jam_mulai', 
            'enrollments.kelas', // REVISI: Tambahkan kolom kelas
            'users.name as student_name', 
            'enrollments.lokasi_cabang',
            'enrollments.hari',
            'enrollments.jadwal_detail',
            'enrollments.alamat_semarang as alamat_siswa'
        )
        ->get()
        ->filter(function($item) use ($hari_ini) {
            $search = strtolower($hari_ini);
            return str_contains(strtolower($item->hari ?? ''), $search) || 
                   str_contains(strtolower($item->jadwal_detail ?? ''), $search);
        })
        ->map(function($item) use ($hari_ini) {
            // REVISI LOGIKA JAM: Mencari jam spesifik hari ini dari string jadwal_detail
            $jamDitemukan = null;
            if ($item->jadwal_detail) {
                $parts = explode(',', $item->jadwal_detail);
                foreach ($parts as $part) {
                    if (str_contains(strtolower($part), strtolower($hari_ini))) {
                        // Mengambil angka format jam (misal 15.00 atau 15:00)
                        preg_match('/([\d.]+)/', $part, $matches);
                        if (isset($matches[1])) {
                            $jamDitemukan = str_replace('.', ':', $matches[1]);
                            break;
                        }
                    }
                }
            }
            
            // Jika jam spesifik tidak ditemukan, gunakan default jam_mulai atau label info
            $item->jam_tampil = $jamDitemukan ?: ($item->jam_mulai ? Carbon::parse($item->jam_mulai)->format('H:i') : 'CEK JADWAL');
            return $item;
        });

    $assignments = DB::table('assignments')
        ->join('users', 'assignments.student_id', '=', 'users.id')
        ->where('assignments.mentor_id', $user->id)
        ->select('assignments.*', 'users.name as student_name')
        ->orderBy('assignments.created_at', 'desc')
        ->get()
        ->map(function($item) {
            $item->created_at = Carbon::parse($item->created_at);
            return $item;
        });

    $stats = [
        'total_siswa' => DB::table('enrollments')
            ->where('mentor_id', $user->id)
            ->where('status_pembayaran', 'verified')
            ->where(function ($query) {
                $query->where('mentor_assignment_status', 'approved')
                    ->orWhereNull('mentor_assignment_status');
            })
            ->distinct('user_id')->count(),
        'total_kelas' => DB::table('enrollments')
            ->where('mentor_id', $user->id)
            ->where('status_pembayaran', 'verified')
            ->where(function ($query) {
                $query->where('mentor_assignment_status', 'approved')
                    ->orWhereNull('mentor_assignment_status');
            })
            ->distinct('mapel')->count(),
    ];

    return view('mentor.overview', compact('user', 'today_schedule', 'stats', 'assignments', 'pendingApprovals'));
}

    public function mentorApprovePlacement($id) {
        $enrollment = DB::table('enrollments')
            ->join('users', 'enrollments.user_id', '=', 'users.id')
            ->where('enrollments.id', $id)
            ->where('enrollments.mentor_id', Auth::id())
            ->where('enrollments.status_pembayaran', 'verified')
            ->select('enrollments.*', 'users.name as student_name')
            ->first();

        if (!$enrollment) {
            return back()->with('error', 'Data penempatan tidak ditemukan.');
        }

        if (($enrollment->mentor_assignment_status ?? 'approved') !== 'pending') {
            return back()->with('error', 'Penempatan ini sudah diproses sebelumnya.');
        }

        $jadwalDetail = trim((string) ($enrollment->jadwal_detail ?? ''));
        if ($jadwalDetail !== '') {
            $conflicts = $this->getMentorScheduleConflicts((int) Auth::id(), $jadwalDetail, (int) $enrollment->id);
            if (!empty($conflicts)) {
                $firstConflict = $conflicts[0];
                return back()->with('error', 'Tidak bisa approve, jadwal tabrakan dengan siswa ' . $firstConflict['student_name'] . ' (' . $firstConflict['day'] . ' ' . $firstConflict['time_range'] . ').');
            }
        }

        DB::table('enrollments')->where('id', $id)->update([
            'mentor_assignment_status' => 'approved',
            'mentor_assignment_note' => 'Disetujui mentor',
            'mentor_responded_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Penempatan disetujui. Jadwal masuk ke daftar mengajar Anda.');
    }

    public function mentorRejectPlacement(Request $request, $id) {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $updated = DB::table('enrollments')
            ->where('id', $id)
            ->where('mentor_id', Auth::id())
            ->where('status_pembayaran', 'verified')
            ->where('mentor_assignment_status', 'pending')
            ->update([
                'mentor_assignment_status' => 'rejected',
                'mentor_assignment_note' => $request->reason,
                'mentor_responded_at' => now(),
                'updated_at' => now(),
            ]);

        if (!$updated) {
            return back()->with('error', 'Penempatan tidak bisa ditolak karena status sudah berubah.');
        }

        return back()->with('success', 'Penempatan ditolak. Admin akan melakukan penjadwalan ulang.');
    }

    public function storeAssignment(Request $request) {
        $request->validate([
            'student_id' => 'required|exists:users,id', 
            'title' => 'required|string|max:255',
            'file' => 'nullable|file|max:5120'
        ]);

        $fileName = $request->hasFile('file') ? $request->file('file')->store('assignments', 'public') : null;

        DB::table('assignments')->insert([
            'mentor_id' => Auth::id(),
            'student_id' => $request->student_id,
            'title' => $request->title,
            'description' => $request->description,
            'link' => $request->link,
            'file_path' => $fileName,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('activities')->insert([
            'user_id' => $request->student_id,
            'title' => 'Tugas Baru: ' . $request->title,
            'type' => 'assignment',
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Tugas dikirim!');
    }

    public function deleteAssignment($id) {
        $assignment = DB::table('assignments')->where('id', $id)->where('mentor_id', Auth::id())->first();
        if ($assignment) {
            if ($assignment->file_path) Storage::disk('public')->delete($assignment->file_path);
            DB::table('assignments')->where('id', $id)->delete();
            return back()->with('success', 'Tugas dihapus!');
        }
        return back()->with('error', 'Tugas tidak ditemukan.');
    }

    public function mentorClasses(Request $request) {
    $user = Auth::user();
    $today = date('Y-m-d');
    $targetId = $request->query('id');

    $classes = DB::table('enrollments')
        ->join('users', 'enrollments.user_id', '=', 'users.id') 
        ->where('enrollments.mentor_id', $user->id)
        ->where('enrollments.status_pembayaran', 'verified')
        ->where(function ($query) {
            $query->where('enrollments.mentor_assignment_status', 'approved')
                ->orWhereNull('enrollments.mentor_assignment_status');
        })
        ->when($targetId, function($query, $targetId) {
            return $query->where('enrollments.id', $targetId);
        })
        ->select(
            'enrollments.id', 
            'enrollments.mapel as name', 
            'enrollments.jenjang', 
            'enrollments.kelas', // REVISI: Tetap pastikan kolom kelas ada
            'enrollments.hari',
            'enrollments.jam_mulai as jam',
            'enrollments.jadwal_detail',
            'enrollments.tipe_paket',
            'users.id as user_id', // REVISI: Gunakan alias yang jelas agar tidak bentrok dengan enrollment id
            'enrollments.is_absen_active',
            'enrollments.pertemuan_selesai',
            'enrollments.jumlah_pertemuan',
            'users.name as student_name'
        )
        ->get()
        ->map(function($class) use ($today, $user) {
            $class->type = ($class->tipe_paket === 'intensif') ? 'Intensif' : 'Reguler'; 
            $class->total_sessions = ($class->jumlah_pertemuan > 0) ? $class->jumlah_pertemuan : (($class->tipe_paket === 'intensif') ? 12 : 8);

            // REVISI: Memastikan join absensi terkunci pada program_id (enrollment id) yang sedang diproses
            $class->students = DB::table('users')
                ->leftJoin('attendances', function($join) use ($today, $class) {
                    $join->on('users.id', '=', 'attendances.student_id')
                         ->where('attendances.program_id', '=', $class->id) // Kunci pada ID pendaftaran spesifik
                         ->where('attendances.date', '=', $today);
                })
                ->where('users.id', $class->user_id)
                ->select('users.id', 'users.name', 'attendances.status as status')
                ->get();
            
            $class->student_count = $class->students->count(); 
            
            // REVISI: Ambil materi sekaligus cek apakah ada tugas (PDF/Link) yang sudah dikirim siswa
            $class->materials = DB::table('program_materials')
                ->where('program_id', $class->id)
                ->orderBy('session_number', 'asc')
                ->get()
                ->map(function($mat) use ($class) {
                    // Cek pengumpulan tugas siswa untuk materi ini di tabel task_submissions
                    $mat->submission = DB::table('task_submissions')
                        ->where('material_id', $mat->id)
                        ->where('user_id', $class->user_id)
                        ->first();
                    return $mat;
                });

            return $class;
        });

    return view('mentor.classes', compact('classes'));
}

    public function toggleAbsen(Request $request) {
        $request->validate(['class_id' => 'required', 'is_active' => 'required|boolean']);
        
        try {
            // REVISI: Update status saklar absensi agar tombol muncul di halaman siswa
            DB::table('enrollments')->where('id', $request->class_id)->update([
                'is_absen_active' => $request->is_active,
                'updated_at' => now()
            ]);
            return response()->json(['success' => true, 'message' => 'Sesi absensi berhasil diperbarui']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Sistem gagal memperbarui sesi']);
        }
    }

    public function storeMaterial(Request $request) {
        $request->validate([
            'program_id' => 'required',
            'session_number' => 'required|integer',
            'title' => 'required|string|max:255',
            'file' => 'nullable|file|max:10240',
            'video_url' => 'nullable|url',
            'quiz_url' => 'nullable|url', // REVISI: Validasi link kuis
        ]);

        $filePath = $request->hasFile('file') ? $request->file('file')->store('materials', 'public') : null;

        DB::table('program_materials')->insert([
            'program_id' => $request->program_id,
            'session_number' => $request->session_number,
            'title' => $request->title,
            'video_url' => $request->video_url,
            'quiz_url' => $request->quiz_url, // REVISI: Simpan link kuis ke database
            'file_path' => $filePath,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Materi & Kuis berhasil diunggah!');
    }

    public function updateMaterial(Request $request, $id) {
        $request->validate([
            'session_number' => 'required|integer',
            'title' => 'required|string|max:255',
            'video_url' => 'nullable|url',
            'quiz_url' => 'nullable|url', // REVISI: Validasi link kuis saat update
        ]);

        $material = DB::table('program_materials')->where('id', $id)->first();
        if (!$material) return back()->with('error', 'Materi tidak ditemukan.');

        $data = [
            'session_number' => $request->session_number,
            'title' => $request->title,
            'video_url' => $request->video_url,
            'quiz_url' => $request->quiz_url, // REVISI: Update link kuis di database
            'updated_at' => now(),
        ];

        if ($request->hasFile('file')) {
            if ($material->file_path) Storage::disk('public')->delete($material->file_path);
            $data['file_path'] = $request->file('file')->store('materials', 'public');
        }

        DB::table('program_materials')->where('id', $id)->update($data);
        return back()->with('success', 'Materi & Kuis berhasil diperbarui!');
    }

    public function storeAttendance(Request $request) {
        $request->validate(['program_id' => 'required', 'attendance' => 'required|array']);
        $date = $request->date ?? date('Y-m-d');
        
        foreach ($request->attendance as $studentId => $status) {
            DB::table('attendances')->updateOrInsert(
                ['program_id' => $request->program_id, 'student_id' => $studentId, 'date' => $date],
                ['status' => $status, 'updated_at' => now()]
            );

            DB::table('activities')->insert([
                'user_id' => $studentId,
                'title' => 'Presensi: Kamu ditandai ' . ucfirst($status),
                'type' => 'attendance',
                'status' => 'verified',
                'created_at' => now(),
            ]);
        }
        return back()->with('success', 'Presensi disimpan!');
    }

    public function storeGrade(Request $request) {
    $request->validate([
        'program_id' => 'required',
        'student_id' => 'required',
        'title' => 'required',
        'score' => 'required|integer|min:0|max:100',
    ]);

    // 1. Simpan atau Perbarui Nilai ke Tabel Grades
    // Menggunakan updateOrInsert agar jika sesi yang sama dinilai ulang, data akan terupdate (tidak double)
    DB::table('grades')->updateOrInsert(
        [
            'program_id' => $request->program_id,
            'student_id' => $request->student_id,
            'title'      => $request->title,
        ],
        [
            'score'      => $request->score,
            'note'       => $request->note,
            'created_at' => now(),
            'updated_at' => now(),
        ]
    );

    // 2. BAGIAN INI TETAP DIHAPUS/DIKOMENTAR agar tidak error karena tabelnya tidak ada
    /* DB::table('activities')->insert([
        'user_id' => $request->student_id,
        'title' => 'Nilai Baru: ' . $request->title . ' (' . $request->score . ')',
        'type' => 'grade',
        'status' => 'verified',
        'created_at' => now(),
    ]);
    */

    return back()->with('success', 'Nilai berhasil dikirim!');
}

    public function deleteMaterial($id) {
        $material = DB::table('program_materials')->where('id', $id)->first();
        if ($material) {
            if ($material->file_path) Storage::disk('public')->delete($material->file_path);
            DB::table('program_materials')->where('id', $id)->delete();
            return back()->with('success', 'Materi dihapus!');
        }
        return back()->with('error', 'Materi tidak ditemukan.');
    }

    public function mentorSchedule() {
    $user = Auth::user();

    $schedule = DB::table('enrollments')
        ->join('users', 'enrollments.user_id', '=', 'users.id')
        ->where('enrollments.mentor_id', $user->id)
        // REVISI: Menggunakan where standar agar sinkron dengan dashboard admin & mentor lainnya
        ->where('enrollments.status_pembayaran', 'verified')
        ->where(function ($query) {
            $query->where('enrollments.mentor_assignment_status', 'approved')
                ->orWhereNull('enrollments.mentor_assignment_status');
        })
        ->select(
            'enrollments.id',
            'enrollments.mapel as program_name', 
            'enrollments.hari', 
            'enrollments.jam_mulai', 
            'enrollments.kelas', // REVISI: Tambahkan kolom kelas di sini
            'enrollments.jadwal_detail',
            'enrollments.alamat_siswa',
            'users.name as student_name',
            'enrollments.jenjang',
            'enrollments.lokasi_cabang'
        )
        ->get();

    $formattedSchedule = $schedule->flatMap(function($item) {
        $sessions = [];
        if ($item->jadwal_detail) {
            // REVISI: Membersihkan spasi dan memproses jadwal dari kolom jadwal_detail secara dinamis
            $parts = explode(',', $item->jadwal_detail);
            foreach ($parts as $part) {
                // Regex untuk menangkap format "Hari (Jam.Menit)"
                preg_match('/(\w+)\s*\(([\d.]+)\)/', strtolower(trim($part)), $matches);
                if (count($matches) >= 3) {
                    $newEntry = clone $item;
                    $newEntry->hari = ucfirst(trim($matches[1]));
                    $newEntry->jam_mulai = str_replace('.', ':', $matches[2]);
                    $sessions[] = $newEntry;
                }
            }
        } 
        
        // Jika jadwal_detail kosong atau regex tidak menemukan hasil, gunakan data default dari kolom hari/jam_mulai
        if (empty($sessions)) {
            $item->jam_mulai = $item->jam_mulai ? Carbon::parse($item->jam_mulai)->format('H:i') : '--:--';
            $sessions[] = $item;
        }
        
        return $sessions;
    });

    return view('mentor.schedule', ['schedule' => $formattedSchedule]);
}
    public function mentorSubmissions() {
        $submissions = DB::table('task_submissions')
            ->join('program_materials', 'task_submissions.material_id', '=', 'program_materials.id')
            ->join('users', 'task_submissions.user_id', '=', 'users.id')
            ->join('enrollments', 'users.id', '=', 'enrollments.user_id')
            ->where('enrollments.mentor_id', Auth::id())
            ->where('enrollments.status_pembayaran', 'verified')
            ->where(function ($query) {
                $query->where('enrollments.mentor_assignment_status', 'approved')
                    ->orWhereNull('enrollments.mentor_assignment_status');
            })
            ->select('task_submissions.*', 'users.name as student_name', 'program_materials.title as session_title')
            ->orderBy('task_submissions.created_at', 'desc')
            ->get();

        return view('mentor.submissions', compact('submissions'));
    }

    private function parseScheduleSlots(?string $jadwalDetail): array {
        if (empty($jadwalDetail)) {
            return [];
        }

        $dayMap = [
            'senin' => 'Senin',
            'selasa' => 'Selasa',
            'rabu' => 'Rabu',
            'kamis' => 'Kamis',
            'jumat' => 'Jumat',
            'sabtu' => 'Sabtu',
            'minggu' => 'Minggu',
        ];

        $slots = [];
        $parts = preg_split('/[,;\n]+/', strtolower($jadwalDetail));

        foreach ($parts as $part) {
            $segment = trim($part);
            if ($segment === '') {
                continue;
            }

            $detectedDay = null;
            foreach ($dayMap as $key => $label) {
                if (str_contains($segment, $key)) {
                    $detectedDay = $label;
                    break;
                }
            }

            if (!$detectedDay) {
                continue;
            }

            $startMinutes = null;
            $endMinutes = null;

            if (preg_match('/(\d{1,2})[.:](\d{2})\s*[-]\s*(\d{1,2})[.:](\d{2})/', $segment, $rangeMatch)) {
                $startMinutes = (((int) $rangeMatch[1]) * 60) + ((int) $rangeMatch[2]);
                $endMinutes = (((int) $rangeMatch[3]) * 60) + ((int) $rangeMatch[4]);
            } elseif (preg_match('/(\d{1,2})[.:](\d{2})/', $segment, $singleMatch)) {
                $startMinutes = (((int) $singleMatch[1]) * 60) + ((int) $singleMatch[2]);
                $endMinutes = $startMinutes + 90;
            }

            if ($startMinutes === null || $endMinutes === null) {
                continue;
            }

            if ($endMinutes <= $startMinutes) {
                $endMinutes += 24 * 60;
            }

            $slots[] = [
                'day' => $detectedDay,
                'start' => $startMinutes,
                'end' => $endMinutes,
            ];
        }

        return $slots;
    }

    private function formatMinutesToTime(int $minutes): string {
        $normalized = $minutes % (24 * 60);
        $hours = intdiv($normalized, 60);
        $mins = $normalized % 60;
        return sprintf('%02d:%02d', $hours, $mins);
    }

    private function getMentorScheduleConflicts(int $mentorId, ?string $jadwalDetail, ?int $ignoreEnrollmentId = null): array {
        $targetSlots = $this->parseScheduleSlots($jadwalDetail);
        if (empty($targetSlots)) {
            return [];
        }

        $mentorSchedules = DB::table('enrollments')
            ->join('users', 'enrollments.user_id', '=', 'users.id')
            ->where('enrollments.mentor_id', $mentorId)
            ->where('enrollments.status_pembayaran', 'verified')
            ->where(function ($query) {
                $query->where('enrollments.mentor_assignment_status', 'approved')
                    ->orWhereNull('enrollments.mentor_assignment_status');
            })
            ->when($ignoreEnrollmentId, function ($query, $ignoreEnrollmentId) {
                return $query->where('enrollments.id', '!=', $ignoreEnrollmentId);
            })
            ->select('enrollments.id', 'enrollments.jadwal_detail', 'users.name as student_name')
            ->get();

        $conflicts = [];

        foreach ($mentorSchedules as $schedule) {
            $existingSlots = $this->parseScheduleSlots($schedule->jadwal_detail);
            foreach ($targetSlots as $targetSlot) {
                foreach ($existingSlots as $existingSlot) {
                    if ($targetSlot['day'] !== $existingSlot['day']) {
                        continue;
                    }

                    $isOverlap = $targetSlot['start'] < $existingSlot['end'] && $existingSlot['start'] < $targetSlot['end'];
                    if (!$isOverlap) {
                        continue;
                    }

                    $key = $schedule->id . '-' . $targetSlot['day'];
                    if (!isset($conflicts[$key])) {
                        $conflicts[$key] = [
                            'enrollment_id' => (int) $schedule->id,
                            'student_name' => $schedule->student_name,
                            'day' => $targetSlot['day'],
                            'time_range' => $this->formatMinutesToTime($existingSlot['start']) . '-' . $this->formatMinutesToTime($existingSlot['end']),
                        ];
                    }
                }
            }
        }

        return array_values($conflicts);
    }

    public function storeMessage(Request $request) {
        $request->validate(['name' => 'required', 'whatsapp' => 'required', 'message' => 'required']);
        DB::table('messages')->insert([
            'name' => $request->name, 'whatsapp' => $request->whatsapp, 'message' => $request->message,
            'created_at' => now(),
        ]);
        return back()->with('success', 'Pesan dikirim!');
    }
}
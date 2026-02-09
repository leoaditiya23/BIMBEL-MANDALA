<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Carbon\Carbon;

class PageController extends Controller
{
    /**
     * ==========================================
     * 1. HALAMAN PUBLIK
     * ==========================================
     */
    public function index() { return view('pages.home'); }
    public function faq() { return view('pages.faq'); }
    public function about() { return view('pages.about'); }
    public function contact() { return view('pages.contact'); }

    public function reguler(Request $request) {
        $programs = DB::table('programs')->where('type', 'reguler')->get();
        $step = $request->query('step', 1);
        return view('pages.program.reguler', compact('programs', 'step'));
    }

    public function intensif(Request $request) {
        $programs = DB::table('programs')->where('type', 'intensif')->get();
        $step = $request->query('step', 1);
        return view('pages.program.intensif', compact('programs', 'step'));
    }

    /**
     * ==========================================
     * 2. AUTENTIKASI & REDIRECT LOGIC
     * ==========================================
     */
    public function login() { return view('pages.login'); }
    public function register() { return view('pages.register'); }

    /**
     * LOGIKA REGISTRASI KHUSUS SISWA (REVISI BARU)
     * Mengelola pendaftaran dari halaman register.blade.php
     */
    public function registerStore(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'birth_date' => 'required|date',
            'gender' => 'required',
            'phone' => 'required',
            'school' => 'required',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'siswa', // Hardcoded sebagai siswa sesuai permintaan
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'whatsapp' => $request->phone,
            'school' => $request->school,
            'referral' => $request->referral,
        ]);

        return redirect()->route('login')->with('success', 'Pendaftaran berhasil! Silakan masuk dengan akun Anda.');
    }

    public function authenticate(Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            if (session()->has('url.intended')) {
                return redirect()->to(session()->pull('url.intended'));
            }
            return redirect()->intended(route('dashboard'));
        }
        return back()->withErrors(['loginError' => 'Email atau Password salah!'])->onlyInput('email');
    }

    public function pendaftaranLanjut(Request $request) {
        $type = $request->query('type', 'intensif');
        $targetUrl = ($type === 'reguler') 
            ? route('program.reguler', ['step' => 3]) 
            : route('program.intensif', ['step' => 3]);
        session(['url.intended' => $targetUrl]);
        return redirect()->route('login');
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    /**
     * ==========================================
     * 3. LOGIKA DASHBOARD (REDIRECTOR)
     * ==========================================
     */
    public function dashboard() {
        if (!Auth::check()) return redirect()->route('login');
        $user = Auth::user();
        if ($user->role === 'admin') return redirect()->route('admin.overview');
        if ($user->role === 'mentor') return redirect()->route('mentor.overview');
        return redirect()->route('siswa.overview');
    }

    /**
     * ==========================================
     * 4. FITUR ADMIN (VIEWS & CRUD)
     * ==========================================
     */
    public function adminOverview() {
    // Menghitung siswa yang benar-benar sudah membayar (Verified)
    $totalSiswaVerified = DB::table('enrollments')
        ->where('status_pembayaran', 'verified')
        ->distinct('user_id') // Agar 1 siswa daftar 2 program tetap dihitung 1 orang
        ->count();

    $stats = [
        'total_pendapatan' => DB::table('enrollments')->where('status_pembayaran', 'verified')->sum('total_harga'),
        'total_siswa'     => $totalSiswaVerified, 
        'total_mentor'    => User::where('role', 'mentor')->count(),
        'total_program'   => DB::table('programs')->count(),
    ];

    // Mengambil riwayat pendaftaran terbaru
    $recent_enrollments = DB::table('enrollments')
        ->join('users', 'enrollments.user_id', '=', 'users.id')
        ->join('programs', 'enrollments.program_id', '=', 'programs.id')
        ->select('enrollments.*', 'users.name as user_name', 'programs.name as program_name')
        ->orderBy('enrollments.created_at', 'desc')
        ->limit(5)
        ->get();

    return view('admin.overview', compact('stats', 'recent_enrollments'));
}

    public function adminPrograms() {
        $programs = DB::table('programs')
            ->leftJoin('users', 'programs.mentor_id', '=', 'users.id')
            ->select('programs.*', 'users.name as mentor_name')
            ->get()
            ->map(function($program) {
                $program->jumlah_peserta = DB::table('enrollments')
                    ->where('program_id', $program->id)
                    ->where('status_pembayaran', 'verified')
                    ->count();
                return $program;
            });

        $mentors = User::where('role', 'mentor')->get();
        return view('admin.programs', compact('programs', 'mentors'));
    }

    public function storeProgram(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'jenjang' => 'required',
            'type' => 'required',
            'price' => 'required|numeric',
            'mentor_id' => 'nullable|exists:users,id',
        ]);

        DB::table('programs')->insert([
            'name' => $request->name,
            'jenjang' => $request->jenjang,
            'type' => $request->type,
            'price' => $request->price,
            'mentor_id' => $request->mentor_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Program berhasil ditambahkan!');
    }

    public function updateProgram(Request $request, $id) {
    $request->validate([
        'name' => 'required|string|max:255',
        'harga' => 'required|numeric', // Ganti price ke harga jika itu nama kolomnya
    ]);

    DB::table('programs')->where('id', $id)->update([
        'name' => $request->name,
        'jenjang' => $request->jenjang,
        'type' => $request->type,
        'harga' => $request->harga, // Sesuaikan di sini
        'mentor_id' => $request->mentor_id,
        'updated_at' => now(),
    ]);

    return back()->with('success', 'Program berhasil diperbarui!');
}

    public function deleteProgram($id) {
        $hasSiswa = DB::table('enrollments')->where('program_id', $id)->exists();
        if ($hasSiswa) {
            return back()->with('error', 'Gagal menghapus! Program ini memiliki siswa aktif.');
        }

        DB::table('programs')->where('id', $id)->delete();
        return back()->with('success', 'Program berhasil dihapus.');
    }

    public function adminMentors() {
    // Mengambil user dengan role mentor
    $mentors = User::where('role', 'mentor')->orderBy('created_at', 'desc')->get();
    return view('admin.mentors', compact('mentors'));
}

public function storeMentor(Request $request) {
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:8',
        'specialization' => 'nullable|string',
        'whatsapp' => 'nullable|string',
    ]);

    User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => 'mentor',
        'specialization' => $request->specialization,
        'whatsapp' => $request->whatsapp,
    ]);

    return redirect()->back()->with('success', 'Mentor baru berhasil didaftarkan!');
}

public function updateMentor(Request $request, $id) {
    $mentor = User::findOrFail($id);
    
    $mentor->update([
        'name' => $request->name,
        'specialization' => $request->specialization,
    ]);

    return redirect()->back()->with('success', 'Profil mentor berhasil diperbarui!');
}

    public function storeRegister(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'nullable|string',
            'specialization' => 'nullable|string|max:255',
            'whatsapp' => 'nullable|string|max:20',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'mentor',
            'specialization' => $request->specialization,
            'whatsapp' => $request->whatsapp,
        ]);

        return back()->with('success', 'Mentor berhasil didaftarkan!');
    }

    public function adminPayments() {
    $payments = DB::table('enrollments')
        ->join('users', 'enrollments.user_id', '=', 'users.id')
        ->join('programs', 'enrollments.program_id', '=', 'programs.id')
        ->select(
            'enrollments.*', 
            'users.name as user_name',   // Sesuai dengan $payment->user_name di view
            'users.whatsapp as user_wa', // Sesuai dengan $payment->user_wa di view
            'programs.name as program_name'
        )
        ->where('enrollments.status_pembayaran', 'pending') // Hanya tampilkan yang butuh verifikasi
        ->orderBy('enrollments.created_at', 'desc')
        ->get();

    return view('admin.payments', compact('payments'));
}

    public function verifyEnrollment($id) {
    // 1. Update status pembayaran
    DB::table('enrollments')->where('id', $id)->update([
        'status_pembayaran' => 'verified',
        'updated_at' => now()
    ]);

    // 2. Opsional: Kamu bisa kirim notifikasi otomatis di sini jika mau
    
    return redirect()->route('admin.payments')->with('success', 'Pembayaran berhasil diverifikasi! Siswa sekarang sudah aktif.');
}

    public function rejectPayment($id) {
    $enrollment = DB::table('enrollments')->where('id', $id)->first();
    
    if (!$enrollment) {
        return back()->with('error', 'Data tidak ditemukan.');
    }

    // Hapus file bukti pembayaran dari folder public
    if ($enrollment->bukti_pembayaran) {
        $filePath = public_path('uploads/bukti/' . $enrollment->bukti_pembayaran);
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    DB::table('enrollments')->where('id', $id)->delete();
    
    return back()->with('success', 'Pendaftaran ditolak dan data telah dihapus.');
}

    public function adminMessages() {
        $messages = DB::table('messages')
            ->orderBy('created_at', 'desc')
            ->get()
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

    public function adminSettings() { return view('admin.settings'); }

    /**
     * ==========================================
     * 5. FITUR SISWA
     * ==========================================
     */
    public function siswaOverview() {
        $user = Auth::user();

        // 1. Ambil Program Aktif (Hanya yang sudah diverifikasi admin)
        $recent_programs = DB::table('enrollments')
            ->join('programs', 'enrollments.program_id', '=', 'programs.id')
            ->leftJoin('users as mentors', 'programs.mentor_id', '=', 'mentors.id')
            ->where('enrollments.user_id', $user->id)
            ->where('enrollments.status_pembayaran', 'verified')
            ->select('programs.*', 'mentors.name as mentor_name')
            ->limit(5)
            ->get();

        // 2. Kalkulasi Stats Otomatis (Dengan proteksi Schema agar tidak error)
        $stats = [
            'attendance' => \Illuminate\Support\Facades\Schema::hasTable('attendances') 
                ? (DB::table('attendances')->where('user_id', $user->id)->avg('status') ?? 0) 
                : 0,
            'completed_tasks' => \Illuminate\Support\Facades\Schema::hasTable('task_submissions') 
                ? DB::table('task_submissions')->where('user_id', $user->id)->count() 
                : 0,
            'total_tasks' => \Illuminate\Support\Facades\Schema::hasTable('assignments') 
                ? DB::table('assignments')->count() 
                : 0,
            'average_score' => \Illuminate\Support\Facades\Schema::hasTable('grades') 
                ? (DB::table('grades')->where('student_id', $user->id)->avg('score') ?? 0) 
                : 0,
            'class_rank' => "New", 
        ];

        // 3. Ambil Aktivitas Terbaru (Data asli dari database)
        $activities = DB::table('enrollments')
            ->join('programs', 'enrollments.program_id', '=', 'programs.id')
            ->where('enrollments.user_id', $user->id)
            ->select('programs.name as title', 'enrollments.created_at', DB::raw("'Pendaftaran Program' as type"), 'enrollments.status_pembayaran as status')
            ->orderBy('enrollments.created_at', 'desc')
            ->limit(5)
            ->get();

        // Pastikan ketiga variabel ini dikirim ke view
        return view('siswa.overview', compact('recent_programs', 'stats', 'activities'));
    }
    public function siswaPrograms() {
        $user = Auth::user();

        // Mengambil daftar semua program yang pernah didaftarkan siswa (baik verified maupun pending)
        $my_programs = DB::table('enrollments')
            ->join('programs', 'enrollments.program_id', '=', 'programs.id')
            ->leftJoin('users as mentors', 'programs.mentor_id', '=', 'mentors.id')
            ->where('enrollments.user_id', $user->id)
            ->select(
                'programs.*', 
                'mentors.name as mentor_name', 
                'enrollments.status_pembayaran', 
                'enrollments.created_at as registration_date'
            )
            ->get();

        return view('siswa.programs', [
    'programs' => $my_programs 
]);
    } 
    public function siswaSchedule() {
        $user = Auth::user();

        // Mengambil jadwal berdasarkan program yang sudah diikuti (enroll) dan diverifikasi
        $schedules = DB::table('enrollments')
            ->join('programs', 'enrollments.program_id', '=', 'programs.id')
            ->where('enrollments.user_id', $user->id)
            ->where('enrollments.status_pembayaran', 'verified')
            ->select(
                'programs.name as program_name',
                'programs.hari',       // Pastikan ada kolom 'hari' di tabel programs
                'programs.jam_mulai',  // Pastikan ada kolom 'jam_mulai'
                'programs.jam_selesai' // Pastikan ada kolom 'jam_selesai'
            )
            ->get();

        return view('siswa.schedule', compact('schedules'));
    }
public function siswaBilling() {
    $user = Auth::user();

    $payments = DB::table('enrollments')
        ->join('programs', 'enrollments.program_id', '=', 'programs.id')
        ->where('enrollments.user_id', $user->id)
        ->select(
            'enrollments.*', 
            'programs.name as program_name'
        )
        ->orderBy('enrollments.created_at', 'desc')
        ->get();

    return view('siswa.billing', compact('payments'));
}
    public function enrollProgram(Request $request) {
    $request->validate([
        'program_id'       => 'required',
        'total_harga'      => 'required|numeric',
        'bukti_pembayaran' => 'required|image|max:2048',
    ]);

    $user = Auth::user();
    
    // Ambil 3 digit terakhir nomor WA sebagai kode unik
    // Jika WA kosong, default ke 000 agar tidak error
    $cleanPhone = preg_replace('/[^0-9]/', '', $user->whatsapp ?? '000');
    $uniqueCode = (int) substr($cleanPhone, -3); 
    
    // Total yang harus dibayar (Misal: 1.500.000 + 863 = 1.500.863)
    $finalAmount = $request->total_harga + $uniqueCode;

    // Proses Upload File
    $namaFile = time() . '_user_' . $user->id . '.' . $request->file('bukti_pembayaran')->getClientOriginalExtension();
    $request->file('bukti_pembayaran')->move(public_path('uploads/bukti'), $namaFile);

    // Insert ke Database
    DB::table('enrollments')->insert([
        'user_id'           => $user->id,
        'program_id'        => $request->program_id,
        'total_harga'       => $finalAmount,
        'payment_code'      => $uniqueCode, // REVISI: Tambahkan ini agar tidak error General Error 1364
        'status_pembayaran' => 'pending',
        'bukti_pembayaran'  => $namaFile,
        'created_at'        => now(),
        'updated_at'        => now(),
    ]);

    return redirect()->route('siswa.billing')->with('success', 'Berhasil! Admin akan memverifikasi pembayaran Anda.');
}

   /**
     * ==========================================
     * 6. FITUR MENTOR
     * ==========================================
     */
    public function mentorOverview()
    {
        $user = Auth::user();

        $today_schedule = DB::table('enrollments')
            ->join('programs', 'enrollments.program_id', '=', 'programs.id')
            ->join('users', 'enrollments.user_id', '=', 'users.id')
            ->where('programs.mentor_id', $user->id)
            ->where('enrollments.status_pembayaran', 'verified')
            ->whereDate('enrollments.created_at', Carbon::today())
            ->select('enrollments.*', 'programs.name as program_name', 'users.name as student_name')
            ->get()
            ->map(function($item) {
                $item->created_at = Carbon::parse($item->created_at);
                return $item;
            });

        $stats = [
            'total_siswa' => DB::table('enrollments')
                ->join('programs', 'enrollments.program_id', '=', 'programs.id')
                ->where('programs.mentor_id', $user->id)
                ->where('enrollments.status_pembayaran', 'verified')
                ->distinct('enrollments.user_id')
                ->count(),
            'total_kelas' => DB::table('programs')
                ->where('mentor_id', $user->id)
                ->count(),
        ];

        $assignments = DB::table('assignments')
            ->where('mentor_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($item) {
                $item->created_at = Carbon::parse($item->created_at);
                return $item;
            });

        return view('mentor.overview', compact('user', 'today_schedule', 'stats', 'assignments'));
    }

    public function storeAssignment(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'link' => 'nullable|url',
            'file' => 'nullable|mimes:pdf|max:2048', 
        ]);

        $fileName = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_tugas_' . Auth::id() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('assignments', $fileName, 'public');
            $fileName = 'assignments/' . $fileName; 
        }

        DB::table('assignments')->insert([
            'mentor_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'link' => $request->link,
            'file_path' => $fileName,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Tugas berhasil dipublikasikan!');
    }

    public function deleteAssignment($id)
    {
        $assignment = DB::table('assignments')->where('id', $id)->where('mentor_id', Auth::id())->first();
        if ($assignment) {
            if ($assignment->file_path) {
                Storage::disk('public')->delete($assignment->file_path);
            }
            DB::table('assignments')->where('id', $id)->delete();
            return back()->with('success', 'Tugas berhasil dihapus!');
        }
        return back()->with('error', 'Tugas tidak ditemukan.');
    }

    public function mentorClasses() 
    {
        $classes = DB::table('programs')
            ->where('mentor_id', Auth::id())
            ->get()
            ->map(function($class) {
                $class->student_count = DB::table('enrollments')
                    ->where('program_id', $class->id)
                    ->where('status_pembayaran', 'verified')
                    ->count();
                
                $class->students = DB::table('enrollments')
                    ->join('users', 'enrollments.user_id', '=', 'users.id')
                    ->where('enrollments.program_id', $class->id)
                    ->where('enrollments.status_pembayaran', 'verified')
                    ->select('users.id', 'users.name')
                    ->get();

                $class->materials = DB::table('session_materials')
                    ->where('program_id', $class->id)
                    ->orderBy('session_number', 'asc')
                    ->get();

                return $class;
            });
            
        return view('mentor.classes', compact('classes'));
    }

    // --- HELPER UNTUK YOUTUBE EMBED ---
    private function convertYoutube($url) {
        if (!$url) return null;
        $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i';
        if (preg_match($pattern, $url, $matches)) {
            return "https://www.youtube.com/embed/" . $matches[1];
        }
        return $url;
    }

    public function storeMaterial(Request $request)
    {
        $request->validate([
            'program_id' => 'required',
            'session_number' => 'required',
            'title' => 'required',
            'file' => 'nullable|mimes:pdf,ppt,pptx|max:5120',
            'video_url' => 'nullable|url'
        ]);

        $path = null;
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('materials', 'public');
        }

        // Konversi link Youtube ke format Embed sebelum simpan
        $embedUrl = $this->convertYoutube($request->video_url);

        DB::table('session_materials')->insert([
            'program_id' => $request->program_id,
            'session_number' => $request->session_number,
            'title' => $request->title,
            'file_path' => $path,
            'video_url' => $embedUrl,
            'created_at' => now(),
        ]);

        return back()->with('success', 'Materi berhasil ditambahkan!');
    }

    public function deleteMaterial($id)
    {
        $material = DB::table('session_materials')->where('id', $id)->first();
        if ($material) {
            if ($material->file_path) {
                Storage::disk('public')->delete($material->file_path);
            }
            DB::table('session_materials')->where('id', $id)->delete();
            return back()->with('success', 'Materi berhasil dihapus!');
        }
        return back()->with('error', 'Materi tidak ditemukan.');
    }

    public function storeGrade(Request $request)
    {
        $request->validate([
            'program_id' => 'required',
            'student_id' => 'required',
            'title' => 'required|string',
            'score' => 'required|integer|min:0|max:100',
        ]);

        DB::table('grades')->insert([
            'program_id' => $request->program_id,
            'student_id' => $request->student_id,
            'mentor_id' => Auth::id(),
            'title' => $request->title,
            'score' => $request->score,
            'mentor_note' => $request->note,
            'created_at' => now(),
        ]);

        return back()->with('success', 'Nilai siswa berhasil disimpan!');
    }

    public function mentorSchedule() 
    {
        $schedule = DB::table('enrollments')
            ->join('programs', 'enrollments.program_id', '=', 'programs.id')
            ->join('users', 'enrollments.user_id', '=', 'users.id')
            ->where('programs.mentor_id', Auth::id())
            ->where('enrollments.status_pembayaran', 'verified')
            ->select('enrollments.*', 'programs.name as program_name', 'users.name as student_name')
            ->get()
            ->map(function($item) {
                $item->created_at = Carbon::parse($item->created_at);
                return $item;
            });

        return view('mentor.schedule', compact('schedule'));
    }

    /**
     * ==========================================
     * 7. KONTAK / PESAN
     * ==========================================
     */
    public function storeMessage(Request $request) {
        $request->validate([
            'name'     => 'required|string|max:255',
            'whatsapp' => 'required',
            'email'    => 'nullable|email',
            'message'  => 'required|string',
        ]);

        DB::table('messages')->insert([
            'name'       => $request->name,
            'whatsapp'   => $request->whatsapp,
            'email'      => $request->email,
            'message'    => $request->message,
            'is_read'    => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Pesan Anda berhasil dikirim!');
    }
}
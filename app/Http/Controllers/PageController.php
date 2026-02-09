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
        $stats = [
            'total_siswa' => User::whereIn('role', ['user', 'siswa'])->count(),
            'total_mentor' => User::where('role', 'mentor')->count(),
            'total_program' => DB::table('programs')->count(),
            'total_pendapatan' => DB::table('enrollments')->where('status_pembayaran', 'verified')->sum('total_harga'),
        ];

        $recent_enrollments = DB::table('enrollments')
            ->join('users', 'enrollments.user_id', '=', 'users.id')
            ->join('programs', 'enrollments.program_id', '=', 'programs.id')
            ->select('enrollments.*', 'users.name as user_name', 'programs.name as program_name')
            ->orderBy('enrollments.created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($item) {
                $item->created_at = Carbon::parse($item->created_at);
                return $item;
            });

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
            'price' => 'required|numeric',
        ]);

        DB::table('programs')->where('id', $id)->update([
            'name' => $request->name,
            'jenjang' => $request->jenjang,
            'type' => $request->type,
            'price' => $request->price,
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
        $mentors = User::where('role', 'mentor')->get()->map(function($mentor) {
            $mentor->program_count = DB::table('programs')->where('mentor_id', $mentor->id)->count();
            return $mentor;
        });
        return view('admin.mentors', compact('mentors'));
    }

    public function updateMentor(Request $request, $id) {
        $request->validate([
            'name' => 'required|string|max:255',
            'specialization' => 'nullable|string|max:255',
        ]);

        User::where('id', $id)->update([
            'name' => $request->name,
            'specialization' => $request->specialization,
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Data mentor berhasil diperbarui!');
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
        ->select('enrollments.*', 'users.name as user_name', 'users.whatsapp as user_wa', 'programs.name as program_name')
        ->where('enrollments.status_pembayaran', 'pending')
        ->get();
    return view('admin.payments', compact('payments'));
}

    public function verifyEnrollment($id) {
        DB::table('enrollments')->where('id', $id)->update([
            'status_pembayaran' => 'verified', 
            'updated_at' => now()
        ]);
        return back()->with('success', 'Pembayaran telah diverifikasi!');
    }

    public function rejectPayment($id) {
        $enrollment = DB::table('enrollments')->where('id', $id)->first();
        if ($enrollment) {
            if ($enrollment->bukti_pembayaran) {
                $filePath = public_path('uploads/bukti/' . $enrollment->bukti_pembayaran);
                if (file_exists($filePath)) { unlink($filePath); }
            }
            DB::table('enrollments')->where('id', $id)->delete();
            return back()->with('error', 'Data pendaftaran telah dihapus.');
        }
        return back()->with('error', 'Data tidak ditemukan.');
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
        $recent_programs = DB::table('programs')
            ->join('enrollments', 'programs.id', '=', 'enrollments.program_id')
            ->where('enrollments.user_id', $user->id)
            ->select('programs.*', 'enrollments.status_pembayaran')->limit(5)->get();
        return view('siswa.overview', compact('recent_programs'));
    }

    public function siswaPrograms() {
        $user = Auth::user();
        $programs = DB::table('programs')
            ->join('enrollments', 'programs.id', '=', 'enrollments.program_id')
            ->where('enrollments.user_id', $user->id)
            ->select('programs.*', 'enrollments.id as enrollment_id', 'enrollments.status_pembayaran', 'enrollments.bukti_pembayaran')->get();
        return view('siswa.programs', compact('programs'));
    }

    public function siswaSchedule() { return view('siswa.schedule', ['schedule' => []]); }

    public function siswaBilling() {
    $user = Auth::user();
    $payments = DB::table('enrollments')
        ->join('programs', 'enrollments.program_id', '=', 'programs.id')
        ->where('enrollments.user_id', $user->id)
        ->select('enrollments.*', 'programs.name as program_name') // Pastikan enrollments.* ikut terambil
        ->get()
        ->map(function($item) {
            $item->created_at = Carbon::parse($item->created_at);
            return $item;
        });
    
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
    $cleanPhone = preg_replace('/[^0-9]/', '', $user->whatsapp);
    $uniqueCode = (int) substr($cleanPhone, -3); 
    
    // Total yang harus dibayar (Misal: 750.000 + 123 = 750.123)
    $finalAmount = $request->total_harga + $uniqueCode;

    $namaFile = time() . '_user_' . $user->id . '.' . $request->file('bukti_pembayaran')->getClientOriginalExtension();
    $request->file('bukti_pembayaran')->move(public_path('uploads/bukti'), $namaFile);

    DB::table('enrollments')->insert([
        'user_id'           => $user->id,
        'program_id'        => $request->program_id,
        'total_harga'       => $finalAmount, // Simpan harga + kode unik
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
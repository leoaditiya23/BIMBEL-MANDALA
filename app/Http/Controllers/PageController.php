<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Mentor; 
use Carbon\Carbon;
use App\Models\Program;

class PageController extends Controller
{
    /**
     * ==========================================
     * 1. HALAMAN PUBLIK
     * ==========================================
     */
    // Contoh di LandingController.php
public function index() {
    // 1. Ambil data Program
    $programs = Program::with('mentor')->get(); 

    // 2. Ambil data FAQ
    $faqs = DB::table('faqs')->orderBy('created_at', 'asc')->get();

    // 3. Ambil data Mentor (TAMBAHKAN BARIS INI)
    // Kita ambil dari model Mentor yang sudah kamu use di atas
    $mentors = Mentor::all();

    // 4. Kirim SEMUA variabel ke view
    return view('pages.home', compact('programs', 'faqs', 'mentors'));
}

    public function faq() { 
        $faqs = DB::table('faqs')->orderBy('created_at', 'asc')->get();
        return view('pages.faq', compact('faqs')); 
    }

    public function about() { return view('pages.about'); }
    public function contact() { return view('pages.contact'); }

   public function reguler(Request $request) {
    // 1. Ambil semua program reguler
    $programs = DB::table('programs')->where('type', 'reguler')->get();
    
    // 2. Ambil semua data mata pelajaran/mapel (Sesuaikan nama tabel Anda, di sini saya asumsikan 'subjects')
    // Jika nama tabel Anda berbeda (misal 'mapels'), silakan ganti 'subjects' di bawah ini
    $subjects = DB::table('subjects')->get(); 

    $programsByJenjang = $programs->keyBy('jenjang')->toArray();
    $step = $request->query('step', 1);

    // 3. Kirim variabel $subjects ke view
    return view('pages.program.reguler', compact('programs', 'programsByJenjang', 'subjects', 'step'));
}

    public function intensif(Request $request) {
        $programs = DB::table('programs')->where('type', 'intensif')->get();
        $programsByName = $programs->keyBy('name')->toArray();
        $step = $request->query('step', 1);
        return view('pages.program.intensif', compact('programs', 'programsByName', 'step'));
    }

    /**
     * ==========================================
     * 2. AUTENTIKASI & REDIRECT LOGIC
     * ==========================================
     */
    public function login() { return view('pages.login'); }
    public function register() { return view('pages.register'); }

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

        User::create([
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

        return redirect()->route('login')->with('success', 'Pendaftaran berhasil! Silakan masuk.');
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

    public function logout(Request $request)
{
    Auth::logout();
    
    // Menghapus semua sesi agar bersih total
    $request->session()->invalidate();
    
    // Membuat token baru agar tidak terkena 419 lagi nanti
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
        
        return match($user->role) {
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
    public function adminOverview() {
    try {
        $stats = [
            'total_pendapatan' => DB::table('enrollments')->where('status_pembayaran', 'verified')->sum('total_harga'),
            'total_siswa'     => DB::table('enrollments')->where('status_pembayaran', 'verified')->distinct('user_id')->count(), 
            'total_mentor'    => User::where('role', 'mentor')->count(),
            'total_program'   => DB::table('programs')->count(),
        ];

        $recent_enrollments = DB::table('enrollments')
            ->join('users', 'enrollments.user_id', '=', 'users.id')
            ->join('programs', 'enrollments.program_id', '=', 'programs.id')
            ->select(
                'enrollments.id',
                'enrollments.user_id',
                'enrollments.metode',         // <--- PASTIKAN INI DIAMBIL
                'enrollments.lokasi_cabang',  // <--- PASTIKAN INI DIAMBIL
                'enrollments.alamat_siswa',   // <--- PASTIKAN INI DIAMBIL
                'enrollments.jadwal_detail',
                'enrollments.per_minggu',
                'enrollments.mapel',
                'enrollments.status_pembayaran',
                'enrollments.created_at',
                'users.name as user_name',
                'programs.jenjang as program_jenjang',
                'programs.name as program_name'
            )
            ->orderBy('enrollments.created_at', 'desc')
            ->paginate(10);

        $mentors = User::where('role', 'mentor')->get();

        return view('admin.overview', compact('stats', 'recent_enrollments', 'mentors'));
        
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
}

    // REVISI: Pastikan fungsi update ini sinkron dengan kolom yang kita gunakan
    public function updateJadwal(Request $request, $id) {
        $request->validate([
            'mentor_id' => 'required|exists:users,id',
            // 'hari' dan 'jam_mulai' dilemahkan validasinya jika kamu hanya pakai satu input jadwal_pertemuan
            'hari' => 'nullable',
            'jam_mulai' => 'nullable',
        ]);

        DB::table('enrollments')->where('id', $id)->update([
            'mentor_id' => $request->mentor_id,
            // Jika kamu menggunakan sistem jadwal gabungan (jadwal_pertemuan)
            'jadwal_pertemuan' => $request->jadwal_pertemuan, 
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Jadwal dan Mentor berhasil ditetapkan!');
    }

    public function adminPayments() {
    $payments = DB::table('enrollments')
        ->join('users', 'enrollments.user_id', '=', 'users.id')
        ->join('programs', 'enrollments.program_id', '=', 'programs.id')
        ->select(
            'enrollments.*', 
            'users.name as user_name', 
            'users.whatsapp as user_wa',
            'enrollments.mapel as program_name' 
        )
        ->where('enrollments.status_pembayaran', 'pending')
        ->orderBy('enrollments.created_at', 'desc')
        ->get();

    return view('admin.payments', compact('payments'));
}

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
            
            $program->mentor = (object) ['name' => $program->mentor_name ?? 'Mentor Belum Ditunjuk'];
            
            return $program;
        });

        $mentors = User::where('role', 'mentor')->get();
        $subjects = DB::table('subjects')->orderBy('name', 'asc')->get();
        
        return view('admin.programs', compact('programs', 'mentors', 'subjects', 'title', 'type'));
    }

    public function updateProgram(Request $request, $id) {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'extra_meeting_price' => 'nullable|numeric|min:0',
            'quran_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'mentor_id' => 'nullable|exists:users,id',
        ]);

        DB::table('programs')->where('id', $id)->update([
            'name' => $request->name,
            'price' => (int) $request->price,
            'extra_meeting_price' => (int) ($request->extra_meeting_price ?? 0),
            'quran_price' => (int) ($request->quran_price ?? 0),
            'description' => $request->description,
            'mentor_id' => $request->mentor_id,
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Data program berhasil diperbarui!');
    }

    public function storeProgram(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'jenjang' => 'required',
            'type' => 'required|in:reguler,intensif',
            'price' => 'required|numeric|min:0',
            'extra_meeting_price' => 'required|numeric|min:0',
            'quran_price' => 'required|numeric|min:0',
            'mentor_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
        ]);

        DB::table('programs')->insert([
            'name' => $request->name,
            'jenjang' => $request->jenjang,
            'type' => $request->type,
            'price' => $request->price,
            'extra_meeting_price' => $request->extra_meeting_price,
            'quran_price' => $request->quran_price,
            'mentor_id' => $request->mentor_id,
            'description' => $request->description,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Program berhasil ditambahkan!');
    }

    public function deleteProgram($id) {
        if (DB::table('enrollments')->where('program_id', $id)->exists()) {
            return back()->with('error', 'Gagal menghapus! Program ini memiliki pendaftar.');
        }

        DB::table('programs')->where('id', $id)->delete();
        return back()->with('success', 'Program berhasil dihapus.');
    }

    public function adminMentors() {
        $users = User::where('role', 'mentor')->get();

        foreach ($users as $user) {
            $exists = Mentor::where('user_id', $user->id)->exists();

            if (!$exists) {
                Mentor::create([
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'specialist' => $user->specialization ?? 'Umum',
                    'whatsapp' => $user->whatsapp ?? '08123456789',
                    'photo' => null,
                ]);
            }
        }

        $mentors = Mentor::orderBy('created_at', 'desc')->get();
        return view('admin.mentors', compact('mentors'));
    }

    public function storeMentor(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'specialist' => 'required',
            'whatsapp' => 'required',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

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

            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('mentors', 'public');
            }

            Mentor::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'specialist' => $request->specialist,
                'whatsapp' => $request->whatsapp,
                'photo' => $photoPath,
            ]);
        });

        return redirect()->back()->with('success', 'Mentor berhasil ditambahkan!');
    }

    public function updateMentor(Request $request, $id) {
        $mentor = Mentor::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'specialist' => 'required|string|max:255',
            'whatsapp' => 'required|string|max:20',
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

        return redirect()->back()->with('success', 'Profil dan Akun Mentor berhasil diperbarui!');
    }

    public function deleteMentor($id) {
        $mentor = Mentor::findOrFail($id);
        $userId = $mentor->user_id;

        DB::transaction(function () use ($mentor, $userId) {
            if ($mentor->photo) {
                Storage::disk('public')->delete($mentor->photo);
            }
            $mentor->delete();
            
            if ($userId) {
                User::where('id', $userId)->delete();
            }
        });

        return redirect()->back()->with('success', 'Mentor dan Akun Login berhasil dihapus!');
    }

    public function verifyEnrollment($id) {
        DB::table('enrollments')->where('id', $id)->update([
            'status_pembayaran' => 'verified',
            'updated_at' => now()
        ]);
        return back()->with('success', 'Pembayaran berhasil diverifikasi!');
    }

    public function rejectPayment($id) {
        $enrollment = DB::table('enrollments')->where('id', $id)->first();
        if ($enrollment && $enrollment->bukti_pembayaran) {
            Storage::disk('public')->delete('bukti/' . $enrollment->bukti_pembayaran);
        }
        DB::table('enrollments')->where('id', $id)->delete();
        return back()->with('success', 'Pendaftaran ditolak.');
    }

    public function adminMessages() {
        $messages = DB::table('messages')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($msg) {
                $msg->created_at = \Illuminate\Support\Carbon::parse($msg->created_at);
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
            return redirect()->route('admin.messages')->with('success', 'Pesan dijawab & dipublish ke FAQ!');
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

    public function adminSubjects() {
        $subjects = DB::table('subjects')->orderBy('jenjang', 'asc')->get();
        return view('admin.subjects', compact('subjects'));
    }

    public function storeSubject(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'jenjang' => 'required'
        ]);
        DB::table('subjects')->insert([
            'name' => strtoupper($request->name),
            'jenjang' => $request->jenjang,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return back()->with('success', 'Mata pelajaran berhasil ditambahkan!');
    }

    public function updateSubject(Request $request, $id) {
        $request->validate([
            'name' => 'required|string|max:255',
            'jenjang' => 'required'
        ]);
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

    public function messageToFaq($id) {
        return redirect()->route('admin.faqs', ['from_msg_id' => $id]);
    }

    public function adminSettings() { return view('admin.settings'); }

/**
     * ==========================================
     * 5. FITUR SISWA
     * ==========================================
     */
    public function siswaOverview() {
        $user = Auth::user();

        // REVISI: Ambil program yang sudah verified dengan detail lokasi & mapel
        $recent_programs = DB::table('enrollments')
            ->join('programs', 'enrollments.program_id', '=', 'programs.id')
            ->leftJoin('users as mentors', 'programs.mentor_id', '=', 'mentors.id')
            ->where('enrollments.user_id', $user->id)
            ->where('enrollments.status_pembayaran', 'verified')
            ->select(
                'programs.*', 
                'mentors.name as mentor_name',
                'enrollments.lokasi_cabang', 
                'enrollments.alamat_siswa',  
                'enrollments.mapel as selected_mapel', // Nama mapel yang dipilih saat daftar
                'enrollments.jadwal_detail as sesi_jadwal' // Jadwal spesifik siswa
            )
            ->get()
            ->map(function($program) use ($user) {
                // Ambil materi per program
                $program->materials = DB::table('program_materials')
                    ->where('program_id', $program->id)
                    ->orderBy('session_number', 'asc')
                    ->get();
                
                // Cek status kehadiran siswa untuk tiap sesi
                foreach($program->materials as $m) {
                    $m->is_attended = DB::table('attendances')
                        ->where('student_id', $user->id)
                        ->where('program_id', $program->id)
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
            ->join('programs', 'enrollments.program_id', '=', 'programs.id')
            ->where('enrollments.user_id', $user->id)
            ->select('programs.name as title', DB::raw("'Pendaftaran Program' as type"), 'enrollments.status_pembayaran as status', 'enrollments.created_at')
            ->orderBy('enrollments.created_at', 'desc')
            ->limit(5)->get();

        $stats = [
            'completed_tasks' => DB::table('task_submissions')->where('user_id', $user->id)->count(),
            'total_tasks'     => DB::table('assignments')->where('student_id', $user->id)->count(),
            'average_score'   => DB::table('task_submissions')->where('user_id', $user->id)->avg('score') ?? 0,
            'attendance'      => DB::table('attendances')->where('student_id', $user->id)->count(),
        ];

        return view('siswa.overview', compact('recent_programs', 'my_assignments', 'activities', 'stats'));
    }

    public function siswaPrograms() {
        $my_programs = DB::table('enrollments')
            ->join('programs', 'enrollments.program_id', '=', 'programs.id')
            ->leftJoin('users as mentors', 'programs.mentor_id', '=', 'mentors.id')
            ->where('enrollments.user_id', Auth::id())
            ->select('programs.*', 'mentors.name as mentor_name', 'enrollments.status_pembayaran', 'enrollments.created_at as registration_date', 'enrollments.mapel', 'enrollments.lokasi_cabang')
            ->get();

        return view('siswa.programs', ['programs' => $my_programs]);
    }

    public function siswaSchedule() {
        // REVISI: Jadwal diambil dari jadwal_detail di enrollment agar akurat sesuai pilihan siswa
        $schedules = DB::table('enrollments')
            ->join('programs', 'enrollments.program_id', '=', 'programs.id')
            ->where('enrollments.user_id', Auth::id())
            ->where('enrollments.status_pembayaran', 'verified')
            ->select('enrollments.mapel as program_name', 'enrollments.jadwal_detail', 'enrollments.lokasi_cabang')
            ->get();

        return view('siswa.schedule', compact('schedules'));
    }

    public function siswaBilling() {
        // REVISI: Menampilkan data pembayaran yang real dari tabel enrollments
        $payments = DB::table('enrollments')
            ->join('programs', 'enrollments.program_id', '=', 'programs.id')
            ->where('enrollments.user_id', Auth::id())
            ->select(
                'enrollments.*', 
                'programs.name as base_program',
                'enrollments.mapel as nama_program', // Ini yang berisi "Matematika", dll
                'enrollments.lokasi_cabang',
                'enrollments.jadwal_detail'
            )
            ->orderBy('enrollments.created_at', 'desc')
            ->get();

        return view('siswa.billing', compact('payments'));
    }

    public function submitTask(Request $request) {
        $request->validate([
            'material_id' => 'required',
            'link' => 'required|url'
        ]);

        DB::table('task_submissions')->updateOrInsert(
            ['user_id' => Auth::id(), 'material_id' => $request->material_id],
            [
                'task_link' => $request->link,
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        return response()->json(['success' => true, 'message' => 'Tugas berhasil dikirim!']);
    }

    public function siswaAbsen(Request $request) {
        $request->validate(['material_id' => 'required']);
        
        $material = DB::table('program_materials')->where('id', $request->material_id)->first();
        if(!$material) return response()->json(['success' => false, 'message' => 'Sesi tidak ditemukan'], 404);

        $program = DB::table('programs')->where('id', $material->program_id)->first();
        if (!$program->is_absen_active) {
            return response()->json(['success' => false, 'message' => 'Absensi belum dibuka oleh mentor.'], 403);
        }

        $alreadyAttended = DB::table('attendances')
            ->where('student_id', Auth::id())
            ->where('program_id', $material->program_id)
            ->where('date', date('Y-m-d'))
            ->exists();

        if ($alreadyAttended) {
            return response()->json(['success' => false, 'message' => 'Anda sudah melakukan absensi hari ini.'], 400);
        }

        DB::table('attendances')->insert([
            'student_id' => Auth::id(),
            'program_id' => $material->program_id,
            'date' => date('Y-m-d'),
            'status' => 'Hadir',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Berhasil absen!']);
    }

    public function enrollProgram(Request $request) {
    // 1. Validasi (Pastikan lokasi_cabang masuk dalam list validasi)
    $request->validate([
        'program_id' => 'required|exists:programs,id',
        'per_minggu' => 'required',
        'jadwal_detail' => 'required',
        'extra_hours' => 'nullable|numeric',
        'bukti_pembayaran' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        'total_harga' => 'required',
        'lokasi_cabang' => 'nullable|string', // Pastikan ini ada
        'alamat_siswa' => 'nullable|string',  // Pastikan ini ada
        'selected_subjects' => 'nullable' 
    ]);

    $user = Auth::user();
    
    // Logic Kode Unik
    $cleanPhone = preg_replace('/[^0-9]/', '', $user->whatsapp ?? '000');
    $uniqueCode = (int) substr($cleanPhone, -3); 
    $finalAmount = (int)$request->total_harga + $uniqueCode;

    $path = $request->file('bukti_pembayaran')->store('bukti', 'public');
    $namaFile = basename($path);

    $subjects = json_decode($request->selected_subjects, true);
    $mapelName = is_array($subjects) ? implode(', ', $subjects) : 'Program Bimbel';

    // 2. SIMPAN KE DATABASE
    // Di dalam fungsi enrollProgram
DB::table('enrollments')->insert([
    'user_id' => $user->id,
    'program_id' => $request->program_id,
    'per_minggu' => $request->per_minggu,
    'jadwal_detail' => $request->jadwal_detail,
    'mapel' => $mapelName, 
    
    // CARA PASTI: Ambil dari input, kalau kosong cek lokasi_cabang
    // Jika lokasi_cabang ada isinya, berarti dia OFFLINE.
    'metode' => $request->metode ?? ($request->lokasi_cabang ? 'offline' : 'online'),
    
    'lokasi_cabang' => $request->lokasi_cabang, 
    'alamat_siswa' => $request->alamat_siswa, 
    'total_harga' => $finalAmount,
    'status_pembayaran' => 'pending',
    'bukti_pembayaran' => 'bukti/' . $namaFile,
    'created_at' => now(),
    'updated_at' => now(),
]);

    return redirect()->route('siswa.billing')->with('success', 'Berhasil! Menunggu verifikasi.');
}
    
/**
     * ==========================================
     * FITUR MENTOR (OTOMATIS BERDASARKAN ADMIN)
     * ==========================================
     */
   public function mentorOverview() {
    $user = Auth::user();
    $hari_ini = Carbon::now()->locale('id')->dayName;

    $today_schedule = DB::table('enrollments')
        ->join('users', 'enrollments.user_id', '=', 'users.id') // Ambil data siswa
        ->where('enrollments.mentor_id', $user->id)
        ->where('enrollments.status_pembayaran', 'verified')
        ->where('enrollments.hari', 'like', '%' . $hari_ini . '%')
        ->select(
            'enrollments.id', 
            'enrollments.mapel as program_name', 
            'enrollments.jam_mulai', 
            'users.name as student_name', // NAMA SISWA MUNCUL DI SINI
            'enrollments.lokasi_cabang'
        )
        ->get()
            ->map(function($item) {
                $item->jam_tampil = $item->jam_mulai ? Carbon::parse($item->jam_mulai)->format('H:i') : '--:--';
                return $item;
            });

        $stats = [
            'total_siswa' => DB::table('enrollments')
                ->where('mentor_id', $user->id)
                ->where('status_pembayaran', 'verified')
                ->distinct('user_id')->count(),
            'total_kelas' => DB::table('enrollments')
                ->where('mentor_id', $user->id)
                ->where('status_pembayaran', 'verified')
                ->distinct('mapel')->count(), // Hitung berdasarkan kolom mapel
        ];

        return view('mentor.overview', compact('user', 'today_schedule', 'stats'));
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

  public function mentorClasses() {
        $user = Auth::user();
        $today = date('Y-m-d');

        // REVISI: Mengambil data enrollment secara individu agar 1 pendaftaran = 1 kartu
        $classes = DB::table('enrollments')
            ->join('users', 'enrollments.user_id', '=', 'users.id') 
            ->where('enrollments.mentor_id', $user->id)
            ->where('enrollments.status_pembayaran', 'verified')
            ->select(
                'enrollments.id', 
                'enrollments.mapel as name', 
                'enrollments.jenjang', 
                'enrollments.hari',
                'enrollments.user_id',
                'users.name as student_name' // Nama siswa ditarik ke level utama
            )
            ->get()
            ->map(function($class) use ($today, $user) {
                $class->type = 'Reguler'; 
                $class->total_sessions = 12;

                // Mengambil data siswa untuk modal absensi/nilai (Tetap dalam bentuk array agar Alpine.js tidak error)
                $class->students = DB::table('users')
                    ->leftJoin('attendances', function($join) use ($today, $class) {
                        $join->on('users.id', '=', 'attendances.student_id')
                             ->where('attendances.program_id', '=', $class->id)
                             ->where('attendances.date', '=', $today);
                    })
                    ->where('users.id', $class->user_id)
                    ->select('users.id', 'users.name', 'attendances.status as status')
                    ->get();
                
                // Indikator jumlah siswa di kartu tersebut
                $class->student_count = 1; 
                
                // Mengambil materi berdasarkan ID enrollment (program_id)
                $class->materials = DB::table('program_materials')
                    ->where('program_id', $class->id)
                    ->orderBy('session_number', 'asc')
                    ->get();

                return $class;
            });

        return view('mentor.classes', compact('classes'));
    }

    public function toggleAbsen(Request $request) {
        // Cek dulu apakah kolom exists untuk menghindari error SQL
        $request->validate(['class_id' => 'required', 'is_active' => 'required|boolean']);
        
        try {
            DB::table('programs')->where('id', $request->class_id)->update([
                'is_absen_active' => $request->is_active,
                'updated_at' => now()
            ]);
            return response()->json(['success' => true, 'message' => 'Status absensi diperbarui']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui (Fitur butuh update database)']);
        }
    }

    public function storeMaterial(Request $request) {
        $request->validate([
            'program_id' => 'required|exists:programs,id',
            'session_number' => 'required|integer',
            'title' => 'required|string|max:255',
            'file' => 'nullable|file|max:10240',
        ]);

        $filePath = $request->hasFile('file') ? $request->file('file')->store('materials', 'public') : null;

        DB::table('program_materials')->insert([
            'program_id' => $request->program_id,
            'session_number' => $request->session_number,
            'title' => $request->title,
            'video_url' => $request->video_url,
            'file_path' => $filePath,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Materi diunggah!');
    }

    public function updateMaterial(Request $request, $id) {
        $request->validate([
            'session_number' => 'required|integer',
            'title' => 'required|string|max:255',
        ]);

        $material = DB::table('program_materials')->where('id', $id)->first();
        if (!$material) return back()->with('error', 'Materi tidak ditemukan.');

        $data = [
            'session_number' => $request->session_number,
            'title' => $request->title,
            'video_url' => $request->video_url,
            'updated_at' => now(),
        ];

        if ($request->hasFile('file')) {
            if ($material->file_path) Storage::disk('public')->delete($material->file_path);
            $data['file_path'] = $request->file('file')->store('materials', 'public');
        }

        DB::table('program_materials')->where('id', $id)->update($data);
        return back()->with('success', 'Materi diperbarui!');
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

        DB::table('grades')->insert([
            'program_id' => $request->program_id,
            'student_id' => $request->student_id,
            'title' => $request->title,
            'score' => $request->score,
            'note' => $request->note,
            'created_at' => now(),
        ]);

        DB::table('activities')->insert([
            'user_id' => $request->student_id,
            'title' => 'Nilai Baru: ' . $request->title . ' (' . $request->score . ')',
            'type' => 'grade',
            'status' => 'verified',
            'created_at' => now(),
        ]);

        return back()->with('success', 'Nilai dikirim!');
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
        $schedule = DB::table('enrollments')
            ->join('programs', 'enrollments.program_id', '=', 'programs.id')
            ->join('users', 'enrollments.user_id', '=', 'users.id')
            ->where('enrollments.mentor_id', Auth::id())
            ->where('enrollments.status_pembayaran', 'verified')
            ->select('programs.name as program_name', 'programs.hari', 'programs.jam_mulai', 'users.name as student_name')
            ->get()
            ->map(function($item) {
                $item->jam_mulai = $item->jam_mulai ? Carbon::parse($item->jam_mulai)->format('H:i') : '--:--';
                return $item;
            });

        return view('mentor.schedule', compact('schedule'));
    }

    public function mentorSubmissions() {
        $submissions = DB::table('task_submissions')
            ->join('program_materials', 'task_submissions.material_id', '=', 'program_materials.id')
            ->join('users', 'task_submissions.user_id', '=', 'users.id')
            ->join('enrollments', 'users.id', '=', 'enrollments.user_id')
            ->where('enrollments.mentor_id', Auth::id())
            ->select('task_submissions.*', 'users.name as student_name', 'program_materials.title as session_title')
            ->orderBy('task_submissions.created_at', 'desc')
            ->get();

        return view('mentor.submissions', compact('submissions'));
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
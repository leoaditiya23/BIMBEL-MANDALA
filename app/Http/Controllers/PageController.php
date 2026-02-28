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

class PageController extends Controller
{
    /**
     * ==========================================
     * 1. HALAMAN PUBLIK
     * ==========================================
     */
    public function index() { 
        $mentors = Mentor::all(); 
        return view('pages.home', compact('mentors')); 
    }

    // REVISI: Mengambil data FAQ dari database agar tidak hardcoded
    public function faq() { 
        $faqs = DB::table('faqs')->orderBy('created_at', 'asc')->get();
        return view('pages.faq', compact('faqs')); 
    }

    public function about() { return view('pages.about'); }
    public function contact() { return view('pages.contact'); }

    public function reguler(Request $request) {
        $programs = DB::table('programs')->where('type', 'reguler')->get();
        $programsByJenjang = $programs->keyBy('jenjang')->toArray();
        $step = $request->query('step', 1);
        return view('pages.program.reguler', compact('programs', 'programsByJenjang', 'step'));
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
            'gender' => 'required',
            'phone' => 'required',
            'school' => 'required',
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
        
        return match($user->role) {
            'admin' => redirect()->route('admin.overview'),
            'mentor' => redirect()->route('mentor.overview'),
            default => redirect()->route('siswa.overview'),
        };
    }

    /**
     * ==========================================
     * 4. FITUR ADMIN (VIEWS & CRUD) - REVISED
     * ==========================================
     */
    public function adminOverview() {
        $stats = [
            'total_pendapatan' => DB::table('enrollments')->where('status_pembayaran', 'verified')->sum('total_harga'),
            'total_siswa'     => DB::table('enrollments')->where('status_pembayaran', 'verified')->distinct('user_id')->count(), 
            'total_mentor'    => User::where('role', 'mentor')->count(),
            'total_program'   => DB::table('programs')->count(),
        ];

        $recent_enrollments = DB::table('enrollments')
            ->join('users', 'enrollments.user_id', '=', 'users.id')
            ->join('programs', 'enrollments.program_id', '=', 'programs.id')
            ->select('enrollments.*', 'users.name as user_name', 'programs.name as program_name', 'programs.jenjang as program_jenjang')
            ->orderBy('enrollments.created_at', 'desc')
            ->paginate(10);

        return view('admin.overview', compact('stats', 'recent_enrollments'));
    }

    // Fungsi Terpadu untuk Manajemen Program (Menggantikan 3 fungsi sebelumnya)
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
            return $program;
        });

        $mentors = User::where('role', 'mentor')->get();
        return view('admin.programs', compact('programs', 'mentors', 'title', 'type'));
    }

    public function updateProgramPrice(Request $request) {
        $request->validate([
            'id' => 'required',
            'price' => 'required|numeric',
            'quran_price' => 'nullable|numeric'
        ]);

        $updated = DB::table('programs')
            ->where('id', $request->id)
            ->update([
                'price' => (int) $request->price,
                'quran_price' => (int) ($request->quran_price ?? 0),
                'updated_at' => now(),
            ]);

        if ($updated) {
            return back()->with('success', 'Harga berhasil diperbarui ke Rp ' . number_format($request->price));
        }
        return back()->with('info', 'Tidak ada perubahan data.');
    }

    public function storeProgram(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'jenjang' => 'required',
            'type' => 'required',
            'price' => 'required|numeric',
            'extra_meeting_price' => 'required|numeric',
            'quran_price' => 'required|numeric',
            'mentor_id' => 'nullable|exists:users,id',
        ]);

        DB::table('programs')->insert([
            'name' => $request->name,
            'jenjang' => $request->jenjang,
            'type' => $request->type,
            'price' => $request->price,
            'extra_meeting_price' => $request->extra_meeting_price,
            'quran_price' => $request->quran_price,
            'mentor_id' => $request->mentor_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Program berhasil ditambahkan!');
    }

    public function updateProgram(Request $request, $id) {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'extra_meeting_price' => 'nullable|numeric',
            'quran_price' => 'nullable|numeric',
        ]);

        DB::table('programs')
            ->where('id', $id)
            ->update([
                'name' => $request->name,
                'price' => (int) $request->price,
                'extra_meeting_price' => (int) ($request->extra_meeting_price ?? 0),
                'quran_price' => (int) ($request->quran_price ?? 0),
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Data program berhasil diperbarui!');
    }

    public function deleteProgram($id) {
        $hasSiswa = DB::table('enrollments')->where('program_id', $id)->exists();
        if ($hasSiswa) {
            return back()->with('error', 'Gagal menghapus! Program ini memiliki pendaftar.');
        }

        DB::table('programs')->where('id', $id)->delete();
        return back()->with('success', 'Program berhasil dihapus.');
    }

    public function adminMentors() {
        $mentors = Mentor::orderBy('created_at', 'desc')->get();
        return view('admin.mentors', compact('mentors'));
    }

   public function storeMentor(Request $request) {
    $request->validate([
        'name' => 'required|string|max:255',
        'specialist' => 'required|string|max:255',
        'whatsapp' => 'required|string|max:20', 
        'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    $path = $request->hasFile('photo') ? $request->file('photo')->store('mentors', 'public') : null;

    Mentor::create([
        'name' => $request->name,
        'specialist' => $request->specialist,
        'whatsapp' => $request->whatsapp, 
        'photo' => $path,
    ]);

    return redirect()->back()->with('success', 'Mentor berhasil ditambahkan!');
}

    public function updateMentor(Request $request, $id) {
    $mentor = Mentor::findOrFail($id);
    $request->validate([
        'name' => 'required|string|max:255',
        'specialist' => 'required|string|max:255',
        'whatsapp' => 'required|string|max:20', 
        'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    $data = $request->only(['name', 'specialist', 'whatsapp']); 
    
    if ($request->hasFile('photo')) {
        if ($mentor->photo) Storage::disk('public')->delete($mentor->photo);
        $data['photo'] = $request->file('photo')->store('mentors', 'public');
    }

    $mentor->update($data); 
    return redirect()->back()->with('success', 'Profil mentor berhasil diperbarui!');
}

    public function deleteMentor($id) {
        $mentor = Mentor::findOrFail($id);
        if ($mentor->photo) Storage::disk('public')->delete($mentor->photo);
        $mentor->delete();
        return redirect()->back()->with('success', 'Mentor berhasil dihapus!');
    }

    public function adminPayments() {
        $payments = DB::table('enrollments')
            ->join('users', 'enrollments.user_id', '=', 'users.id')
            ->join('programs', 'enrollments.program_id', '=', 'programs.id')
            ->select('enrollments.*', 'users.name as user_name', 'users.whatsapp as user_wa', 'programs.name as program_name')
            ->where('enrollments.status_pembayaran', 'pending')
            ->orderBy('enrollments.created_at', 'desc')
            ->get();

        return view('admin.payments', compact('payments'));
    }

    public function verifyEnrollment($id) {
        DB::table('enrollments')->where('id', $id)->update([
            'status_pembayaran' => 'verified',
            'updated_at' => now()
        ]);
        return redirect()->route('admin.payments')->with('success', 'Pembayaran berhasil diverifikasi!');
    }

    public function rejectPayment($id) {
        $enrollment = DB::table('enrollments')->where('id', $id)->first();
        if ($enrollment && $enrollment->bukti_pembayaran) {
            $filePath = public_path('uploads/bukti/' . $enrollment->bukti_pembayaran);
            if (file_exists($filePath)) unlink($filePath);
        }
        DB::table('enrollments')->where('id', $id)->delete();
        return back()->with('success', 'Pendaftaran ditolak.');
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

    // --- FITUR FAQ (REVISED: MENDUKUNG PRE-FILL & AUTO-DELETE PESAN) ---
    public function adminFaqs(Request $request) {
        $faqs = DB::table('faqs')->orderBy('created_at', 'desc')->get();
        
        // Cek apakah ada data pesan yang dikirim lewat URL (opsional jika modal tidak dipakai)
        $from_message = null;
        if ($request->has('from_msg_id')) {
            $from_message = DB::table('messages')->where('id', $request->from_msg_id)->first();
        }

        return view('admin.faqs', compact('faqs', 'from_message'));
    }

    public function storeFaq(Request $request) {
        $request->validate([
            'question' => 'required',
            'answer' => 'required'
        ]);

        // 1. Simpan ke tabel FAQ
        DB::table('faqs')->insert([
            'question' => $request->question,
            'answer' => $request->answer,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Jika input message_id ada (dikirim dari modal di Inbox), hapus pesan tersebut
        if ($request->filled('message_id')) {
            DB::table('messages')->where('id', $request->message_id)->delete();
            return redirect()->route('admin.messages')->with('success', 'Pesan berhasil dijawab dan dipublish ke FAQ!');
        }

        return redirect()->route('admin.faqs')->with('success', 'FAQ berhasil diterbitkan!');
    }

    // --- REVISI: FUNGSI UPDATE FAQ (BARU) ---
    public function updateFaq(Request $request, $id) {
        $request->validate([
            'question' => 'required',
            'answer' => 'required'
        ]);

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
        // Hanya mengalihkan ke halaman FAQ dengan membawa ID pesan
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

        $recent_programs = DB::table('enrollments')
            ->join('programs', 'enrollments.program_id', '=', 'programs.id')
            ->leftJoin('users as mentors', 'programs.mentor_id', '=', 'mentors.id')
            ->where('enrollments.user_id', $user->id)
            ->where('enrollments.status_pembayaran', 'verified')
            ->select('programs.*', 'mentors.name as mentor_name')
            ->get();

        $my_assignments = DB::table('assignments')
            ->join('users as mentors', 'assignments.mentor_id', '=', 'mentors.id')
            ->where('assignments.student_id', $user->id)
            ->select('assignments.*', 'mentors.name as mentor_name')
            ->orderBy('assignments.created_at', 'desc')
            ->limit(5)
            ->get();

        $activities = DB::table('enrollments')
            ->join('programs', 'enrollments.program_id', '=', 'programs.id')
            ->where('enrollments.user_id', $user->id)
            ->select('programs.name as title', DB::raw("'Pendaftaran Program' as type"), 'enrollments.status_pembayaran as status', 'enrollments.created_at')
            ->orderBy('enrollments.created_at', 'desc')
            ->limit(5)
            ->get();

        $stats = [
            'completed_tasks' => DB::table('task_submissions')->where('user_id', $user->id)->count(),
            'total_tasks' => DB::table('assignments')->where('student_id', $user->id)->count(),
            'average_score' => DB::table('grades')->where('student_id', $user->id)->avg('score') ?? 0,
        ];

        return view('siswa.overview', compact('recent_programs', 'my_assignments', 'activities', 'stats'));
    }

    public function siswaPrograms() {
        $my_programs = DB::table('enrollments')
            ->join('programs', 'enrollments.program_id', '=', 'programs.id')
            ->leftJoin('users as mentors', 'programs.mentor_id', '=', 'mentors.id')
            ->where('enrollments.user_id', Auth::id())
            ->select('programs.*', 'mentors.name as mentor_name', 'enrollments.status_pembayaran', 'enrollments.created_at as registration_date')
            ->get();

        return view('siswa.programs', ['programs' => $my_programs]);
    }

    public function siswaSchedule() {
        $schedules = DB::table('enrollments')
            ->join('programs', 'enrollments.program_id', '=', 'programs.id')
            ->where('enrollments.user_id', Auth::id())
            ->where('enrollments.status_pembayaran', 'verified')
            ->select('programs.name as program_name', 'programs.hari', 'programs.jam_mulai', 'programs.jam_selesai')
            ->get();

        return view('siswa.schedule', compact('schedules'));
    }

    public function siswaBilling() {
        $payments = DB::table('enrollments')
            ->join('programs', 'enrollments.program_id', '=', 'programs.id')
            ->where('enrollments.user_id', Auth::id())
            ->select('enrollments.*', 'programs.name as program_name')
            ->orderBy('enrollments.created_at', 'desc')
            ->get();

        return view('siswa.billing', compact('payments'));
    }

    public function enrollProgram(Request $request) {
        $request->validate([
            'program_id' => 'required|exists:programs,id',
            'bukti_pembayaran' => 'required|image|max:2048',
        ]);

        $program = DB::table('programs')->where('id', $request->program_id)->first();
        $user = Auth::user();
        
        $cleanPhone = preg_replace('/[^0-9]/', '', $user->whatsapp ?? '000');
        $uniqueCode = (int) substr($cleanPhone, -3); 
        $finalAmount = $program->price + $uniqueCode;

        $namaFile = time() . '_user_' . $user->id . '.' . $request->file('bukti_pembayaran')->getClientOriginalExtension();
        $request->file('bukti_pembayaran')->move(public_path('uploads/bukti'), $namaFile);

        DB::table('enrollments')->insert([
            'user_id' => $user->id,
            'program_id' => $request->program_id,
            'total_harga' => $finalAmount,
            'payment_code' => $uniqueCode,
            'status_pembayaran' => 'pending',
            'bukti_pembayaran' => $namaFile,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('siswa.billing')->with('success', 'Berhasil! Menunggu verifikasi.');
    }

    /**
     * ==========================================
     * 6. FITUR MENTOR
     * ==========================================
     */
    public function mentorOverview() {
        $user = Auth::user();
        $daftar_hari = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
        ];
        $hari_ini = $daftar_hari[date('l')];

        $today_schedule = DB::table('enrollments')
            ->join('programs', 'enrollments.program_id', '=', 'programs.id')
            ->join('users', 'enrollments.user_id', '=', 'users.id')
            ->where('programs.mentor_id', $user->id)
            ->where('enrollments.status_pembayaran', 'verified')
            ->where('programs.hari', $hari_ini)
            ->select('enrollments.id', 'programs.name as program_name', 'programs.jam_mulai', 'users.name as student_name', 'users.id as student_id')
            ->get()
            ->map(function($item) {
                $item->jam_tampil = $item->jam_mulai ? Carbon::parse($item->jam_mulai)->format('H:i') : '--:--';
                return $item;
            });

        $stats = [
            'total_siswa' => DB::table('enrollments')
                ->join('programs', 'enrollments.program_id', '=', 'programs.id')
                ->where('programs.mentor_id', $user->id)
                ->where('enrollments.status_pembayaran', 'verified')
                ->distinct('enrollments.user_id')
                ->count(),
            'total_kelas' => DB::table('programs')->where('mentor_id', $user->id)->count(),
        ];

        $assignments = DB::table('assignments')
            ->leftJoin('users', 'assignments.student_id', '=', 'users.id')
            ->where('assignments.mentor_id', $user->id)
            ->select('assignments.*', 'users.name as student_name')
            ->orderBy('assignments.created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($item) {
                $item->created_at = Carbon::parse($item->created_at);
                return $item;
            });

        return view('mentor.overview', compact('user', 'today_schedule', 'stats', 'assignments'));
    }

    public function storeAssignment(Request $request) {
        $request->validate(['student_id' => 'required', 'title' => 'required|string|max:255']);
        $fileName = $request->hasFile('file') ? $request->file('file')->store('assignments', 'public') : null;

        DB::table('assignments')->insert([
            'mentor_id' => Auth::id(),
            'student_id' => $request->student_id,
            'title' => $request->title,
            'description' => $request->description,
            'link' => $request->link,
            'file_path' => $fileName,
            'created_at' => now(),
        ]);
        return back()->with('success', 'Tugas berhasil dikirim!');
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

        $classes = DB::table('programs')->where('mentor_id', $user->id)->get()->map(function($class) use ($today) {
            $class->students = DB::table('enrollments')
                ->join('users', 'enrollments.user_id', '=', 'users.id')
                ->leftJoin('attendances', function($join) use ($today) {
                    $join->on('users.id', '=', 'attendances.student_id')
                         ->where('attendances.date', '=', $today);
                })
                ->where('enrollments.program_id', $class->id)
                ->where('enrollments.status_pembayaran', 'verified')
                ->select('users.id', 'users.name', 'attendances.status as status')
                ->get();
            
            $class->student_count = $class->students->count();
            $class->materials = Schema::hasTable('program_materials') 
                ? DB::table('program_materials')->where('program_id', $class->id)->get() 
                : collect([]);
            
            return $class;
        });
        return view('mentor.classes', compact('classes'));
    }

    public function toggleAbsen(Request $request) {
        $request->validate(['class_id' => 'required', 'is_active' => 'required|boolean']);
        DB::table('programs')->where('id', $request->class_id)->update([
            'is_absen_active' => $request->is_active,
            'updated_at' => now()
        ]);
        return response()->json(['success' => true, 'message' => 'Status absensi diperbarui']);
    }

    public function storeMaterial(Request $request) {
        $request->validate([
            'program_id'     => 'required',
            'session_number' => 'required|integer',
            'title'          => 'required|string|max:255',
            'video_url'      => 'nullable|url',
            'file'           => 'nullable|file|mimes:pdf,ppt,pptx,doc,docx|max:10240',
        ]);

        $filePath = $request->hasFile('file') ? $request->file('file')->store('materials', 'public') : null;

        DB::table('program_materials')->insert([
            'program_id'     => $request->program_id,
            'session_number' => $request->session_number,
            'title'          => $request->title,
            'video_url'      => $request->video_url,
            'file_path'      => $filePath,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        return back()->with('success', 'Materi berhasil diunggah!');
    }

    public function updateMaterial(Request $request, $id) {
        $request->validate([
            'session_number' => 'required|integer',
            'title'          => 'required|string|max:255',
            'video_url'      => 'nullable|url',
            'file'           => 'nullable|file|mimes:pdf,ppt,pptx,doc,docx|max:10240',
        ]);

        $material = DB::table('program_materials')->where('id', $id)->first();
        if (!$material) return back()->with('error', 'Materi tidak ditemukan.');

        $data = [
            'session_number' => $request->session_number,
            'title'          => $request->title,
            'video_url'      => $request->video_url,
            'updated_at'     => now(),
        ];

        if ($request->hasFile('file')) {
            if ($material->file_path) Storage::disk('public')->delete($material->file_path);
            $data['file_path'] = $request->file('file')->store('materials', 'public');
        }

        DB::table('program_materials')->where('id', $id)->update($data);
        return back()->with('success', 'Materi berhasil diperbarui!');
    }

    public function storeAttendance(Request $request) {
        if ($request->has('attendance')) {
            foreach ($request->attendance as $studentId => $status) {
                DB::table('attendances')->updateOrInsert(
                    ['program_id' => $request->program_id, 'student_id' => $studentId, 'date' => $request->date ?? date('Y-m-d')],
                    ['status' => $status, 'updated_at' => now()]
                );
            }
        }
        return back()->with('success', 'Presensi disimpan!');
    }

    public function storeGrade(Request $request) {
        $request->validate([
            'program_id' => 'required',
            'student_id' => 'required',
            'title' => 'required|string',
            'score' => 'required|integer|min:0|max:100',
        ]);

        DB::table('grades')->insert([
            'program_id' => $request->program_id,
            'student_id' => $request->student_id,
            'title' => $request->title,
            'score' => $request->score,
            'note' => $request->note,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
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
        $schedule = DB::table('enrollments')
            ->join('programs', 'enrollments.program_id', '=', 'programs.id')
            ->join('users', 'enrollments.user_id', '=', 'users.id')
            ->where('programs.mentor_id', Auth::id())
            ->where('enrollments.status_pembayaran', 'verified')
            ->select('enrollments.id', 'programs.name as program_name', 'programs.jenjang', 'programs.hari', 'programs.jam_mulai', 'programs.jam_selesai', 'users.name as student_name', 'users.id as student_id', 'enrollments.created_at')
            ->get()
            ->map(function($item) {
                $item->jam_mulai = $item->jam_mulai ? Carbon::parse($item->jam_mulai)->format('H:i') : '--:--';
                return $item;
            });

        return view('mentor.schedule', compact('schedule'));
    }

    public function storeMessage(Request $request) {
        $request->validate(['name' => 'required', 'whatsapp' => 'required', 'message' => 'required']);
        DB::table('messages')->insert([
            'name' => $request->name, 'whatsapp' => $request->whatsapp, 'email' => $request->email,
            'message' => $request->message, 'is_read' => false, 'created_at' => now(), 'updated_at' => now(),
        ]);
        return back()->with('success', 'Pesan dikirim!');
    }
}
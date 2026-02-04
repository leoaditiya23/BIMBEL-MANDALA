<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        $user = Auth::user();
        if ($user->role === 'admin') return redirect()->route('admin.overview');
        if ($user->role === 'mentor') return redirect()->route('mentor.overview');
        return redirect()->route('siswa.overview');
    }

    /**
     * ==========================================
     * 4. FITUR ADMIN (VIEWS)
     * ==========================================
     */
    public function adminOverview() {
        $stats = [
            'total_siswa' => User::where('role', 'siswa')->count(),
            'total_mentor' => User::where('role', 'mentor')->count(),
            'total_program' => DB::table('programs')->count(),
            'total_pendapatan' => DB::table('enrollments')->where('status_pembayaran', 'verified')->sum('total_harga'),
        ];

        $recent_enrollments = DB::table('enrollments')
            ->join('users', 'enrollments.user_id', '=', 'users.id')
            ->leftJoin('programs', 'enrollments.program_id', '=', 'programs.id')
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
            ->select('programs.*', 'users.name as mentor_name')->get();
        $mentors = User::where('role', 'mentor')->get();
        return view('admin.programs', compact('programs', 'mentors'));
    }

    public function adminMentors() {
        $mentors = User::where('role', 'mentor')->get();
        return view('admin.mentors', compact('mentors'));
    }

    public function adminPayments() {
        $payments = DB::table('enrollments')
            ->join('users', 'enrollments.user_id', '=', 'users.id')
            ->join('programs', 'enrollments.program_id', '=', 'programs.id')
            ->select('enrollments.*', 'users.name as user_name', 'programs.name as program_name')
            ->where('enrollments.status_pembayaran', 'pending')
            ->whereNotNull('enrollments.bukti_pembayaran')
            ->get();
        return view('admin.payments', compact('payments'));
    }

    public function adminSettings() { return view('admin.settings'); }

    /**
     * ==========================================
     * 5. FITUR SISWA (VIEWS)
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

    public function siswaSchedule() {
        return view('siswa.schedule', ['schedule' => []]);
    }

    public function siswaBilling() {
        $user = Auth::user();
        $payments = DB::table('enrollments')
            ->join('programs', 'enrollments.program_id', '=', 'programs.id')
            ->where('enrollments.user_id', $user->id)
            ->select('enrollments.*', 'programs.name as program_name')
            ->get()
            ->map(function($item) {
                $item->created_at = Carbon::parse($item->created_at);
                return $item;
            });
        return view('siswa.billing', compact('payments'));
    }

    /**
     * ==========================================
     * 6. FITUR MENTOR (VIEWS)
     * ==========================================
     */
    public function mentorOverview() {
        $user = Auth::user();
        $stats = [
            'total_students' => DB::table('enrollments')
                ->join('programs', 'enrollments.program_id', '=', 'programs.id')
                ->where('programs.mentor_id', $user->id)
                ->where('enrollments.status_pembayaran', 'verified')
                ->distinct('enrollments.user_id')->count('enrollments.user_id'),
            'total_classes' => DB::table('programs')->where('mentor_id', $user->id)->count(),
            'today_sessions' => 0 
        ];
        return view('mentor.overview', compact('stats'));
    }

    public function mentorClasses() {
        $classes = DB::table('programs')->where('mentor_id', Auth::id())->get();
        return view('mentor.classes', compact('classes'));
    }

    public function mentorSchedule() {
        $schedule = DB::table('enrollments')
            ->join('programs', 'enrollments.program_id', '=', 'programs.id')
            ->join('users', 'enrollments.user_id', '=', 'users.id')
            ->where('programs.mentor_id', Auth::id())
            ->where('enrollments.status_pembayaran', 'verified')
            ->select('enrollments.*', 'programs.name as program_name', 'users.name as student_name')
            ->get();
        return view('mentor.schedule', compact('schedule'));
    }

    /**
     * ==========================================
     * 7. PROSES DATA (POST METHODS)
     * ==========================================
     */
    public function enrollProgram(Request $request) {
        $program = DB::table('programs')->where('id', $request->program_id)->first();
        $targetRoute = ($program && $program->type === 'intensif') ? 'program.intensif' : 'program.reguler';
        return redirect()->route($targetRoute, ['step' => 3])->with('success', 'Pendaftaran Berhasil!');
    }

    public function uploadBukti(Request $request, $id) {
        if ($request->hasFile('bukti_pembayaran')) {
            $file = $request->file('bukti_pembayaran');
            $fileName = time() . '_' . Auth::id() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/bukti'), $fileName);
            
            DB::table('enrollments')->where('id', $id)->update([
                'bukti_pembayaran' => $fileName, 
                'status_pembayaran' => 'pending',
                'updated_at' => now()
            ]);
            return back()->with('success', 'Bukti transfer berhasil diperbarui!');
        }
        return back()->with('error', 'Gagal mengupload file.');
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

    public function storeProgram(Request $request) {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
        ]);

        DB::table('programs')->insert([
            'name' => $request->name, 
            'jenjang' => $request->jenjang, 
            'type' => $request->type,
            'price' => $request->price, 
            'mentor_id' => $request->mentor_id, 
            'created_at' => now(),
        ]);
        return back()->with('success', 'Program baru berhasil ditambahkan!');
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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

    $step = $request->query('step', 1);
    
    return view('pages.program.reguler', compact('programs', 'subjects', 'mapelByJenjang', 'step'));
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
            ? route('program.reguler', ['step' => 3]) 
            : route('program.intensif', ['step' => 3]);
        
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
                'enrollments.*',
                'users.name as user_name',
                'programs.jenjang as program_jenjang',
                'programs.name as program_name'
            )
            ->orderBy('enrollments.created_at', 'desc')
            ->paginate(10);

        // TRANSFORMASI DATA UNTUK TAMPILAN DASHBOARD
        $recent_enrollments->getCollection()->transform(function($item) {
            // 1. REVISI LOGIKA LOKASI: Menyesuaikan dengan kolom 'alamat_semarang' di database
            // Gunakan trim untuk memastikan data kosong/null ditangani dengan benar
            $alamatSemarang = trim($item->alamat_semarang ?? '');
            $cabang = trim($item->lokasi_cabang ?? '');
            
            // Cek apakah kolom berisi alamat nyata (bukan kosong, null, atau strip '-')
            $hasRealAlamat = !empty($alamatSemarang) && $alamatSemarang !== '-';
            $hasRealCabang = !empty($cabang) && $cabang !== '-';

            if ($hasRealAlamat || $hasRealCabang) {
                // Tampilkan alamat_semarang jika ada, jika tidak gunakan lokasi cabang
                $item->display_lokasi = $hasRealAlamat ? $item->alamat_semarang : $item->lokasi_cabang;
                $item->is_online = false;
            } else {
                // Fallback jika benar-benar tidak ada data alamat offline
                $item->display_lokasi = "VIA ZOOM (ONLINE)";
                $item->is_online = true;
            }
            
            // 2. Logika Program: Mengubah ID menjadi Nama Mata Pelajaran asli
            $mapelRaw = $item->mapel;
            $mapelIds = json_decode($mapelRaw, true);
            
            if (!is_array($mapelIds)) {
                $mapelIds = array_filter(explode(',', $mapelRaw));
            }

            // Ambil nama-nama mata pelajaran dari database berdasarkan ID
            $mapelNames = DB::table('programs')
                ->whereIn('id', $mapelIds)
                ->pluck('name')
                ->toArray();

            // Tampilkan daftar nama mapel yang dipisah koma, atau fallback ke program_name
            $item->display_program = count($mapelNames) > 0 
                ? implode(', ', $mapelNames) 
                : ( (!empty($mapelRaw) && $mapelRaw !== '0') ? $mapelRaw : $item->program_name );
            
            return $item;
        });

        $mentors = User::where('role', 'mentor')->get();

        return view('admin.overview', compact('stats', 'recent_enrollments', 'mentors'));
        
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
            // Ambil 3 digit terakhir dari WA untuk ditampilkan sebagai kode unik
            $cleanPhone = preg_replace('/[^0-9]/', '', $item->user_wa ?? '000');
            $item->kode_unik_tampil = substr($cleanPhone, -3);
            
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
        DB::table('enrollments')->where('id', $id)->update([
            'status_pembayaran' => 'verified',
            'updated_at' => now()
        ]);
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
        $request->validate([
            'mentor_id' => 'required|exists:users,id',
            'jadwal_pertemuan' => 'nullable|string',
        ]);

        DB::table('enrollments')->where('id', $id)->update([
            'mentor_id' => $request->mentor_id,
            'jadwal_pertemuan' => $request->jadwal_pertemuan, 
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Jadwal dan Mentor berhasil ditetapkan!');
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

        $mentors = User::where('role', 'mentor')->get();
        $subjects = DB::table('subjects')->orderBy('name', 'asc')->get();
        
        return view('admin.programs', compact('programs', 'mentors', 'subjects', 'title', 'type'));
    }

    public function storeProgram(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'jenjang' => 'required',
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
                'enrollments.mapel as selected_mapel_raw', // Ambil data mapel mentah untuk diproses
                'enrollments.jadwal_detail as sesi_jadwal',
                'enrollments.jumlah_pertemuan', 
                'enrollments.pertemuan_selesai',
                'enrollments.program_id as original_program_id'
            )
            ->get()
            ->map(function($program) use ($user) {
                // PROSES NAMA MAPEL UNTUK PROGRAM AKTIF (Kiri)
                $mapelIds = json_decode($program->selected_mapel_raw, true);
                if (!is_array($mapelIds)) {
                    $mapelIds = [$program->selected_mapel_raw];
                }

                $mapelNames = DB::table('programs')
                    ->whereIn('id', $mapelIds)
                    ->pluck('name')
                    ->toArray();

                // Menambahkan property baru 'display_mapel' untuk digunakan di View
                $program->display_mapel = count($mapelNames) > 0 ? implode(', ', $mapelNames) : $program->base_program_name;

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
                'enrollments.jadwal_detail', 
                'enrollments.lokasi_cabang',
                'enrollments.per_minggu',
                'enrollments.status_pembayaran as status', 
                'enrollments.created_at'
            )
            ->orderBy('enrollments.created_at', 'desc')
            ->limit(5)->get()
            ->map(function($item) {
                // PROSES NAMA MAPEL UNTUK AKTIVITAS TERBARU (Kanan)
                $mapelIds = json_decode($item->mapel, true);
                if (!is_array($mapelIds)) {
                    $mapelIds = [$item->mapel];
                }

                $mapelNames = DB::table('programs')
                    ->whereIn('id', $mapelIds)
                    ->pluck('name')
                    ->toArray();

                $namaMapelTampil = count($mapelNames) > 0 ? implode(', ', $mapelNames) : 'Program Reguler';

                $item->title = "Pendaftaran Les: " . strtoupper($namaMapelTampil);
                
                $metode = $item->lokasi_cabang ? "Offline (" . $item->lokasi_cabang . ")" : "Online";
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
        $my_programs = DB::table('enrollments')
            ->join('programs', 'enrollments.program_id', '=', 'programs.id')
            ->leftJoin('users as mentors', 'enrollments.mentor_id', '=', 'mentors.id')
            ->where('enrollments.user_id', Auth::id())
            ->select(
                'programs.name as base_name', 
                'mentors.name as mentor_name', 
                'enrollments.status_pembayaran', 
                'enrollments.created_at as registration_date', 
                'enrollments.mapel', 
                'enrollments.lokasi_cabang',
                'enrollments.alamat_siswa',
                'enrollments.jumlah_pertemuan',
                'enrollments.jadwal_detail'
            )
            ->get();

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
                'enrollments.mapel as mapel_raw'
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

        $enrollment = DB::table('enrollments')->where('id', $material->program_id)->first();
        
        if (!$enrollment || !$enrollment->is_absen_active) {
            return response()->json(['success' => false, 'message' => 'Absensi belum dibuka oleh mentor.'], 403);
        }

        $alreadyAttended = DB::table('attendances')
            ->where('student_id', Auth::id())
            ->where('program_id', $enrollment->id)
            ->where('date', date('Y-m-d'))
            ->exists();

        if ($alreadyAttended) {
            return response()->json(['success' => false, 'message' => 'Anda sudah melakukan absensi hari ini.'], 400);
        }

        DB::table('attendances')->insert([
            'student_id' => Auth::id(),
            'program_id' => $enrollment->id,
            'date' => date('Y-m-d'),
            'status' => 'Hadir',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Berhasil absen!']);
    }

   public function enrollProgram(Request $request) {
    // 1. Validasi Input (Pastikan data minimal tersedia)
    $request->validate([
        'program_id' => 'required',
        'jenjang' => 'required',
        'tipe_paket' => 'required',
        'per_minggu' => 'required',
        'jadwal_detail' => 'required',
        'bukti_pembayaran' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        'total_harga' => 'required',
    ]);

    try {
        DB::beginTransaction();
        $user = Auth::user();

        // 2. CEK VALIDASI PROGRAM ID
        $programExists = DB::table('programs')->where('id', $request->program_id)->exists();
        if (!$programExists) {
            return redirect()->back()->with('error', 'Gagal: Program ID ('.$request->program_id.') tidak ditemukan di database.');
        }
        
        // 3. Logika Kode Unik (3 digit WA)
        $cleanPhone = preg_replace('/[^0-9]/', '', $user->whatsapp ?? '000');
        $uniqueCode = (int) substr($cleanPhone, -3); 
        $baseAmount = (int) preg_replace('/[^0-9]/', '', $request->total_harga);
        $finalAmount = $baseAmount + $uniqueCode;

        // 4. Simpan File Bukti
        $path = $request->file('bukti_pembayaran')->store('pembayaran/reguler', 'public');
        $namaFile = basename($path);

        // 5. Proses Nama Mapel (Konversi ID ke Nama untuk tampilan Dashboard)
        $mapelIds = json_decode($request->selected_subjects ?? '[]', true);
        if (!is_array($mapelIds)) {
            $mapelIds = [$request->program_id];
        }
        
        $mapelNames = DB::table('programs')
            ->whereIn('id', $mapelIds)
            ->pluck('name')
            ->toArray();

        $mapelString = count($mapelNames) > 0 ? implode(', ', $mapelNames) : 'Program Reguler';

        // 6. Simpan ke Database
        DB::table('enrollments')->insert([
            'user_id' => $user->id,
            'program_id' => $request->program_id,
            'jenjang' => $request->jenjang,
            'tipe_paket' => $request->tipe_paket,
            'per_minggu' => $request->per_minggu,
            'extra_hours' => $request->extra_hours ?? 0,
            'is_mengaji' => $request->is_mengaji ?? 0,
            'jadwal_detail' => $request->jadwal_detail,
            'mapel' => $mapelString, 
            'metode' => $request->lokasi_cabang ? 'offline' : 'online',
            'lokasi_cabang' => $request->lokasi_cabang,
            
            // PENYAMBUNGAN KRUSIAL: Memastikan data input 'alamat_siswa' dari form
            // masuk ke kolom 'alamat_semarang' sesuai struktur database Anda.
            'alamat_semarang' => $request->alamat_siswa ?? '-', 
            
            'total_harga' => $finalAmount,
            'status_pembayaran' => 'pending',
            'bukti_pembayaran' => 'pembayaran/reguler/' . $namaFile,
            'jumlah_pertemuan' => 8,
            'pertemuan_selesai' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::commit();
        
        return redirect()->route('siswa.overview')->with('success', 'Pendaftaran Berhasil! Admin akan segera memverifikasi.');
        
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Sistem Error: ' . $e->getMessage());
    }
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
            ->join('users', 'enrollments.user_id', '=', 'users.id')
            ->where('enrollments.mentor_id', $user->id)
            ->whereRaw('LOWER(enrollments.status_pembayaran) = ?', ['verified'])
            ->where('enrollments.hari', 'like', '%' . $hari_ini . '%')
            ->select(
                'enrollments.id', 
                'enrollments.mapel as program_name', 
                'enrollments.jam_mulai', 
                'users.name as student_name', 
                'enrollments.lokasi_cabang'
            )
            ->get()
            ->map(function($item) {
                $item->jam_tampil = $item->jam_mulai ? Carbon::parse($item->jam_mulai)->format('H:i') : '--:--';
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
                ->whereRaw('LOWER(status_pembayaran) = ?', ['verified'])
                ->distinct('user_id')->count(),
            'total_kelas' => DB::table('enrollments')
                ->where('mentor_id', $user->id)
                ->whereRaw('LOWER(status_pembayaran) = ?', ['verified'])
                ->distinct('mapel')->count(),
        ];

        return view('mentor.overview', compact('user', 'today_schedule', 'stats', 'assignments'));
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
            ->whereRaw('LOWER(enrollments.status_pembayaran) = ?', ['verified'])
            ->when($targetId, function($query, $targetId) {
                return $query->where('enrollments.id', $targetId);
            })
            ->select(
                'enrollments.id', 
                'enrollments.mapel as name', 
                'enrollments.jenjang', 
                'enrollments.hari',
                'enrollments.jam_mulai as jam',
                'enrollments.jadwal_detail',
                'enrollments.user_id',
                'enrollments.is_absen_active',
                'users.name as student_name'
            )
            ->get()
            ->map(function($class) use ($today, $user) {
                $class->type = 'Reguler'; 
                $class->total_sessions = 12;

                $class->students = DB::table('users')
                    ->leftJoin('attendances', function($join) use ($today, $class) {
                        $join->on('users.id', '=', 'attendances.student_id')
                             ->where('attendances.program_id', '=', $class->id)
                             ->where('attendances.date', '=', $today);
                    })
                    ->where('users.id', $class->user_id)
                    ->select('users.id', 'users.name', 'attendances.status as status')
                    ->get();
                
                $class->student_count = $class->students->count(); 
                
                $class->materials = DB::table('program_materials')
                    ->where('program_id', $class->id)
                    ->orderBy('session_number', 'asc')
                    ->get();

                return $class;
            });

        return view('mentor.classes', compact('classes'));
    }

    public function toggleAbsen(Request $request) {
        $request->validate(['class_id' => 'required', 'is_active' => 'required|boolean']);
        
        try {
            DB::table('enrollments')->where('id', $request->class_id)->update([
                'is_absen_active' => $request->is_active,
                'updated_at' => now()
            ]);
            return response()->json(['success' => true, 'message' => 'Status absensi diperbarui']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui status']);
        }
    }

    public function storeMaterial(Request $request) {
        $request->validate([
            'program_id' => 'required',
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
        $user = Auth::user();

        $schedule = DB::table('enrollments')
            ->join('users', 'enrollments.user_id', '=', 'users.id')
            ->where('enrollments.mentor_id', $user->id)
            ->whereRaw('LOWER(enrollments.status_pembayaran) = ?', ['verified'])
            ->select(
                'enrollments.id',
                'enrollments.mapel as program_name', 
                'enrollments.hari', 
                'enrollments.jam_mulai', 
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
                $parts = explode(',', $item->jadwal_detail);
                foreach ($parts as $part) {
                    preg_match('/(\w+)\s*\(([\d.]+)\)/', strtolower($part), $matches);
                    if (count($matches) >= 3) {
                        $newEntry = clone $item;
                        $newEntry->hari = ucfirst(trim($matches[1]));
                        $newEntry->jam_mulai = str_replace('.', ':', $matches[2]);
                        $sessions[] = $newEntry;
                    }
                }
            } else {
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
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    use HasFactory;

    /**
     * Kolom yang dapat diisi secara massal.
     * Sesuai dengan migrasi full untuk mendukung diagram alur.
     */
    protected $fillable = [
        'user_id',
        'program_id',
        'per_minggu',        // Tambahan revisi
        'jadwal_detail',     // Tambahan revisi
        'metode',
        'jenjang',
        'mapel',
        'alamat_semarang',
        'mentor_id',
        'jadwal_pertemuan',
        'ambil_mengaji',
        'batch_intensif',
        'total_harga',
        'status_pembayaran',
        'payment_method',
        'midtrans_order_id',
        'midtrans_snap_token',
        'midtrans_transaction_status',
        'midtrans_payment_type',
        'midtrans_payload',
        'paid_at',
        'mentor_assignment_status',
        'mentor_assignment_note',
        'mentor_requested_at',
        'mentor_responded_at',
        'assigned_by_admin_id',
        'progres_nilai',
    ];

    /**
     * RELASI: Pendaftaran ini milik siapa (Siswa).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * RELASI: Pendaftaran ini mengambil program apa.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }
}
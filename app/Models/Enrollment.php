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
        'metode',
        'jenjang',
        'mapel',
        'alamat_semarang',
        'jadwal_pertemuan',
        'ambil_mengaji',
        'batch_intensif',
        'total_harga',
        'status_pembayaran',
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
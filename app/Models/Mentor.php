<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mentor extends Model
{
    use HasFactory;

    // Tambahkan ini untuk memastikan model merujuk ke tabel yang benar
    protected $table = 'mentors';

    protected $fillable = ['name', 'specialist', 'whatsapp', 'photo', 'user_id'];

    /**
     * Relasi ke model User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
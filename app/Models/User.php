<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'username',      
        'password',
        'role',             
        'whatsapp',         
        'specialization',   
        'jenjang',          
        'is_trial_claimed', 
        'birth_date',    
        'gender',        
        'school',        
        'referral',      
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * REVISI DI SINI:
     * Pastikan format casting benar untuk Laravel 11
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_trial_claimed' => 'boolean',
            'birth_date' => 'date', 
        ];
    }

    // ... sisa kode relasi dan helper (enrollments, programs, isAdmin, dll) tetap sama
    // Tidak ada perubahan pada fungsi-fungsi di bawahnya sesuai permintaan Anda.

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function programs(): HasMany
    {
        return $this->hasMany(Program::class, 'mentor_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isMentor(): bool
    {
        return $this->role === 'mentor';
    }

    public function isSiswa(): bool
    {
        return $this->role === 'siswa';
    }
}
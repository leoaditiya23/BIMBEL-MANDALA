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
        'specialist',       // Ditambahkan agar sinkron dengan input Mentor
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

    public function enrollments(): HasMany
    {
        return $this->hasMany(\App\Models\Enrollment::class);
    }

    public function programs(): HasMany
    {
        return $this->hasMany(\App\Models\Program::class, 'mentor_id');
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
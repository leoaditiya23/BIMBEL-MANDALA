<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Atribut yang dapat diisi (Mass Assignable).
     * Kolom ini harus sesuai dengan input di Register & DatabaseSeeder.
     */
   protected $fillable = [
    'name',
    'email',
    'password',
    'role',             
    'whatsapp',         
    'specialization',   
    'jenjang',          
    'is_trial_claimed', 
];

    /**
     * Atribut yang disembunyikan saat serialisasi (JSON).
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting tipe data kolom.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_trial_claimed' => 'boolean',
        ];
    }

    /**
     * RELASI: Seorang User (Siswa) bisa memiliki banyak pendaftaran program.
     * Digunakan di PageController@dashboard untuk menarik data $my_programs.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * RELASI: Seorang User (Mentor) bisa mengajar banyak program.
     */
    public function programs(): HasMany
    {
        return $this->hasMany(Program::class, 'mentor_id');
    }

    /**
     * HELPER: Mengecek Role User
     */
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
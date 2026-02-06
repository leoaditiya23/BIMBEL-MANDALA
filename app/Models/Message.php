<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'messages'; // Nama tabel di database
    protected $fillable = ['name', 'whatsapp', 'email', 'message', 'is_read'];
}
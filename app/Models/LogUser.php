<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LogUser extends Model
{
    use SoftDeletes;

    protected $table = 'log_user'; // Sesuai schema kamu

    protected $fillable = [
        'log',
        'jenis_log',
        'user_id'
    ];

    /**
     * Relasi ke model User
     */
    public function user()
    {
        // Pastikan 'User' mengarah ke model User kamu
        return $this->belongsTo(User::class, 'user_id');
    }
}

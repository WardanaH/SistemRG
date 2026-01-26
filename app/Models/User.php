<?php

namespace App\Models;

use App\Models\MCabang;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'nama',
        'username',
        'email',
        'password',
        'cabang_id',
    ];

    public function cabang()
    {
        return $this->belongsTo(MCabang::class, 'cabang_id');
    }
}

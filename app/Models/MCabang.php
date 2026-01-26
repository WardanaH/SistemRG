<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MCabang extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'kode',
        'nama',
        'slug',
        'email',
        'telepon',
        'alamat',
        'jenis'
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'cabang_id');
    }
}

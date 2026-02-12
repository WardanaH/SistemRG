<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MFinishing extends Model
{
    use HasFactory;

    protected $table = 'm_finishings';
    protected $guarded = ['id'];
}

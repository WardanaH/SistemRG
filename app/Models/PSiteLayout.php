<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PSiteLayout extends Model
{
    protected $table = 'p_site_layouts';

    protected $fillable = ['navbar', 'footer'];

    protected $casts = [
        'navbar' => 'array',
        'footer' => 'array',
    ];
}

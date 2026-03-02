<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PKontakBranche extends Model
{
    protected $table = 'p_kontak_branches';

    protected $fillable = [
        'name','address','maps_url','lat','lng','is_active','sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'lat' => 'float',
        'lng' => 'float',
        'sort_order' => 'integer',
    ];
}

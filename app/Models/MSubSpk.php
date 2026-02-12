<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MSubSpk extends Model
{
    protected $guarded = ['id'];

    public function spk()
    {
        return $this->belongsTo(MSpk::class, 'spk_id');
    }

    public function bahan()
    {
        return $this->belongsTo(MBahanBaku::class, 'bahan_id');
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }
}

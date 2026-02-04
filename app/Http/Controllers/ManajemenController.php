<?php

namespace App\Http\Controllers;

use App\Models\MBahanBaku;
use Illuminate\Http\Request;

class ManajemenController extends Controller
{
    public function index()
    {
        return view('spk.manajemen.index' , [
            'title' => 'Dashboard Manajemen',
            'bahans' => MBahanBaku::all()
        ]);
    }
}

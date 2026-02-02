<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class OperatorController extends Controller
{
    public function index()
    {
        // Ambil semua user yang punya role 'operator'
        $operators = User::role(['operator outdoor', 'operator indoor', 'operator multi'])->with('cabang')->get();
        // dd($operators);

        return view('spk.operator.index', [
            'title' => 'Dashboard Operator',
            'operators' => $operators
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DesignerController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        return view('spk.designer.index', [
            'user' => $user,
            'title' => 'Dashboard Designer'
        ]);
    }

    
}

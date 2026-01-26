<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DesignerController extends Controller
{
    public function index(Request $request)
    {
        return view('spk.designer.index');
    }

    public function spk()
    {
        return view('spk.designer.spk');
    }
}

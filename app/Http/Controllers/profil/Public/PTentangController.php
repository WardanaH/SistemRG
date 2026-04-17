<?php

namespace App\Http\Controllers\Profil\Public;

use App\Http\Controllers\Controller;
use App\Models\PTentang;

class PTentangController extends Controller
{
    public function index()
    {
        $about = PTentang::query()->first();
        if (!$about) $about = PTentang::query()->create(PTentang::defaults());

        return view('profil.public.pages.tentang', compact('about'));
    }
}

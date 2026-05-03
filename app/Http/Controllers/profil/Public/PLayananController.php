<?php

namespace App\Http\Controllers\Profil\Public;

use App\Http\Controllers\Controller;
use App\Models\PLayanan;

class PLayananController extends Controller
{
    public function index()
    {
        $s = PLayanan::query()->first();
        $defaults = PLayanan::defaults();

        $data = $defaults;

        if ($s) {
            foreach ($defaults as $k => $v) {
                $data[$k] = $s->{$k} ?? $v;
            }
        }

        return view('profil.public.pages.layanan', [
            'layanan' => $s,
            'data' => $data,
        ]);
    }
}

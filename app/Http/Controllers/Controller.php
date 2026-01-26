<?php

namespace App\Http\Controllers;

use App\Models\LogUser;

abstract class Controller
{
    public function log($log, $category)
    {
        $table = new LogUser();
        $table->user_id = auth()->user()->id;
        $table->log = $log;
        $table->jenis_log = $category;
        $table->save();
    }
}

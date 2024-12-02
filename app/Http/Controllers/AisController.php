<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AisController extends Controller
{
    public function indexAis()
    {
        return view('general.ais');
    }

    public function indexAisHistory()
    {
        return view('general.aishistory');
    }
}

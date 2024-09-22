<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MapController extends Controller
{
    public function index()
    {
        return view('maps');
    }
    public function index2()
    {
        return view('maps2');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Key;

class KeyValueWebController extends Controller
{
    public function index()
    {
        $keyValues = Key::with('latestValue')->orderBy('key')->get();
        return view('key-values.index', compact('keyValues'));
    }
}

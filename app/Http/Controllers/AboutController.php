<?php

namespace App\Http\Controllers;

// FIX: Hapus Route::get() yang salah ditulis di dalam body method
class AboutController extends Controller
{
    public function index()
    {
        return view('about');
    }
}
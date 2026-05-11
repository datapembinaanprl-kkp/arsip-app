<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ArchiveController extends Controller
{
    public function index()    { return inertia('archives/Index'); }
    public function create()   { return inertia('archives/Create'); }
    public function store()    { return back(); }
    public function show($id)  { return inertia('archives/Show'); }
    public function edit($id)  { return inertia('archives/Edit'); }
    public function update()   { return back(); }
    public function destroy()  { return back(); }
    public function tolak()    { return back(); }
}
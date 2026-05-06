<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\UserController;


class UserController extends Controller
{
    //
}

Route::get('/admin/users', [UserController::class, 'index']);
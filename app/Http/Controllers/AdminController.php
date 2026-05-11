<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // Semua fungsi sudah dipindah ke UserController
    // File ini hanya dipertahankan agar route lama tidak crash

    public function users(): RedirectResponse
    {
        return redirect()->route('users.index');
    }

    public function createUser(): RedirectResponse
    {
        return redirect()->route('users.create');
    }

    public function storeUser(): RedirectResponse
    {
        return redirect()->route('users.create');
    }

    public function editUser(User $user): RedirectResponse
    {
        return redirect()->route('users.edit', $user);
    }

    public function updateUser(User $user): RedirectResponse
    {
        return redirect()->route('users.edit', $user);
    }

    public function toggleUser(User $user): RedirectResponse
    {
        return redirect()->route('users.edit', $user);
    }

    public function destroyUser(User $user): RedirectResponse
    {
        return redirect()->route('users.index');
    }
}
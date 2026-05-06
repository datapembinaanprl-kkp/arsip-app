<?php

// LOKASI FILE INI: app/Http/Controllers/AdminController.php
// HAPUS file: app/Http/Controllers/Admin/AdminController.php
//
// Penyebab error:
//   Fatal: Cannot declare class App\Http\Controllers\AdminController,
//          because the name is already in use
//
// Solusi: Hanya boleh ada SATU AdminController.
// Gunakan file ini (namespace App\Http\Controllers)
// dan hapus yang ada di subfolder Admin/

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    // FIX: Constructor bersih — proteksi role di routes/web.php
    // dengan ->middleware('role:admin'), bukan $this->middleware()

    // ─── Daftar pengguna ──────────────────────────────────
    public function users(Request $request): View
    {
        $query = User::with('roles')->latest();

        if ($search = $request->get('q')) {
            $query->where(fn($q) => $q
                ->where('name',  'ilike', "%{$search}%")
                ->orWhere('email', 'ilike', "%{$search}%")
            );
        }

        if ($role = $request->get('role')) {
            $query->whereHas('roles', fn($q) => $q->where('name', $role));
        }

        $users = $query->paginate(15)->withQueryString();
        $roles = Role::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'roles'));
    }

    // ─── Form tambah pengguna ─────────────────────────────
    public function createUser(): View
    {
        $roles = Role::orderBy('name')->get();
        return view('admin.users.create', compact('roles'));
    }

    // ─── Simpan pengguna baru ─────────────────────────────
    public function storeUser(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|exists:roles,name',
        ], [
            'email.unique'       => 'Email sudah digunakan.',
            'password.min'       => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'is_active' => true,
        ]);

        $user->assignRole($request->role);

        return redirect()->route('admin.users')
            ->with('success', "Pengguna {$user->name} berhasil ditambahkan.");
    }

    // ─── Form edit pengguna ───────────────────────────────
    public function editUser(User $user): View
    {
        $roles = Role::orderBy('name')->get();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    // ─── Update pengguna ──────────────────────────────────
    public function updateUser(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => "required|email|unique:users,email,{$user->id}",
            'password' => 'nullable|string|min:8|confirmed',
            'role'     => 'required|exists:roles,name',
        ]);

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        $user->syncRoles([$request->role]);

        return redirect()->route('admin.users')
            ->with('success', "Pengguna {$user->name} berhasil diperbarui.");
    }

    // ─── Toggle aktif/nonaktif ────────────────────────────
    public function toggleUser(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menonaktifkan akun sendiri.');
        }

        $user->update(['is_active' => ! $user->is_active]);
        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Pengguna {$user->name} berhasil {$status}.");
    }

    // ─── Hapus pengguna ───────────────────────────────────
    public function destroyUser(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('admin.users')
            ->with('success', "Pengguna {$user->name} berhasil dihapus.");
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\TimKerja;
use App\Http\Requests\StoreTimKerjaRequest;
use App\Http\Requests\UpdateTimKerjaRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TimKerjaController extends Controller
{
    public function index(Request $request): Response
    {
        $timKerjas = TimKerja::query()
            ->when($request->filled('search'), fn($q) =>
                $q->where(fn($q) =>
                    $q->where('nama', 'ilike', '%' . $request->search . '%')
                      ->orWhere('kode', 'ilike', '%' . $request->search . '%')
                )
            )
            ->when($request->filled('status'), fn($q) =>
                $q->where('is_active', $request->status === 'active')
            )
            ->withCount('documents')
            ->orderBy('nama')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('tim-kerja/Index', [
            'tim_kerjas' => $timKerjas,
            'filters'    => $request->only(['search', 'status']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('tim-kerja/Create');
    }

    public function store(StoreTimKerjaRequest $request)
    {
        TimKerja::create($request->validated());

        return redirect()->route('tim-kerja.index')
            ->with('success', 'Tim Kerja berhasil ditambahkan.');
    }

    public function edit(TimKerja $timKerja): Response
    {
        return Inertia::render('tim-kerja/Edit', [
            'tim_kerja' => $timKerja,
        ]);
    }

    public function update(UpdateTimKerjaRequest $request, TimKerja $timKerja)
    {
        $timKerja->update($request->validated());

        return redirect()->route('tim-kerja.index')
            ->with('success', 'Tim Kerja berhasil diperbarui.');
    }

    public function destroy(TimKerja $timKerja)
    {
        if ($timKerja->documents()->exists()) {
            return back()->with('error', 'Tim Kerja tidak bisa dihapus karena masih memiliki dokumen.');
        }

        $timKerja->delete();

        return redirect()->route('tim-kerja.index')
            ->with('success', 'Tim Kerja berhasil dihapus.');
    }
}
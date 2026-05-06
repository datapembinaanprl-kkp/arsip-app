<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrganizationalStructureRequest;
use App\Models\OrganizationalStructure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use App\Services\ActivityLogger;

class OrganizationalStructureController extends Controller
{
    /**
     * Display the org chart list and tree.
     */
    public function index(): View
    {
        // Load root members with their full recursive tree
        $members = OrganizationalStructure::roots()
            ->with('allChildren')
            ->get();

        // Flat list for table view
        $allMembers = OrganizationalStructure::orderBy('parent_id')->orderBy('order')->get();

        return view('organizational-structure.index', compact('members', 'allMembers'));
    }

    /**
     * Show create form.
     */
    public function create(): View
    {
        // All members available as potential parents (flat list for select dropdown)
        $parents = OrganizationalStructure::orderBy('name')->get();

        return view('organizational-structure.create', compact('parents'));
    }

    /**
     * Store a new member.
     */
    public function store(OrganizationalStructureRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Handle photo upload → store in public disk under org-photos/
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('org-photos', 'public');
        }

        // Default order to next available slot within the same parent
        if (empty($data['order'])) {
            $data['order'] = OrganizationalStructure::where('parent_id', $data['parent_id'] ?? null)
                ->max('order') + 1;
        }

        OrganizationalStructure::create($data);
        $member = OrganizationalStructure::create($data);
        ActivityLogger::organisasiDibuat($member->name);

        return redirect()->route('organizational-structure.index')
            ->with('success', 'Anggota berhasil ditambahkan.');
    }

    /**
     * Show edit form.
     */
    public function edit(OrganizationalStructure $organizationalStructure): View
    {
        // Exclude self from parent options to prevent circular reference
        $parents = OrganizationalStructure::where('id', '!=', $organizationalStructure->id)
            ->orderBy('name')
            ->get();

        return view('organizational-structure.edit', [
            'member'  => $organizationalStructure,
            'parents' => $parents,
        ]);
    }

    /**
     * Update an existing member.
     */
    public function update(OrganizationalStructureRequest $request, OrganizationalStructure $organizationalStructure): RedirectResponse
    {
        $data = $request->validated();

        // Prevent a member from being its own parent
        if (!empty($data['parent_id']) && $data['parent_id'] == $organizationalStructure->id) {
            return back()->withErrors(['parent_id' => 'Anggota tidak bisa menjadi atasan dirinya sendiri.']);
        }

        // Replace photo if a new one is uploaded
        if ($request->hasFile('photo')) {
            // Delete old photo from storage
            if ($organizationalStructure->photo) {
                Storage::disk('public')->delete($organizationalStructure->photo);
            }
            $data['photo'] = $request->file('photo')->store('org-photos', 'public');
        }

        $organizationalStructure->update($data);
        ActivityLogger::organisasiDiperbarui($organizationalStructure->name);

        return redirect()->route('organizational-structure.index')
            ->with('success', 'Anggota berhasil diperbarui.');;
    }

    /**
     * Delete a member and clean up their photo.
     */
    public function destroy(OrganizationalStructure $organizationalStructure): RedirectResponse
    {
        // Remove photo asset from storage
        if ($organizationalStructure->photo) {
            Storage::disk('public')->delete($organizationalStructure->photo);
        }

        // Children's parent_id becomes null (set null FK cascade handles this)
        ActivityLogger::organisasiDihapus($organizationalStructure->name);
        $organizationalStructure->delete();

        return redirect()->route('organizational-structure.index')
            ->with('success', 'Anggota berhasil dihapus.');
    }
}
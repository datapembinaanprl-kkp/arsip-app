@extends('layouts.app') {{-- Match your existing admin layout --}}

@section('title', 'Struktur Organisasi')

@section('content')
<div class="os-page">

    {{-- ── Page Header ─────────────────────────────────────────── --}}
    <div class="os-header">
        <div>
            <h1 class="os-title">Struktur Organisasi</h1>
            <p class="os-subtitle">Kelola hierarki dan jabatan anggota organisasi</p>
        </div>
        <a href="{{ route('organizational-structure.create') }}" class="os-btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2z"/></svg>
            Tambah Staff
        </a>
    </div>

    {{-- ── Flash Messages ───────────────────────────────────────── --}}
    @if(session('success'))
        <div class="os-alert os-alert-success">{{ session('success') }}</div>
    @endif

    {{-- ── Tab Navigation ───────────────────────────────────────── --}}
    <div class="os-tabs">
        <button class="os-tab active" data-target="tab-tree">Tampilan Hierarki</button>
        <button class="os-tab" data-target="tab-table">Tampilan Tabel</button>
    </div>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- TAB 1: Hierarchy / Org Chart Tree                          --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
        <div id="tab-tree" class="os-tab-content active">
        <div class="os-card">
            <div class="os-tree-wrapper">
                @if($members->isEmpty())
                    <div class="os-empty">
                        Belum ada anggota.
                        <a href="{{ route('organizational-structure.create') }}">Tambah yang pertama.</a>
                    </div>
                @else
                    <div class="os-tree">
                        @foreach($members as $root)
                            @include('organizational-structure.tree-node', [
                                'member' => $root,
                                'depth'  => 0,
                            ])
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- TAB 2: Flat Table View                                     --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div id="tab-table" class="os-tab-content">
        <div class="os-card">
            <table class="os-table">
                <thead>
                    <tr>
                    <th>#</th>                        
                    <th>Foto</th>
                    <th>Nama</th>
                    <th>Jabatan</th>
                    <th>Melapor Kepada</th>
                    <th>Urutan</th>
                    <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allMembers as $index => $member)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <img src="{{ $member->photo_url }}" alt="{{ $member->name }}" class="os-avatar-sm">
                        </td>
                        <td class="os-name">{{ $member->name }}</td>
                        <td><span class="os-badge">{{ $member->position }}</span></td>
                        <td class="os-muted">{{ $member->parent?->name ?? '—' }}</td>
                        <td class="os-muted">{{ $member->order }}</td>
                        <td>
                            <div class="os-actions">
                                <a href="{{ route('organizational-structure.edit', $member) }}" class="os-btn-icon os-btn-edit" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/></svg>
                                </a>
                                <form action="{{ route('organizational-structure.destroy', $member) }}" method="POST" class="os-delete-form">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="os-btn-icon os-btn-deleete" title="Hapus" onclick="return confirm('Hapus {{ addslashes($member->name) }}? Bawahannya akan dipindah ke level teratas.')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/><path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="os-empty">Belum ada anggota.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>


@include('organizational-structure.styles')
@include('organizational-structure.scripts')
@endsection
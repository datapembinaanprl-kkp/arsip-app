@extends('layouts.app')
@section('title', 'Daftar Dokumen')

@section('content')
<div class="doc-page">

    {{-- ── Header ──────────────────────────────────────────────── --}}
    <div class="doc-header">
        <div>
            <h1 class="doc-title">Daftar Dokumen</h1>
            <p class="doc-subtitle">Kelola dan pantau seluruh dokumen</p>
        </div>
        <a href="{{ route('documents.create') }}" class="doc-btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2z"/>
            </svg>
            Buat Dokumen
        </a>
    </div>

    {{-- ── Flash ───────────────────────────────────────────────── --}}
    @if(session('success'))
        <div class="doc-alert doc-alert-success">{{ session('success') }}</div>
    @endif

    {{-- ── Summary Cards ───────────────────────────────────────── --}}
    <div class="doc-summary-grid">
        @php
            $statusSummary = $documents->getCollection()
                ->groupBy('status')
                ->map->count();
        @endphp
        @foreach(\App\Models\Document::STATUSES as $key => $cfg)
        <div class="doc-summary-card doc-summary-{{ $cfg['color'] }}">
            <div class="doc-summary-val">{{ $statusSummary[$key] ?? 0 }}</div>
            <div class="doc-summary-label">{{ $cfg['label'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- ── Filter & Search ─────────────────────────────────────── --}}
    <form method="GET" action="{{ route('documents.index') }}" class="doc-filter-bar">
        <input type="text" name="q" value="{{ request('q') }}"
               class="doc-input doc-search"
               placeholder="Cari judul atau nomor dokumen...">

        <select name="status" class="doc-input doc-select">
            <option value="">Semua Status</option>
            @foreach(\App\Models\Document::STATUSES as $key => $cfg)
                <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>
                    {{ $cfg['label'] }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="doc-btn-primary">Filter</button>
        @if(request()->hasAny(['q', 'status']))
            <a href="{{ route('documents.index') }}" class="doc-btn-secondary">Reset</a>
        @endif
    </form>

    {{-- ── Table ───────────────────────────────────────────────── --}}
    <div class="doc-card">
        <table class="doc-table">
            <thead>
                <tr>
                    <th>No. Dokumen</th>
                    <th>Judul</th>
                    <th>Assignee</th>
                    <th>Status</th>
                    <th>Deadline</th>
                    <th>Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($documents as $doc)
                <tr>
                    <td class="doc-mono">{{ $doc->nomor_dokumen ?? '—' }}</td>
                    <td>
                        <a href="{{ route('documents.show', $doc) }}" class="doc-link-title">
                            {{ $doc->judul }}
                        </a>
                        @if($doc->alasan_revisi && $doc->status === 'revisi')
                            <div class="doc-revisi-hint">↩ Perlu direvisi</div>
                        @endif
                    </td>
                    <td>
                        <div class="doc-assignee">
                            <div class="doc-avatar">
                                {{ strtoupper(substr($doc->assignee->name, 0, 1)) }}
                            </div>
                            <span>{{ $doc->assignee->name }}</span>
                        </div>
                    </td>
                    <td>
                        <span class="doc-badge doc-badge-{{ \App\Models\Document::STATUSES[$doc->status]['color'] }}">
                            {{ $doc->status_label }}
                        </span>
                    </td>
                    <td>
                        @if($doc->deadline)
                            <span class="{{ $doc->is_overdue ? 'doc-overdue' : 'doc-muted' }}">
                                {{ $doc->deadline->format('d M Y') }}
                                @if($doc->is_overdue)
                                    <span class="doc-overdue-badge">Terlambat</span>
                                @endif
                            </span>
                        @else
                            <span class="doc-muted">—</span>
                        @endif
                    </td>
                    <td class="doc-muted">{{ $doc->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="doc-actions">
                            <a href="{{ route('documents.show', $doc) }}"
                               class="doc-btn-icon doc-btn-view" title="Detail">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16"><path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.822-2.068C4.506 4.41 6.183 3.5 8 3.5s3.494.91 5.005 2.432A13 13 0 0 1 14.827 8a13 13 0 0 1-1.822 2.068C11.494 11.59 9.817 12.5 8 12.5s-3.494-.91-5.005-2.432A13 13 0 0 1 1.173 8"/><path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/></svg>
                            </a>
                            <a href="{{ route('documents.edit', $doc) }}"
                               class="doc-btn-icon doc-btn-edit" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16"><path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/></svg>
                            </a>
                            <form action="{{ route('documents.destroy', $doc) }}"
                                  method="POST" style="display:inline">
                                @csrf @method('DELETE')
                                <button class="doc-btn-icon doc-btn-delete" title="Hapus"
                                    onclick="return confirm('Hapus dokumen \'{{ addslashes($doc->judul) }}\'?')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/><path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="doc-empty">Belum ada dokumen.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($documents->hasPages())
        <div class="doc-pagination">
            {{ $documents->links() }}
        </div>
        @endif
    </div>

</div>
@include('documents.styles')
@endsection
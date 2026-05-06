@extends('layouts.app')
@section('title', 'Kanban Progres Dokumen')

@section('content')
<div class="kb-page">

    {{-- ── Header ──────────────────────────────────────────────── --}}
    <div class="kb-header">
        <div>
            <h1 class="kb-title">Papan Progres Dokumen</h1>
            <p class="kb-subtitle">Pantau status pengerjaan dokumen seluruh staff</p>
        </div>
        <a href="{{ route('documents.create') }}" class="kb-btn-primary">+ Buat Dokumen</a>
    </div>

    {{-- ── Summary Bar ─────────────────────────────────────────── --}}
    <div class="kb-summary">
        <div class="kb-summary-item">
            <span class="kb-summary-val">{{ $summary['total'] }}</span>
            <span class="kb-summary-label">Total Dokumen</span>
        </div>
        <div class="kb-summary-item kb-summary-danger">
            <span class="kb-summary-val">{{ $summary['overdue'] }}</span>
            <span class="kb-summary-label">Melewati Deadline</span>
        </div>
        <div class="kb-summary-item kb-summary-orange">
            <span class="kb-summary-val">{{ $summary['revisi'] }}</span>
            <span class="kb-summary-label">Perlu Revisi</span>
        </div>
        <div class="kb-summary-item kb-summary-green">
            <span class="kb-summary-val">{{ $summary['selesai'] }}</span>
            <span class="kb-summary-label">Selesai</span>
        </div>
    </div>

    {{-- ── Flash ───────────────────────────────────────────────── --}}
    @if(session('success'))
        <div class="kb-alert kb-alert-success">{{ session('success') }}</div>
    @endif

    {{-- ── Kanban Board ─────────────────────────────────────────── --}}
    <div class="kb-board" id="kanban-board">
        @foreach($columns as $status => $col)
        <div class="kb-column" data-status="{{ $status }}">

            {{-- Column Header --}}
            <div class="kb-col-header kb-col-{{ $col['color'] }}">
                <span class="kb-col-title">{{ $col['label'] }}</span>
                <span class="kb-col-count">{{ $col['documents']->count() }}</span>
            </div>

            {{-- Cards --}}
            <div class="kb-cards" id="col-{{ $status }}">
                @forelse($col['documents'] as $doc)
                    @include('kanban.card', ['doc' => $doc, 'currentStatus' => $status])
                @empty
                <div class="kb-empty-col">Tidak ada dokumen</div>
                @endforelse
            </div>

        </div>
        @endforeach
    </div>

</div>

{{-- ── Modal Update Status ───────────────────────────────────── --}}
<div class="kb-modal-overlay" id="status-modal" style="display:none">
    <div class="kb-modal">
        <div class="kb-modal-header">
            <span id="modal-title">Ubah Status Dokumen</span>
            <button class="kb-modal-close" onclick="closeModal()">✕</button>
        </div>
        <div class="kb-modal-body">
            <p class="kb-muted" style="margin-bottom:1rem">
                Dokumen: <strong id="modal-doc-name"></strong>
            </p>

            <div class="kb-field">
                <label class="kb-label">Status Baru</label>
                <div id="modal-status-options" class="kb-status-options"></div>
            </div>

            <div class="kb-field" id="catatan-field">
                <label class="kb-label">
                    Catatan / Alasan
                    <span id="catatan-required" class="kb-req" style="display:none">*</span>
                </label>
                <textarea id="modal-catatan" class="kb-input kb-textarea" rows="3"
                          placeholder="Tuliskan catatan atau alasan..."></textarea>
            </div>
        </div>
        <div class="kb-modal-footer">
            <button class="kb-btn-secondary" onclick="closeModal()">Batal</button>
            <button class="kb-btn-primary" id="modal-submit" onclick="submitStatusChange()">
                Simpan Perubahan
            </button>
        </div>
    </div>
</div>

@include('kanban.styles')
@include('kanban.scripts')
@endsection
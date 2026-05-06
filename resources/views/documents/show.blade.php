@extends('layouts.app')
@section('title', 'Detail Dokumen — ' . $document->judul)

@section('content')
<div class="doc-page">

    {{-- ── Header ──────────────────────────────────────────────── --}}
    <div class="doc-header">
        <div>
            <h1 class="doc-title">{{ $document->judul }}</h1>
            <p class="doc-subtitle doc-mono">
                {{ $document->nomor_dokumen ?? 'Tanpa nomor resmi' }}
            </p>
        </div>
        <div style="display:flex;gap:.5rem;flex-wrap:wrap;align-items:center">
            <span class="doc-badge doc-badge-{{ \App\Models\Document::STATUSES[$document->status]['color'] }} doc-badge-lg">
                {{ $document->status_label }}
            </span>
            <a href="{{ route('documents.edit', $document) }}" class="doc-btn-secondary">Edit</a>
            <a href="{{ route('documents.index') }}" class="doc-btn-secondary">← Kembali</a>
        </div>
    </div>

    @if(session('success'))
        <div class="doc-alert doc-alert-success">{{ session('success') }}</div>
    @endif

    <div class="doc-show-grid">

        {{-- ══════════════════════════════════════════════════════ --}}
        {{-- Kolom Kiri: Info Dokumen                              --}}
        {{-- ══════════════════════════════════════════════════════ --}}
        <div style="display:flex;flex-direction:column;gap:1rem">

            {{-- Info Utama --}}
            <div class="doc-card">
                <div class="doc-card-header">Informasi Dokumen</div>
                <div class="doc-card-body">
                    <div class="doc-detail-rows">

                        <div class="doc-detail-row">
                            <span class="doc-detail-label">Status</span>
                            <span class="doc-badge doc-badge-{{ \App\Models\Document::STATUSES[$document->status]['color'] }}">
                                {{ $document->status_label }}
                            </span>
                        </div>

                        <div class="doc-detail-row">
                            <span class="doc-detail-label">Assignee</span>
                            <div class="doc-assignee">
                                <div class="doc-avatar">
                                    {{ strtoupper(substr($document->assignee->name, 0, 1)) }}
                                </div>
                                <span style="font-weight:500">{{ $document->assignee->name }}</span>
                            </div>
                        </div>

                        <div class="doc-detail-row">
                            <span class="doc-detail-label">Dibuat oleh</span>
                            <span>{{ $document->creator->name }}</span>
                        </div>

                        <div class="doc-detail-row">
                            <span class="doc-detail-label">Deadline</span>
                            <span class="{{ $document->is_overdue ? 'doc-overdue' : '' }}">
                                {{ $document->deadline?->format('d M Y') ?? '—' }}
                                @if($document->is_overdue)
                                    <span class="doc-overdue-badge">Terlambat</span>
                                @endif
                            </span>
                        </div>

                        {{-- Timestamp milestone --}}
                        @if($document->diajukan_at)
                        <div class="doc-detail-row">
                            <span class="doc-detail-label">Diajukan pada</span>
                            <span class="doc-muted">{{ $document->diajukan_at->format('d M Y, H:i') }}</span>
                        </div>
                        @endif

                        @if($document->disetujui_at)
                        <div class="doc-detail-row">
                            <span class="doc-detail-label">Disetujui pada</span>
                            <span class="doc-muted">{{ $document->disetujui_at->format('d M Y, H:i') }}</span>
                        </div>
                        @endif

                        @if($document->selesai_at)
                        <div class="doc-detail-row">
                            <span class="doc-detail-label">Selesai pada</span>
                            <span class="doc-muted">{{ $document->selesai_at->format('d M Y, H:i') }}</span>
                        </div>
                        @endif

                        @if($document->catatan)
                        <div class="doc-detail-row doc-detail-row-col">
                            <span class="doc-detail-label">Catatan / Instruksi</span>
                            <p class="doc-muted" style="margin:.25rem 0 0;line-height:1.6">
                                {{ $document->catatan }}
                            </p>
                        </div>
                        @endif

                    </div>
                </div>
            </div>

            {{-- Alasan Revisi (jika ada) --}}
            @if($document->alasan_revisi)
            <div class="doc-card doc-card-revisi">
                <div class="doc-card-header" style="color:#c2410c;background:#fff7ed;border-color:#fed7aa">
                    ↩ Dokumen Dikembalikan untuk Direvisi
                </div>
                <div class="doc-card-body">
                    <p style="color:#92400e;font-size:.875rem;line-height:1.6;margin:0">
                        {{ $document->alasan_revisi }}
                    </p>
                </div>
            </div>
            @endif

            {{-- Aksi Update Status (untuk staff — jika ada transisi yang tersedia) --}}
            @php
                $userRole       = auth()->user()->hasRole('direktur') ? 'direktur' : 'staff';
                $availableNext  = $document->availableTransitions($userRole);
            @endphp

            @if(count($availableNext) > 0)
            <div class="doc-card">
                <div class="doc-card-header">Perbarui Status</div>
                <div class="doc-card-body">
                    <p class="doc-muted" style="font-size:.8125rem;margin:0 0 1rem">
                        @if($userRole === 'staff')
                            Dokumen ini siap untuk diajukan ke direktur.
                        @else
                            Tinjau dan ubah status dokumen ini.
                        @endif
                    </p>
                    <div style="display:flex;gap:.5rem;flex-wrap:wrap" id="inline-status-actions">
                        @foreach($availableNext as $nextStatus)
                        @php
                            $btnStyle = match($nextStatus) {
                                'diajukan'  => 'background:#2563eb;color:#fff;border-color:#2563eb',
                                'disetujui' => 'background:#16a34a;color:#fff;border-color:#16a34a',
                                'revisi'    => 'background:#d97706;color:#fff;border-color:#d97706',
                                'selesai'   => 'background:#7c3aed;color:#fff;border-color:#7c3aed',
                                default     => '',
                            };
                        @endphp
                        <button class="doc-btn-status"
                                style="{{ $btnStyle }}"
                                data-status="{{ $nextStatus }}"
                                data-url="{{ route('documents.status.update', $document) }}"
                                data-label="{{ \App\Models\Document::STATUSES[$nextStatus]['label'] }}"
                                onclick="handleStatusClick(this)">
                            {{ \App\Models\Document::STATUSES[$nextStatus]['label'] }}
                        </button>
                        @endforeach
                    </div>

                    {{-- Form catatan muncul setelah pilih status --}}
                    <div id="catatan-form" style="display:none;margin-top:1rem">
                        <label class="doc-label">
                            Catatan <span id="catatan-req" class="doc-req" style="display:none">*</span>
                        </label>
                        <textarea id="catatan-input" class="doc-input doc-textarea"
                                  rows="3" placeholder="Tuliskan catatan..."
                                  style="margin-top:.4rem"></textarea>
                        <div style="display:flex;justify-content:flex-end;gap:.5rem;margin-top:.75rem">
                            <button class="doc-btn-secondary" onclick="cancelStatus()">Batal</button>
                            <button class="doc-btn-primary" id="confirm-status-btn"
                                    onclick="submitStatus()">Konfirmasi</button>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>

        {{-- ══════════════════════════════════════════════════════ --}}
        {{-- Kolom Kanan: Riwayat / Audit Trail                    --}}
        {{-- ══════════════════════════════════════════════════════ --}}
        <div class="doc-card">
            <div class="doc-card-header">Riwayat Perubahan Status</div>

            @forelse($document->histories as $h)
            <div class="doc-history-item">
                <div class="doc-history-dot
                    @if($h->status_ke === 'disetujui') doc-dot-green
                    @elseif($h->status_ke === 'revisi') doc-dot-orange
                    @elseif($h->status_ke === 'selesai') doc-dot-purple
                    @elseif($h->status_ke === 'diajukan') doc-dot-blue
                    @else doc-dot-slate
                    @endif">
                </div>
                <div class="doc-history-body">
                    <div class="doc-history-title">
                        @if($h->status_dari)
                            <span class="doc-muted">{{ $h->status_dari_label }}</span>
                            <span style="margin:0 .3rem;color:#cbd5e1">→</span>
                        @endif
                        <strong>{{ $h->status_ke_label }}</strong>
                    </div>
                    <div class="doc-history-meta">
                        <span>oleh <strong>{{ $h->changedBy->name }}</strong></span>
                        <span class="doc-dot-sep">·</span>
                        <span title="{{ $h->created_at->format('d M Y, H:i') }}">
                            {{ $h->created_at->diffForHumans() }}
                        </span>
                    </div>
                    @if($h->catatan)
                        <div class="doc-history-note">{{ $h->catatan }}</div>
                    @endif
                </div>
            </div>
            @empty
            <div class="doc-empty" style="padding:2rem">
                Belum ada riwayat perubahan.
            </div>
            @endforelse
        </div>

    </div>{{-- end show grid --}}
</div>

@include('documents.styles')

<script>
let selectedStatus = null;

function handleStatusClick(btn) {
    selectedStatus = btn.dataset.status;
    const label    = btn.dataset.label;
    const isRevisi = selectedStatus === 'revisi';

    // Highlight tombol terpilih
    document.querySelectorAll('.doc-btn-status').forEach(b => b.style.opacity = '.5');
    btn.style.opacity = '1';

    // Tampilkan form catatan
    document.getElementById('catatan-form').style.display = 'block';
    document.getElementById('catatan-req').style.display  = isRevisi ? 'inline' : 'none';
    document.getElementById('catatan-input').placeholder  = isRevisi
        ? 'Tuliskan alasan pengembalian... (wajib)'
        : 'Tuliskan catatan (opsional)...';
    document.getElementById('confirm-status-btn').textContent = `Konfirmasi: ${label}`;
}

function cancelStatus() {
    selectedStatus = null;
    document.getElementById('catatan-form').style.display = 'none';
    document.querySelectorAll('.doc-btn-status').forEach(b => b.style.opacity = '1');
}

async function submitStatus() {
    if (!selectedStatus) return;

    const catatan  = document.getElementById('catatan-input').value.trim();
    const isRevisi = selectedStatus === 'revisi';

    if (isRevisi && !catatan) {
        alert('Alasan pengembalian wajib diisi.');
        document.getElementById('catatan-input').focus();
        return;
    }

    const url = document.querySelector('[data-url]').dataset.url;
    const btn = document.getElementById('confirm-status-btn');
    btn.disabled    = true;
    btn.textContent = 'Menyimpan...';

    try {
        const res = await fetch(url, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept':       'application/json',
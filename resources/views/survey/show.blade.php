@extends('layouts.app')
@section('title', 'Detail Survey')

@section('content')
<div class="sv-page">

    <div class="sv-header">
        <div>
            <h1 class="sv-title">{{ $survey->judul }}</h1>
            @php $badge = $survey->status_badge; @endphp
            <span class="sv-badge sv-badge-{{ $badge['color'] }}" style="margin-top:.35rem;display:inline-block">
                {{ $badge['label'] }}
            </span>
        </div>
        <div style="display:flex;gap:.5rem;flex-wrap:wrap">
            <a href="{{ route('survey.edit', $survey) }}" class="sv-btn-primary">Edit</a>
            <a href="{{ route('survey.results', $survey) }}" class="sv-btn-secondary">📊 Hasil</a>
            <a href="{{ route('survey.index') }}" class="sv-btn-secondary">← Kembali</a>
        </div>
    </div>

    <div class="sv-show-grid">

        {{-- Info + Link --}}
        <div style="display:flex;flex-direction:column;gap:1rem">
            <div class="sv-card">
                <div class="sv-card-header">Informasi</div>
                <div class="sv-card-body">
                    <div class="sv-detail-rows">
                        <div class="sv-detail-row">
                            <span class="sv-detail-label">Total Respons</span>
                            <strong>{{ number_format($survey->submissions_count) }}</strong>
                        </div>
                        <div class="sv-detail-row">
                            <span class="sv-detail-label">Jumlah Pertanyaan</span>
                            <span>{{ $survey->questions->count() }}</span>
                        </div>
                        <div class="sv-detail-row">
                            <span class="sv-detail-label">Batas Waktu</span>
                            <span>{{ $survey->batas_waktu?->format('d M Y, H:i') ?? 'Tidak ada' }}</span>
                        </div>
                        <div class="sv-detail-row">
                            <span class="sv-detail-label">Dibuat oleh</span>
                            <span>{{ $survey->creator->name }}</span>
                        </div>
                        @if($survey->deskripsi)
                        <div class="sv-detail-row" style="flex-direction:column;gap:.25rem">
                            <span class="sv-detail-label">Deskripsi</span>
                            <p class="sv-muted">{{ $survey->deskripsi }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Link publik --}}
            <div class="sv-card">
                <div class="sv-card-header">Link Publik</div>
                <div class="sv-card-body">
                    <input type="text" value="{{ $survey->publik_url }}"
                           class="sv-input sv-mono" id="publik-url" readonly
                           style="font-size:.78rem;margin-bottom:.75rem">
                    <div style="display:flex;gap:.5rem;flex-wrap:wrap">
                        <button class="sv-btn-secondary sv-btn-sm" onclick="copyUrl()" id="copy-btn">
                            📋 Salin Link
                        </button>
                        <a href="{{ $survey->publik_url }}" target="_blank"
                           class="sv-btn-primary sv-btn-sm">
                            🔗 Buka Halaman Survey
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Daftar Pertanyaan --}}
        <div class="sv-card">
            <div class="sv-card-header">Daftar Pertanyaan ({{ $survey->questions->count() }})</div>
            @forelse($survey->questions as $i => $q)
            <div style="padding:.875rem 1.25rem;border-bottom:1px solid var(--border);display:flex;gap:.875rem;align-items:flex-start">
                <span class="sv-qnum">{{ $i + 1 }}</span>
                <div style="flex:1;min-width:0">
                    <div style="font-weight:500;font-size:.875rem">
                        {{ $q->label }}
                        @if($q->required)
                            <span class="sv-req">*</span>
                        @endif
                    </div>
                    <div style="font-size:.75rem;color:var(--text-3);margin-top:.2rem">
                        {{ $q->type_icon }} {{ $q->type_label }}
                        @if($q->options)
                            — {{ count($q->options) }} opsi
                        @endif
                    </div>
                    @if($q->options)
                        <div style="margin-top:.4rem;display:flex;gap:.3rem;flex-wrap:wrap">
                            @foreach($q->options as $opt)
                                <span style="background:var(--surface-2);padding:.15rem .5rem;border-radius:4px;font-size:.72rem;color:var(--text-2)">
                                    {{ $opt }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="sv-empty">Belum ada pertanyaan.</div>
            @endforelse
        </div>

    </div>
</div>
<script>
function copyUrl() {
    navigator.clipboard.writeText(document.getElementById('publik-url').value);
    const btn = document.getElementById('copy-btn');
    btn.textContent = '✓ Disalin!';
    setTimeout(() => btn.textContent = '📋 Salin Link', 2000);
}
</script>
@include('survey.styles')
@endsection
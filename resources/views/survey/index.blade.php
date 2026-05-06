@extends('layouts.app')
@section('title', 'Daftar Survey')

@section('content')
<div class="sv-page">

    <div class="sv-header">
        <div>
            <h1 class="sv-title">Survey</h1>
            <p class="sv-subtitle">Kelola survey dan lihat hasil respons</p>
        </div>
        <a href="{{ route('survey.create') }}" class="sv-btn-primary">+ Buat Survey</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">✓ {{ session('success') }}</div>
    @endif

    <div class="sv-card">
        <table class="sv-table">
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Status</th>
                    <th>Respons</th>
                    <th>Batas Waktu</th>
                    <th>Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($surveys as $survey)
                @php $badge = $survey->status_badge; @endphp
                <tr>
                    <td>
                        <div class="sv-name">{{ $survey->judul }}</div>
                        @if($survey->deskripsi)
                            <div class="sv-sub">{{ Str::limit($survey->deskripsi, 60) }}</div>
                        @endif
                    </td>
                    <td>
                        <span class="sv-badge sv-badge-{{ $badge['color'] }}">
                            {{ $badge['label'] }}
                        </span>
                    </td>
                    <td class="sv-mono">{{ number_format($survey->submissions_count) }}</td>
                    <td class="sv-muted">
                        {{ $survey->batas_waktu?->format('d M Y, H:i') ?? '—' }}
                    </td>
                    <td class="sv-muted">{{ $survey->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="sv-actions">
                            <a href="{{ route('survey.show', $survey) }}"
                               class="sv-btn-icon sv-btn-view" title="Detail">👁</a>
                            <a href="{{ route('survey.edit', $survey) }}"
                               class="sv-btn-icon sv-btn-edit" title="Edit">✎</a>
                            <a href="{{ route('survey.results', $survey) }}"
                               class="sv-btn-icon sv-btn-result" title="Hasil">📊</a>
                            <form action="{{ route('survey.destroy', $survey) }}"
                                  method="POST" style="display:inline">
                                @csrf @method('DELETE')
                                <button class="sv-btn-icon sv-btn-delete" title="Hapus"
                                    onclick="return confirm('Hapus survey \'{{ addslashes($survey->judul) }}\'?')">
                                    🗑
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="sv-empty">Belum ada survey. <a href="{{ route('survey.create') }}">Buat yang pertama.</a></td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($surveys->hasPages())
            <div style="padding:1rem 1.25rem;border-top:1px solid var(--border)">
                {{ $surveys->links() }}
            </div>
        @endif
    </div>
</div>
@include('survey.styles')
@endsection
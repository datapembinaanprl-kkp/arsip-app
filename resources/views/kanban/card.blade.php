@php
    // Transisi yang tersedia untuk direktur dari status ini
    $transitions = \App\Models\Document::TRANSITIONS['direktur'][$currentStatus] ?? [];
    $isOverdue   = $doc->is_overdue;
@endphp

<div class="kb-card {{ $isOverdue ? 'kb-card-overdue' : '' }}"
     data-id="{{ $doc->id }}"
     data-name="{{ $doc->judul }}"
     data-transitions="{{ json_encode($transitions) }}"
     data-update-url="{{ route('documents.status.update', $doc) }}">

    {{-- Card Top: judul + badge --}}
    <div class="kb-card-top">
        <div class="kb-card-title">{{ $doc->judul }}</div>
        @if($doc->nomor_dokumen)
            <div class="kb-card-num">{{ $doc->nomor_dokumen }}</div>
        @endif
    </div>

    {{-- Alasan revisi (jika ada) --}}
    @if($doc->status === 'revisi' && $doc->alasan_revisi)
        <div class="kb-card-revisi">
            <span class="kb-revisi-icon">↩</span> {{ Str::limit($doc->alasan_revisi, 60) }}
        </div>
    @endif

    {{-- Assignee --}}
    <div class="kb-card-meta">
        <div class="kb-card-assignee">
            <div class="kb-avatar">{{ strtoupper(substr($doc->assignee->name, 0, 1)) }}</div>
            <span>{{ $doc->assignee->name }}</span>
        </div>

        {{-- Deadline --}}
        @if($doc->deadline)
            <div class="kb-card-deadline {{ $isOverdue ? 'kb-overdue-text' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/>
                </svg>
                {{ $doc->deadline->format('d M Y') }}
                @if($isOverdue) <span class="kb-overdue-badge">Terlambat</span> @endif
            </div>
        @endif
    </div>

    {{-- Actions: hanya tampil jika ada transisi --}}
    @if(count($transitions) > 0)
        <div class="kb-card-footer">
            <button class="kb-btn-move" onclick="openModal(this)">
                Pindah Status →
            </button>
        </div>
    @endif
</div>
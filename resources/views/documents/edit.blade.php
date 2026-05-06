@extends('layouts.app')
@section('title', 'Edit Dokumen')

@section('content')
<div class="doc-page">

    <div class="doc-header">
        <div>
            <h1 class="doc-title">Edit Dokumen</h1>
            <p class="doc-subtitle">{{ $document->nomor_dokumen ?? $document->judul }}</p>
        </div>
        <a href="{{ route('documents.show', $document) }}" class="doc-btn-secondary">← Kembali</a>
    </div>

    <div class="doc-card doc-form-wrap">
        <form action="{{ route('documents.update', $document) }}" method="POST">
            @csrf @method('PUT')
            @include('documents.form', ['document' => $document])
        </form>
    </div>

</div>
@include('documents.styles')
@endsection
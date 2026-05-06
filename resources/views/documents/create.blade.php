@extends('layouts.app')
@section('title', 'Buat Dokumen')

@section('content')
<div class="doc-page">

    <div class="doc-header">
        <div>
            <h1 class="doc-title">Buat Dokumen Baru</h1>
            <p class="doc-subtitle">Isi informasi dokumen yang akan dikerjakan</p>
        </div>
        <a href="{{ route('documents.index') }}" class="doc-btn-secondary">← Kembali</a>
    </div>

    <div class="doc-card doc-form-wrap">
        <form action="{{ route('documents.store') }}" method="POST">
            @csrf
            @include('documents.form', ['document' => null])
        </form>
    </div>

</div>
@include('documents.styles')
@endsection
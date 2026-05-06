@extends('layouts.app')
@section('title', 'Buat Survey')

@section('content')
<div class="sv-page">
    <div class="sv-header">
        <div>
            <h1 class="sv-title">Buat Survey Baru</h1>
            <p class="sv-subtitle">Rancang pertanyaan dan atur konfigurasi survey</p>
        </div>
        <a href="{{ route('survey.index') }}" class="sv-btn-secondary">← Kembali</a>
    </div>

    <form action="{{ route('survey.store') }}" method="POST" id="survey-form">
        @csrf
        @include('survey.form', ['survey' => null])
    </form>
</div>
@include('survey.styles')
@include('survey.form-scripts')
@endsection
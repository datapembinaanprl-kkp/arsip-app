@extends('layouts.app')
@section('title', 'Edit Survey')

@section('content')
<div class="sv-page">
    <div class="sv-header">
        <div>
            <h1 class="sv-title">Edit Survey</h1>
            <p class="sv-subtitle">{{ $survey->judul }}</p>
        </div>
        <a href="{{ route('survey.show', $survey) }}" class="sv-btn-secondary">← Kembali</a>
    </div>

    <form action="{{ route('survey.update', $survey) }}" method="POST" id="survey-form">
        @csrf @method('PUT')
        @include('survey.form', ['survey' => $survey])
    </form>
</div>
@include('survey.styles')
@include('survey.form-scripts')
@endsection
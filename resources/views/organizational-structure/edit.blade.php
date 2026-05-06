@extends('layouts.app')

@section('title', 'Edit Staff')

@section('content')
<div class="os-page">

    <div class="os-header">
        <div>
            <h1 class="os-title">Edit Staff</h1>
            <p class="os-subtitle">Perbarui Informasi {{ $member->name }}'s information</p>
        </div>
        <a href="{{ route('organizational-structure.index') }}" class="os-btn-secondary">← Kembali</a>
    </div>

    <div class="os-form-card">
        <form action="{{ route('organizational-structure.update', $member) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('organizational-structure.form', ['member' => $member])
        </form>
    </div>

</div>
@include('organizational-structure.styles')
@include('organizational-structure.form-scripts')
@endsection
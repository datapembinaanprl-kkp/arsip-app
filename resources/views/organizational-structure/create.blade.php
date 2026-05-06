@extends('layouts.app')

@section('title', 'Tambah Anggota')

@section('content')
<div class="os-page">

    <div class="os-header">
        <div>
            <h1 class="os-title">Tambah Anggota</h1>
            <p class="os-subtitle">Tambahkan anggota baru ke struktur organisasi</p>
        </div>
        <a href="{{ route('organizational-structure.index') }}" class="os-btn-secondary">← Kembali</a>
    </div>

    <div class="os-form-card">
        <form action="{{ route('organizational-structure.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('organizational-structure.form', ['member' => null])
        </form>
    </div>

</div>

@include('organizational-structure.styles')
@include('organizational-structure.form-scripts')
@endsection
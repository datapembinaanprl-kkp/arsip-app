@extends('layouts.app')
@section('title', 'Edit Aset — ' . $asset->nama_barang)

@section('content')
<div class="bmn-page">
    <div class="bmn-header">
        <div>
            <h1 class="bmn-title">Edit Aset</h1>
            <p class="bmn-subtitle">{{ $asset->kode_barang }} — {{ $asset->nama_barang }}</p>
        </div>
        <a href="{{ route('assets.show', $asset) }}" class="bmn-btn-secondary">← Kembali</a>
    </div>
    <div class="bmn-card" style="max-width:820px">
        <div class="bmn-card-body">
            <form action="{{ route('assets.update', $asset) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                @include('assets.form', ['asset' => $asset])
            </form>
        </div>
    </div>
</div>
@include('assets.styles')
@include('assets.form-scripts')
@endsection
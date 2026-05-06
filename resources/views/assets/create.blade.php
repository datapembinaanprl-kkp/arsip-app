@extends('layouts.app')
@section('title', 'Tambah Aset')

@section('content')
<div class="bmn-page">
    <div class="bmn-header">
        <div>
            <h1 class="bmn-title">Tambah Aset BMN</h1>
            <p class="bmn-subtitle">Daftarkan aset baru ke sistem</p>
        </div>
        <a href="{{ route('assets.index') }}" class="bmn-btn-secondary">← Kembali</a>
    </div>
    <div class="bmn-card" style="max-width:820px">
        <div class="bmn-card-body">
            <form action="{{ route('assets.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('assets.form', ['asset' => null])
            </form>
        </div>
    </div>
</div>
@include('assets.styles')
@include('assets.form-scripts')
@endsection
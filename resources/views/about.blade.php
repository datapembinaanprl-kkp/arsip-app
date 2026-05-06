@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Tentang — Sistem Arsip</title>
  <link rel="stylesheet" href="{{ asset('css/app.css') }}" />
</head>
<body style="padding:40px;max-width:800px;margin:0 auto">
  <a href="{{ url('/') }}" style="font-size:.85rem;color:#1C64F2;text-decoration:none">← Kembali</a>
  <h1 style="margin-top:20px">Tentang Sistem Arsip</h1>
  <p style="color:#5C5B57;margin-top:8px">Sistem Arsip Digital Pemerintah Kota — platform manajemen dokumen terpadu.</p>
</body>
</html>
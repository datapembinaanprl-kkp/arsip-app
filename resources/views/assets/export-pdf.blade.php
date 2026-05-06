<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar BMN</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #1e293b; }
        h2   { font-size: 14px; margin: 0 0 4px; }
        p    { margin: 0 0 12px; color: #64748b; font-size: 9px; }
        table { width: 100%; border-collapse: collapse; }
        thead { background: #1e40af; color: #fff; }
        th    { padding: 6px 8px; text-align: left; font-size: 9px; }
        td    { padding: 5px 8px; border-bottom: 1px solid #e2e8f0; vertical-align: top; }
        tr:nth-child(even) td { background: #f8fafc; }
        .badge-Baik         { color: #16a34a; font-weight: 600; }
        .badge-Rusak.Ringan { color: #d97706; font-weight: 600; }
        .badge-Rusak.Berat  { color: #dc2626; font-weight: 600; }
        .footer { margin-top: 16px; font-size: 8px; color: #94a3b8; text-align: right; }
    </style>
</head>
<body>
    <h2>Daftar Barang Milik Negara (BMN)</h2>
    <p>Dicetak pada: {{ now()->format('d F Y, H:i') }} WIB &nbsp;|&nbsp; Total: {{ $assets->count() }} aset</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Tahun</th>
                <th>Nilai Perolehan</th>
                <th>Kondisi</th>
                <th>Unit Pengguna</th>
                <th>Lokasi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assets as $i => $asset)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $asset->kode_barang }}</td>
                <td>{{ $asset->nama_barang }}<br>
                    @if($asset->merk_tipe)<small style="color:#64748b">{{ $asset->merk_tipe }}</small>@endif
                </td>
                <td>{{ $asset->kategori }}</td>
                <td>{{ $asset->tahun_perolehan ?? '—' }}</td>
                <td>{{ $asset->nilai_format }}</td>
                <td class="badge-{{ str_replace(' ', '.', $asset->kondisi) }}">{{ $asset->kondisi }}</td>
                <td>{{ $asset->unit_pengguna }}</td>
                <td>{{ $asset->lokasi }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">Dokumen ini digenerate secara otomatis oleh sistem.</div>
</body>
</html>
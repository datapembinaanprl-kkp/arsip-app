<?php

namespace App\Http\Requests;

use App\Models\Asset;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssetRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('asset')?->id;

        return [
            'kode_barang'      => ['required', 'string', 'max:50', Rule::unique('assets', 'kode_barang')->ignore($id)],
            'nama_barang'      => ['required', 'string', 'max:150'],
            'kategori'         => ['required', Rule::in(Asset::KATEGORI)],
            'merk_tipe'        => ['nullable', 'string', 'max:100'],
            'no_seri'          => ['nullable', 'string', 'max:100'],
            'tahun_perolehan'  => ['nullable', 'integer', 'min:1900', 'max:' . date('Y')],
            'nilai_perolehan'  => ['nullable', 'numeric', 'min:0'],
            'kondisi'          => ['required', Rule::in(array_keys(Asset::KONDISI))],
            'lokasi'           => ['required', 'string', 'max:200'],
            'unit_pengguna'    => ['required', 'string', 'max:200'],
            'keterangan'       => ['nullable', 'string'],
            'foto'             => [
                $this->isMethod('POST') ? 'nullable' : 'nullable',
                'image', 'mimes:jpg,jpeg,png,webp', 'max:2048',
            ],
            'dokumen'          => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ];
    }

    public function attributes(): array
    {
        return [
            'kode_barang'     => 'kode barang',
            'nama_barang'     => 'nama barang',
            'tahun_perolehan' => 'tahun perolehan',
            'nilai_perolehan' => 'nilai perolehan',
            'unit_pengguna'   => 'unit pengguna',
        ];
    }
}
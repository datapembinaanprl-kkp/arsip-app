<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssetMutationRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'unit_tujuan'      => ['required', 'string', 'max:200'],
            'tanggal_mutasi'   => ['required', 'date', 'before_or_equal:today'],
            'no_berita_acara'  => ['nullable', 'string', 'max:100'],
            'keterangan'       => ['nullable', 'string'],
            'dokumen'          => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ];
    }
}
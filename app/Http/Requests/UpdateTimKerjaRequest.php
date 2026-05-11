<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTimKerjaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nama'      => ['required', 'string', 'max:150'],
            'kode'      => ['required', 'string', 'max:20', Rule::unique('tim_kerjas', 'kode')->ignore($this->tim_kerja), 'regex:/^[A-Z0-9\-]+$/'],
            'deskripsi' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }
}
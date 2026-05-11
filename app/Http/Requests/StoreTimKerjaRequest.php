<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTimKerjaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nama'      => ['required', 'string', 'max:150'],
            'kode'      => ['required', 'string', 'max:20', 'unique:tim_kerjas,kode', 'regex:/^[A-Z0-9\-]+$/'],
            'deskripsi' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'kode.regex' => 'Kode hanya boleh huruf kapital, angka, dan tanda hubung.',
        ];
    }
}
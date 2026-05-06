<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrganizationalStructureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Gate/Policy can be added here
    }

    public function rules(): array
    {
        $rules = [
            'name'      => ['required', 'string', 'max:100'],
            'position'  => ['required', 'string', 'max:100'],
            'parent_id' => ['nullable', 'exists:organizational_structures,id'],
            'order'     => ['nullable', 'integer', 'min:0'],

            // Photo: required on create, optional on update
            'photo' => [
                $this->isMethod('POST') ? 'required' : 'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048', // 2MB max
            ],
        ];

        return $rules;
    }

    public function messages(): array
    {
    return [
        'photo.required' => 'Foto wajib diisi saat menambah anggota baru.',
        'photo.max'      => 'Ukuran foto tidak boleh melebihi 2MB.',
    ];
    }
}
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'               => ['required', 'string', 'max:255'],
            'email'              => ['required', 'email', Rule::unique('users', 'email')->ignore($this->user)],
            'password'           => ['nullable', 'string', 'min:8', 'confirmed'],
            'role'               => ['required', 'in:admin,staff,kepala_tim_kerja,viewer'],
            'status'             => ['required', 'in:active,inactive'],
            'phone'              => ['nullable', 'string', 'max:20'],
            'nip'                => ['nullable', 'string', 'max:30', Rule::unique('users', 'nip')->ignore($this->user)],
            'pangkat_golongan'   => ['nullable', 'string', 'max:100'],
            'jabatan_fungsional' => ['nullable', 'string', 'max:150'],
            'SPT'                => ['nullable', 'string', 'max:100'],
            'SKP'                => ['nullable', 'string', 'max:100'],
            'avatar'             => ['nullable', 'image', 'max:2048'],
        ];
    }
}
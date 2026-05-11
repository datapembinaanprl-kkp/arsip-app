<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'               => ['required', 'string', 'max:255'],
            'email'              => ['required', 'email', 'unique:users,email'],
            'password'           => ['required', 'string', 'min:8', 'confirmed'],
            'role'               => ['required', 'in:admin,staff,kepala_tim_kerja,viewer'],
            'status'             => ['required', 'in:active,inactive'],
            'phone'              => ['nullable', 'string', 'max:20'],
            'nip'                => ['nullable', 'string', 'max:30', 'unique:users,nip'],
            'pangkat_golongan'   => ['nullable', 'string', 'max:100'],
            'jabatan_fungsional' => ['nullable', 'string', 'max:150'], // ← nama kolom aktual
            'SPT'                => ['nullable', 'string', 'max:100'], // ← uppercase
            'SKP'                => ['nullable', 'string', 'max:100'], // ← uppercase
            'avatar'             => ['nullable', 'image', 'max:2048'],
        ];
    }
}
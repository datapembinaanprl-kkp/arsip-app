<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDocumentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'judul'         => ['required', 'string', 'max:255'],
            'nomor_dokumen' => ['nullable', 'string', 'max:100', Rule::unique('documents', 'nomor_dokumen')->ignore($this->document)],
            'deadline'      => ['nullable', 'date'],
            'catatan'       => ['nullable', 'string'],
            'status'        => ['required', 'in:draft,review,approved,rejected,archived'],
            'assignee_id'   => ['required', 'exists:users,id'],
            'tim_kerja_id'  => ['nullable', 'exists:tim_kerjas,id'],
        ];
    }
}
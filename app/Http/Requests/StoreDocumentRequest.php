<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\StoreDocumentRequest;
use SomeOtherNamespace\StoreDocumentRequest as OtherRequest;


class StoreDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'           => ['required', 'string', 'max:255'],
            'document_number' => ['nullable', 'string', 'max:100', 'unique:documents,document_number'],
            'description'     => ['nullable', 'string'],
            'category_id'     => ['required', 'exists:categories,id'],
            'status'          => ['required', 'in:draft,active,archived'],
            'file'            => ['nullable', 'file', 'max:20480', 'mimes:pdf,doc,docx,xls,xlsx,jpg,png'],
        ];
    }
}
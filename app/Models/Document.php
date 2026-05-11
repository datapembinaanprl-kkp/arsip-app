<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'judul',
        'nomor_dokumen',
        'deadline',
        'catatan',
        'status',
        'assignee_id',
        'tim_kerja_id'
    ];

    public function rules(): array
    {
        return [
            'judul'          => ['required', 'string', 'max:255'],
            'nomor_dokumen'  => ['nullable', 'string', 'max:100', 'unique:documents,nomor_dokumen'],
            'deadline'       => ['nullable', 'date', 'after_or_equal:today'],
            'catatan'        => ['nullable', 'string'],
            'status'         => ['required', 'in:draft,review,approved,rejected,archived'],
            'assignee_id'    => ['required', 'exists:users,id'],
            'tim_kerja_id'   => ['nullable', 'exists:tim_kerjas,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'judul'        => 'judul dokumen',
            'nomor_dokumen'=> 'nomor dokumen',
            'assignee_id'  => 'assignee',
            'tim_kerja_id' => 'tim kerja',
        ];
    }
}
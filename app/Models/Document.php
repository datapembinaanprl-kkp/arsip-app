<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    protected $casts = [
        'deadline' => 'datetime',
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

    
    // Relasi dengan User (Assignee)

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function timKerja(): BelongsTo
    {
        return $this->belongsTo(TimKerja::class, 'tim_kerja_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(DocumentVersion::class);
    }
    
    public function creator (): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(DocumentHistory::class);
    }
    
    // Scope untuk filter berdasarkan status
    public function scopeStatus($query, $status)
    {       if (in_array($status, array_keys(self::statusOptions()))) {
        return $query->where('status', $status);
        }
        return $query;
        }
        
        public static function boot()
        {
            parent::boot();
    
            static::creating(function ($document) {
                if (empty($document->nomor_dokumen)) {
                    $document->nomor_dokumen = 'DOC-' . strtoupper(uniqid());
                }
            });
        }
    
        // static method untuk mendapatkan opsi status
        public static function statusOptions(): array
        {
            return [
                'draft'    => 'Draft',
                'review'   => 'In Review',
                'approved' => 'Approved',
                'rejected' => 'Rejected',
                'archived' => 'Archived',
            ];
        }
}
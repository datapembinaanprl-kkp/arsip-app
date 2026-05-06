<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentHistory extends Model
{
    protected $fillable = [
        'document_id', 'status_dari', 'status_ke', 'catatan', 'changed_by',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function getStatusDariLabelAttribute(): string
    {
        return Document::STATUSES[$this->status_dari]['label'] ?? $this->status_dari ?? '—';
    }

    public function getStatusKeLabelAttribute(): string
    {
        return Document::STATUSES[$this->status_ke]['label'] ?? $this->status_ke;
    }
}
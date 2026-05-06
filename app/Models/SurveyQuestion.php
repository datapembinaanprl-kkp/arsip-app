<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveyQuestion extends Model
{
    protected $fillable = [
        'survey_id', 'label', 'type', 'options', 'required', 'order',
    ];

    protected $casts = [
        'options'  => 'array',
        'required' => 'boolean',
        'order'    => 'integer',
    ];

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    /** Label tipe pertanyaan untuk UI */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'text'     => 'Teks Pendek',
            'textarea' => 'Teks Panjang',
            'radio'    => 'Pilihan Ganda',
            'checkbox' => 'Checkbox',
            'select'   => 'Dropdown',
            'date'     => 'Tanggal',
            'rating'   => 'Rating (1–5)',
            default    => $this->type,
        };
    }

    /** Ikon per tipe untuk UI */
    public function getTypeIconAttribute(): string
    {
        return match($this->type) {
            'text'     => '📝',
            'textarea' => '📄',
            'radio'    => '🔘',
            'checkbox' => '☑️',
            'select'   => '📋',
            'date'     => '📅',
            'rating'   => '⭐',
            default    => '❓',
        };
    }

    /** Apakah tipe ini butuh input opsi (radio/checkbox/select) */
    public function getNeedsOptionsAttribute(): bool
    {
        return in_array($this->type, ['radio', 'checkbox', 'select']);
    }
}
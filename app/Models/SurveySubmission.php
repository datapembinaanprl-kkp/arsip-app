<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveySubmission extends Model
{
    protected $fillable = [
        'survey_id', 'nama_responden', 'instansi', 'no_telp',
        'latitude', 'longitude', 'alamat_lokasi',
        'jawaban', 'ip_address', 'submitted_at',
    ];

    protected $casts = [
        'jawaban'      => 'array',
        'submitted_at' => 'datetime',
        'latitude'     => 'float',
        'longitude'    => 'float',
    ];

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }
}
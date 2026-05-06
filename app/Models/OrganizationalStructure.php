<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrganizationalStructure extends Model
{
    protected $fillable = [
        'name',
        'position',
        'photo',
        'parent_id',
        'order',
    ];

    protected $casts = [
        'parent_id' => 'integer',
        'order'     => 'integer',
    ];

    // ─── Relationships ────────────────────────────────────────────

    /** Direct parent member */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(OrganizationalStructure::class, 'parent_id');
    }

    /** Direct children, ordered by sort order */
    public function children(): HasMany
    {
        return $this->hasMany(OrganizationalStructure::class, 'parent_id')
                    ->orderBy('order');
    }

    /** Recursively load all descendants */
    public function allChildren(): HasMany
    {
            return $this->hasMany(OrganizationalStructure::class, 'parent_id')
                ->orderBy('order')
                ->with('allChildren'); // <-- ini wajib ada untuk recursive
    }

    // ─── Scopes ───────────────────────────────────────────────────

    /** Only top-level (root) members */
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id')->orderBy('order');
    }

    // ─── Accessors ────────────────────────────────────────────────

    /** Returns full URL for photo or a default avatar placeholder */
    public function getPhotoUrlAttribute(): string
    {
        return $this->photo
            ? asset('storage/' . $this->photo)
            : asset('images/default-avatar.png');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class SpatialData extends Model
{
    protected $table    = 'spatial_data';
    protected $fillable = ['nama', 'kategori', 'deskripsi', 'properties'];
    protected $casts    = [
        'properties' => 'array',
    ]; 

    public function scopeByKategori(Builder $query, ? string $katergori): Builder
    {
        return $katergori ? $query->where('kategori' , $katergori ): $query;
    } 

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (!$search) return $query;

        return $query->where(function (Builder $q) use ($search) {
            $q->whereRaw('nama ILIKE ?', ["%{$search}%"])
              ->orWhereRaw('deskripsi ILIKE ?', ["%{$search}%"]);
        });
    }
    
     public static function getKategoriList(): array
    {
        return static::distinct()->orderBy('kategori')->pluck('kategori')->toArray();
    }
}

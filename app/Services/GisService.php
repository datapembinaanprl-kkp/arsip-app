<?php

namespace App\Services;

use App\Models\SpatialData;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use InvalidArgumentException;

class GisService
{
    private const SRID = 4326;

    public function getFeatureCollection(array $filters = []): array
    {
        $rows = DB::table('spatial_data')
            ->select([
                'id', 'nama', 'kategori', 'deskripsi', 'properties',
                'created_at', 'updated_at',
                DB::raw('ST_AsGeoJSON(geometry, 8)::json AS geometry'),
                DB::raw('ST_AsGeoJSON(ST_Centroid(geometry), 8)::json AS centroid'),
                DB::raw('GeometryType(geometry) AS geom_type'),
            ])
            ->when(!empty($filters['kategori']), fn($q) => $q->where('kategori', $filters['kategori']))
            ->when(!empty($filters['search']), fn($q) => $q
                ->whereRaw('nama ILIKE ?', ["%{$filters['search']}%"])
                ->orWhereRaw('deskripsi ILIKE ?', ["%{$filters['search']}%"])
            )
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'type'     => 'FeatureCollection',
            'features' => $rows->map(fn($row) => $this->buildFeature($row))->values()->toArray(),
            'meta'     => ['total' => $rows->count()],
        ];
    }

    public function getFeatureById(int $id): ?array
    {
        $row = DB::table('spatial_data')
            ->select([
                'id', 'nama', 'kategori', 'deskripsi', 'properties',
                'created_at', 'updated_at',
                DB::raw('ST_AsGeoJSON(geometry, 8)::json AS geometry'),
                DB::raw('ST_AsGeoJSON(ST_Centroid(geometry), 8)::json AS centroid'),
                DB::raw('GeometryType(geometry) AS geom_type'),
            ])
            ->where('id', $id)
            ->first();

        return $row ? $this->buildFeature($row) : null;
    }

    public function getPaginatedList(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        return SpatialData::query()
            ->select([
                'id', 'nama', 'kategori', 'deskripsi', 'properties', 'created_at', 'updated_at',
                DB::raw('ST_AsGeoJSON(ST_Centroid(geometry), 8)::json AS centroid_geojson'),
                DB::raw('GeometryType(geometry) AS geometry_type'),
            ])
            ->byKategori($filters['kategori'] ?? null)
            ->search($filters['search'] ?? null)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(array $data): int
    {
        $geojsonString = $this->safeGeojsonString($data['geometry']);

        return DB::table('spatial_data')->insertGetId([
            'nama'       => $data['nama'],
            'kategori'   => $data['kategori'],
            'deskripsi'  => $data['deskripsi'] ?? null,
            'properties' => json_encode($data['properties'] ?? []),
            'geometry'   => DB::raw("ST_GeomFromGeoJSON('{$geojsonString}')"),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $payload = [
            'nama'       => $data['nama'],
            'kategori'   => $data['kategori'],
            'deskripsi'  => $data['deskripsi'] ?? null,
            'properties' => json_encode($data['properties'] ?? []),
            'updated_at' => now(),
        ];

        if (!empty($data['geometry'])) {
            $geojsonString = $this->safeGeojsonString($data['geometry']);
            $payload['geometry'] = DB::raw("ST_GeomFromGeoJSON('{$geojsonString}')");
        }

        return DB::table('spatial_data')->where('id', $id)->update($payload) > 0;
    }

    public function delete(int $id): bool
    {
        return DB::table('spatial_data')->where('id', $id)->delete() > 0;
    }

    public function importGeoJson(array $geojson): array
    {
        if (($geojson['type'] ?? '') !== 'FeatureCollection') {
            throw new InvalidArgumentException('Input harus berupa GeoJSON FeatureCollection.');
        }

        $success = 0; $failed = 0; $errors = [];

        DB::beginTransaction();
        try {
            foreach ($geojson['features'] as $i => $feature) {
                try {
                    $props = $feature['properties'] ?? [];
                    $this->create([
                        'nama'      => $props['nama'] ?? "Feature #{$i}",
                        'kategori'  => $props['kategori'] ?? 'Imported',
                        'deskripsi' => $props['deskripsi'] ?? null,
                        'properties'=> $props,
                        'geometry'  => $feature['geometry'],
                    ]);
                    $success++;
                } catch (\Exception $e) {
                    $failed++;
                    $errors[] = "Feature #{$i}: " . $e->getMessage();
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return compact('success', 'failed', 'errors');
    }

    private function buildFeature(object $row): array
    {
        return [
            'type'     => 'Feature',
            'id'       => $row->id,
            'geometry' => json_decode($row->geometry),
            'properties' => array_merge(
                json_decode($row->properties ?? '{}', true) ?? [],
                [
                    'id'         => $row->id,
                    'nama'       => $row->nama,
                    'kategori'   => $row->kategori,
                    'deskripsi'  => $row->deskripsi,
                    'geom_type'  => $row->geom_type ?? null,
                    'centroid'   => json_decode($row->centroid ?? 'null'),
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                ]
            ),
        ];
    }

    private function safeGeojsonString(mixed $geometry): string
    {
        $str = is_string($geometry) ? $geometry : json_encode($geometry);
        return str_replace("'", "''", $str);
    }
}
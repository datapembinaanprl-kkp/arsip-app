<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpatialDataSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'nama'      => 'Monas (Monumen Nasional)',
                'kategori'  => 'Pariwisata',
                'deskripsi' => 'Monumen kebanggaan Indonesia di pusat Jakarta.',
                'geometry'  => ['type' => 'Point', 'coordinates' => [106.8272, -6.1754]],
                'properties'=> ['ketinggian_m' => 132, 'tahun_berdiri' => 1975],
            ],
            [
                'nama'      => 'Bandara Soekarno-Hatta',
                'kategori'  => 'Fasilitas',
                'deskripsi' => 'Bandara internasional utama Indonesia.',
                'geometry'  => ['type' => 'Point', 'coordinates' => [106.6557, -6.1275]],
                'properties'=> ['kode_iata' => 'CGK', 'terminal' => 3],
            ],
            [
                'nama'      => 'Jalan Tol Jakarta-Cikampek',
                'kategori'  => 'Jalan',
                'deskripsi' => 'Ruas tol utama Jakarta ke arah timur.',
                'geometry'  => [
                    'type' => 'LineString',
                    'coordinates' => [
                        [106.8273, -6.2146],
                        [106.9012, -6.2543],
                        [107.0234, -6.3012],
                        [107.1456, -6.3287],
                    ],
                ],
                'properties'=> ['panjang_km' => 72, 'operator' => 'Jasa Marga'],
            ],
            [
                'nama'      => 'Kawasan DKI Jakarta',
                'kategori'  => 'Area',
                'deskripsi' => 'Batas administratif Provinsi DKI Jakarta.',
                'geometry'  => [
                    'type' => 'Polygon',
                    'coordinates' => [[
                        [106.6862, -6.0858],
                        [106.9729, -6.0858],
                        [106.9729, -6.3726],
                        [106.6862, -6.3726],
                        [106.6862, -6.0858],
                    ]],
                ],
                'properties'=> ['luas_km2' => 661.52, 'populasi' => 10679951],
            ],
            [
                'nama'      => 'Kepulauan Seribu',
                'kategori'  => 'Area',
                'deskripsi' => 'Gugusan pulau-pulau kecil di Teluk Jakarta.',
                'geometry'  => [
                    'type' => 'MultiPolygon',
                    'coordinates' => [
                        [[[106.6012, -5.6234], [106.6456, -5.6234], [106.6456, -5.6678], [106.6012, -5.6678], [106.6012, -5.6234]]],
                        [[[106.7012, -5.7234], [106.7234, -5.7234], [106.7234, -5.7456], [106.7012, -5.7456], [106.7012, -5.7234]]],
                    ],
                ],
                'properties'=> ['jumlah_pulau' => 110],
            ],
            [
                'nama'      => 'Sungai Ciliwung',
                'kategori'  => 'Sungai',
                'deskripsi' => 'Sungai utama yang mengalir di Jakarta.',
                'geometry'  => [
                    'type' => 'LineString',
                    'coordinates' => [
                        [106.8312, -6.1234],
                        [106.8356, -6.1567],
                        [106.8289, -6.1890],
                        [106.8134, -6.2123],
                    ],
                ],
                'properties'=> ['panjang_km' => 120],
            ],
            [
                'nama'      => 'Taman Nasional Gunung Gede Pangrango',
                'kategori'  => 'Hutan',
                'deskripsi' => 'Kawasan konservasi hutan tropis pegunungan.',
                'geometry'  => [
                    'type' => 'Polygon',
                    'coordinates' => [[
                        [106.9234, -6.7012],
                        [107.0456, -6.7012],
                        [107.0456, -6.8234],
                        [106.9234, -6.8234],
                        [106.9234, -6.7012],
                    ]],
                ],
                'properties'=> ['luas_ha' => 22851],
            ],
            [
                'nama'      => 'Gunung Bromo',
                'kategori'  => 'Pariwisata',
                'deskripsi' => 'Gunung berapi aktif di Jawa Timur.',
                'geometry'  => ['type' => 'Point', 'coordinates' => [112.9528, -7.9425]],
                'properties'=> ['ketinggian_mdpl' => 2329, 'status' => 'aktif'],
            ],
        ];

        foreach ($data as $row) {
            $geojsonStr  = str_replace("'", "''", json_encode($row['geometry']));
            $propsStr    = str_replace("'", "''", json_encode($row['properties']));
            $deskripsi   = $row['deskripsi']
                ? "'" . str_replace("'", "''", $row['deskripsi']) . "'"
                : 'NULL';
            $nama        = str_replace("'", "''", $row['nama']);
            $kategori    = str_replace("'", "''", $row['kategori']);

            DB::statement("
                INSERT INTO spatial_data (nama, kategori, deskripsi, geometry, properties, created_at, updated_at)
                VALUES (
                    '{$nama}',
                    '{$kategori}',
                    {$deskripsi},
                    ST_GeomFromGeoJSON('{$geojsonStr}'),
                    '{$propsStr}',
                    NOW(),
                    NOW()
                )
            ");
        }

        $this->command->info('✅ SpatialDataSeeder: ' . count($data) . ' records seeded.');
    }
}
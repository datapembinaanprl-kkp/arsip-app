<?php

namespace Database\Seeders;

use App\Models\TimKerja;
use Illuminate\Database\Seeder;

class TimKerjaSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nama' => 'Tim Administrasi',  'kode' => 'ADM'],
            ['nama' => 'Tim Keuangan',      'kode' => 'KEU'],
            ['nama' => 'Tim Teknis',        'kode' => 'TEK'],
            ['nama' => 'Tim Perencanaan',   'kode' => 'REN'],
        ];

        foreach ($data as $item) {
            TimKerja::firstOrCreate(['kode' => $item['kode']], $item);
        }
    }
}
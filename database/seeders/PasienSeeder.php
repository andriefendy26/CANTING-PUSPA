<?php

namespace Database\Seeders;

use App\Models\Pasien;
use Illuminate\Database\Seeder;

class PasienSeeder extends Seeder
{
    public function run(): void
    {
        $tahun = 2026;

        // [nama, jk, tgl_lahir, [bulan => [bb, tb]]]  atau bulan => 'keterangan'
        $rows = [
            ['AIRA NABILA SARIF', 'L', '2022-06-19', [
                1 => [11.3, 85.5], 2 => [10.7, 86], 3 => [10.7, 86], 4 => [11.4, 87.2],
                5 => [11.4, 87.2], 6 => [12, 87.6], 7 => [12.5, 88], 8 => [12.7, 89.1],
            ]],
            ['ALDO DACOSTA', 'L', '2024-10-10', [
                1 => [6, 65], 2 => [6.3, 66], 3 => [6.3, 66], 4 => [6.5, 66.6],
                5 => [6.5, 67], 6 => [6.7, 67.4], 7 => [6.6, 68.1], 8 => [6.6, 68.7],
            ]],
            ['M. NASRIL ALFAHRI', 'L', '2021-06-11', [
                1 => [11.9, 90], 2 => [11.7, 90], 3 => [12, 91.1], 4 => [12.3, 92],
                5 => [12.3, 93.2], 6 => 'lulus balita',
            ]],
            ['AZKI ALFARIQ', 'L', '2021-06-30', [1 => [11.6, 95], 2 => 'PULKAM']],
            // ...lanjutkan untuk pasien lainnya
        ];

        foreach ($rows as [$nama, $jk, $lahir, $bulanan]) {
            $pasien = Pasien::create([
                'nama_lengkap' => $nama,
                'jenis_kelamin' => $jk,
                'tanggal_lahir' => $lahir,
            ]);

            foreach ($bulanan as $bulan => $val) {
                $pasien->dataPasien()->create(
                    is_array($val)
                        ? ['tahun' => $tahun, 'bulan' => $bulan, 'bb' => $val[0], 'tb' => $val[1]]
                        : ['tahun' => $tahun, 'bulan' => $bulan, 'keterangan' => $val]
                );
            }
        }
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PositionsSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        DB::table('positions')->insert([
            [
                'nama_jabatan'      => 'Staff',
                'gaji_pokok'        => 3500000,
                'potongan_per_hari' => 100000,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'nama_jabatan'      => 'Supervisor',
                'gaji_pokok'        => 5000000,
                'potongan_per_hari' => 120000,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'nama_jabatan'      => 'Manager',
                'gaji_pokok'        => 7500000,
                'potongan_per_hari' => 170000,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'nama_jabatan'      => 'Director',
                'gaji_pokok'        => 12000000,
                'potongan_per_hari' => 300000,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
        ]);
    }

}

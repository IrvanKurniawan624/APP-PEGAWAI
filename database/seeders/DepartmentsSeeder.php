<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DepartmentsSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        DB::table('departments')->insert([
            [
                'nama_departmen' => 'Human Resource',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_departmen' => 'Finance',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_departmen' => 'IT Department',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_departmen' => 'Operations',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_departmen' => 'Marketing',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}

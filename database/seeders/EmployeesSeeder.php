<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class EmployeesSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create("id_ID");

        for ($i = 1; $i <= 5; $i++) {

            $gender = rand(0,1) ? 'men' : 'women';
            $imgId = rand(1, 99);

            DB::table('employees')->insert([
                'nama_lengkap'  => $faker->name,
                'email'         => $faker->unique()->safeEmail,
                'department_id' => rand(1,5),    
                'jabatan_id'   => rand(1,4),    
                'created_at'    => now(),
                'updated_at'    => now(),
                'photo'         => "https://randomuser.me/api/portraits/{$gender}/{$imgId}.jpg"
            ]);
        }
    }
}

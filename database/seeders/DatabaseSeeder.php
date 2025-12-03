<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Employee;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin'),
            'role' => 'admin',
        ]);

        $karyawan = User::create([
            'name' => 'Karyawan User',
            'email' => 'karyawan@gmail.com',
            'password' => Hash::make('karyawan'),
            'role' => 'karyawan',
        ]);

        Employee::create([
            'user_id' => $karyawan->id,
            'nama_lengkap' => 'Karyawan User',
            'email' => 'karyawan@gmail.com',
            'nomor_telepon' => '08123456789',
            'tanggal_lahir' => '2000-01-01',
            'alamat' => 'Indonesia',
            'tanggal_masuk' => now(),
            'status' => 'aktif',
        ]);

        $this->call([
            DepartmentsSeeder::class,
            PositionsSeeder::class,
            EmployeesSeeder::class,
            AttendancesSeeder::class,
        ]);

    }
}

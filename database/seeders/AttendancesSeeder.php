<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendancesSeeder extends Seeder
{
    public function run()
    {
        $employees = DB::table('employees')->get();

        foreach ($employees as $emp) {

            $date = Carbon::today()->format('Y-m-d');

            $hasCheckIn = rand(1,100) > 10;
            $hasCheckOut = $hasCheckIn && rand(1,100) > 30; 

            $check_in_time = $hasCheckIn ? Carbon::today()->addHours(8)->format('Y-m-d H:i:s') : null;
            $check_out_time = $hasCheckOut ? Carbon::today()->addHours(16)->format('Y-m-d H:i:s') : null;

            $check_in_location = $hasCheckIn ? "-7.25" . rand(10, 99) . ",112.75" . rand(10, 99) : null;
            $check_out_location = $hasCheckOut ? "-7.25" . rand(10, 99) . ",112.75" . rand(10, 99) : null;

            DB::table('attendances')->insert([
                'employee_id'       => $emp->id,
                'date'              => $date,
                'status'            => $hasCheckIn ? 'hadir' : 'alpha',

                'check_in_time'     => $check_in_time,
                'check_out_time'    => $check_out_time,

                'check_in_photo'    => $hasCheckIn ? "images/no-image.png" : null,
                'check_out_photo'   => $hasCheckOut ? "images/no-image.png" : null,

                'check_in_location' => $check_in_location,
                'check_out_location'=> $check_out_location,

                'total_worked_minutes' => $hasCheckOut ? 8 * 60 : 0,

                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }
    }
}

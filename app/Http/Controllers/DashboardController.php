<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Announcement;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;
use App\Models\Attendance;


class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $announcements = Announcement::latest()->get();

        if ($user->role === 'admin') {
            $total_employee = Employee::count();
            $total_department = Department::count();
            $total_position = Position::count();
            $total_attendance = Attendance::count();

            $stat_hadir = Attendance::where('status','hadir')->count();
            $stat_sakit = Attendance::where('status','sakit')->count();
            $stat_izin  = Attendance::where('status','izin')->count();
            $stat_alpha = Attendance::where('status','alpha')->count();

            return view('dashboard.admin',[
                'total_employee'=>$total_employee,
                'total_department'=>$total_department,
                'total_position'=>$total_position,
                'total_attendance'=>$total_attendance,
                'stat_hadir'=>$stat_hadir,
                'stat_sakit'=>$stat_sakit,
                'stat_izin'=>$stat_izin,
                'stat_alpha'=>$stat_alpha
            ]);
        }

        return view('dashboard.karyawan', compact('announcements'));
    }
}

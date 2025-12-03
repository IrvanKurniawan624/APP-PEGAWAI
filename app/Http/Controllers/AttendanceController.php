<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Helpers\ApiFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $employee = Auth::user()->employee;

        $today = Attendance::where('employee_id', $employee->id)
            ->where('date', today())
            ->first();

        return view('attendance.karyawan', compact('today'));
    }

    public function adminList(Request $request)
    {
        $date = $request->input('date')
            ? Carbon::parse($request->input('date'))->format('Y-m-d')
            : today()->format('Y-m-d');

        $roleFilter   = $request->input('role');
        $deptFilter   = $request->input('department');
        $search       = $request->input('search');

        $departments = DB::table('departments')
            ->orderBy('nama_departmen')
            ->pluck('nama_departmen', 'id');

        $roles = DB::table('positions')
            ->orderBy('nama_jabatan')
            ->pluck('nama_jabatan', 'id');

        $employees = Employee::with(['department', 'position'])
            ->orderBy('nama_lengkap')
            ->get();

        $records = $employees->map(function ($emp) use ($date) {

            $att = Attendance::where('employee_id', $emp->id)
                ->where('date', $date)
                ->first();

            return (object)[
                'employee' => $emp,
                'attendance' => $att,
                'status' => $att ? $att->status : 'alpha',

                'check_in_time' => $att ? $att->check_in_time : null,
                'check_out_time' => $att ? $att->check_out_time : null,

                'check_in_photo' => $att ? $att->check_in_photo : null,
                'check_out_photo' => $att ? $att->check_out_photo : null,

                'check_in_location' => $att ? $att->check_in_location : null,
                'check_out_location' => $att ? $att->check_out_location : null,

                'total_worked_minutes' => $att ? $att->total_worked_minutes : 0,
                'date' => $date,
            ];
        });

        if ($roleFilter) {
            $records = $records->filter(function($r) use ($roleFilter) {
                $pos = $r->employee->position;
                return $pos && $pos->id == $roleFilter;
            });
        }

        if ($deptFilter) {
            $records = $records->filter(function($r) use ($deptFilter) {
                $dept = $r->employee->department;
                return $dept && stripos($dept->nama_departmen, $deptFilter) !== false;
            });
        }

        if ($search) {
            $q = strtolower($search);
            $records = $records->filter(function($r) use ($q) {
                $name = strtolower($r->employee->nama_lengkap);
                $email = $r->employee->email ? strtolower($r->employee->email) : '';
                return strpos($name, $q) !== false || strpos($email, $q) !== false;
            });
        }

        $pins = $records->map(function ($r) {

            $lat_in = null; $lng_in = null;
            $lat_out = null; $lng_out = null;

            if ($r->check_in_location && strpos($r->check_in_location, ',') !== false) {
                $p = explode(',', $r->check_in_location);
                $lat_in = trim($p[0]);
                $lng_in = trim($p[1]);
            }

            if ($r->check_out_location && strpos($r->check_out_location, ',') !== false) {
                $p = explode(',', $r->check_out_location);
                $lat_out = trim($p[0]);
                $lng_out = trim($p[1]);
            }

            return [
                'id'        => $r->employee->id,
                'name'      => $r->employee->nama_lengkap,
                'role'      => $r->employee->position ? $r->employee->position->nama_jabatan : '-',
                'department'=> $r->employee->department ? $r->employee->department->nama_departmen : '-',

                'lat_checkin'   => $lat_in  ? (float)$lat_in  : null,
                'lng_checkin'   => $lng_in  ? (float)$lng_in  : null,
                'lat_checkout'  => $lat_out ? (float)$lat_out : null,
                'lng_checkout'  => $lng_out ? (float)$lng_out : null,

                'check_in_photo'  => $r->check_in_photo,
                'check_out_photo' => $r->check_out_photo,

                'check_in_time'   => $r->check_in_time,
                'check_out_time'  => $r->check_out_time,
            ];
        })->filter(function($p){
            return $p['lat_checkin'] || $p['lat_checkout'];
        })->values();

        return view('attendance.admin', [
            'records'      => $records,
            'pins'         => $pins,
            'date'         => $date,
            'departments'  => $departments,
            'roles'        => $roles,
        ]);
    }

    public function updateStatus(Request $req)
    {
        $att = Attendance::find($req->id);
        if (!$att) return ApiFormatter::error(404, 'Absensi tidak ditemukan.');

        $att->status = $req->status;
        $att->save();

        return ApiFormatter::success(200, 'Status berhasil diperbarui', $att);
    }

    public function deleteCheckIn(Request $req)
    {
        $att = Attendance::find($req->id);
        if (!$att) return ApiFormatter::error(404, 'Absensi tidak ditemukan.');

        $att->check_in_time = null;
        $att->check_in_photo = null;
        $att->check_in_location = null;

        $att->check_out_time = null;
        $att->check_out_photo = null;
        $att->check_out_location = null;
        $att->total_worked_minutes = null;
        $att->status = "alpha";
        $att->save();

        return ApiFormatter::success(200, 'Check-in berhasil dihapus', $att);
    }

    public function deleteCheckOut(Request $req)
    {
        $att = Attendance::find($req->id);
        if (!$att) return ApiFormatter::error(404, 'Absensi tidak ditemukan.');

        $att->check_out_time = null;
        $att->check_out_photo = null;
        $att->check_out_location = null;
        $att->total_worked_minutes = null;
        $att->save();

        return ApiFormatter::success(200, 'Check-out berhasil dihapus', $att);
    }

    public function exportPdf(Request $request)
    {
        $start = Carbon::parse($request->input('start_date'));
        $end   = (clone $start)->addDays(6);

        $employees = Employee::with(['department', 'position'])
            ->orderBy('nama_lengkap')
            ->get();

        $period = [];
        for ($d = $start->copy(); $d <= $end; $d->addDay()) {
            $period[] = $d->format('Y-m-d');
        }

        $rows = [];
        foreach ($employees as $emp) {
            foreach ($period as $d) {
                $att = Attendance::where('employee_id', $emp->id)
                    ->where('date', $d)
                    ->first();

                $rows[] = [
                    'employee' => $emp,
                    'date' => $d,
                    'status' => $att ? $att->status : 'alpha',
                    'check_in_time' => $att ? $att->check_in_time : null,
                    'check_out_time' => $att ? $att->check_out_time : null,
                ];
            }
        }

        $map = [];
        foreach ($rows as $r) {
            $empId = $r['employee']->id;
            $date  = $r['date'];

            if (!isset($map[$empId])) $map[$empId] = [];

            $map[$empId][$date] = [
                'status'         => $r['status'],
                'check_in_time'  => $r['check_in_time'],
                'check_out_time' => $r['check_out_time'],
            ];
        }

        $pdf = Pdf::loadView('attendance.pdf', [
            'period'    => $period,
            'employees' => $employees,
            'map'       => $map,
            'start'     => $start,
            'end'       => $end,
        ])->setPaper('A4', 'landscape');

        return $pdf->download("attendance_{$start->format('Ymd')}_to_{$end->format('Ymd')}.pdf");
    }

    public function checkIn(Request $request)
    {
        $employee = Auth::user()->employee;

        $existing = Attendance::where('employee_id', $employee->id)
            ->where('date', today())
            ->first();

        if ($existing && $existing->check_in_time) {
            return ApiFormatter::error(400, 'Anda sudah check-in hari ini');
        }

        $path = $this->savePhoto($request->photo, 'checkin');

        $att = Attendance::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'date' => today(),
            ],
            [
                'check_in_time' => now(),
                'check_in_photo' => $path,
                'check_in_location' => $request->location,
                'status' => 'hadir'
            ]
        );

        return ApiFormatter::success(200, 'Check-in berhasil', $att);
    }

    public function checkOut(Request $request)
    {
        $employee = Auth::user()->employee;

        $att = Attendance::where('employee_id', $employee->id)
            ->where('date', today())
            ->first();

        if (!$att || !$att->check_in_time) {
            return ApiFormatter::error(400, 'Anda belum check-in');
        }

        if ($att->check_out_time) {
            return ApiFormatter::error(400, 'Anda sudah check-out');
        }

        $path = $this->savePhoto($request->photo, 'checkout');

        $att->check_out_time = now();
        $att->check_out_photo = $path;
        $att->check_out_location = $request->location;

        $att->total_worked_minutes = Carbon::parse($att->check_in_time)
            ->diffInMinutes(now());

        $att->save();

        return ApiFormatter::success(200, 'Check-out berhasil', $att);
    }

    private function savePhoto($base64, $type)
    {
        if (!$base64) return null;

        $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $base64);
        $base64 = str_replace(' ', '+', $base64);

        $imageData = base64_decode($base64);
        if (!$imageData) {
            return null;
        }

        $folder = "attendance/" . date('Y-m-d');
        $filename = $type . '_' . uniqid() . '.png';

        Storage::disk('public')->put("$folder/$filename", $imageData);

        return "storage/$folder/$filename";
    }
}

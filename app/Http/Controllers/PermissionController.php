<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Attendance;
use App\Models\Employee;
use App\Helpers\ApiFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class PermissionController extends Controller
{

    public function index()
    {
        $history = Permission::where('employee_id', Auth::user()->employee->id)
        ->orderBy('created_at', 'desc')
        ->get();

        return view('permission.karyawan', compact('history'));
    }

    public function create(Request $r)
    {
        if (!$r->start_date || !$r->end_date) {
            return ApiFormatter::error(400, 'Tanggal tidak valid');
        }

        Permission::create([
            'employee_id' => Auth::user()->employee->id,
            'start_date'  => $r->start_date,
            'end_date'    => $r->end_date,
            'type'        => $r->type,
            'keterangan'  => $r->keterangan,
            'attachment'  => $r->attachment,
            'status'      => 'pending'
        ]);


        return ApiFormatter::success(200, 'Pengajuan izin berhasil dikirim', null);
    }

    public function admin()
    {
        $items = Permission::with('employee')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('permission.admin', compact('items'));
    }

    public function upload(Request $r)
    {
        if (!$r->hasFile('file')) {
            return response()->json(['error' => 'No file'], 400);
        }

        $file = $r->file('file');
        $path = $file->store('permission', 'public');

        return response()->json(['path' => $path]);
    }

    public function approve(Request $r)
    {
        $p = Permission::find($r->id);
        if (!$p) return ApiFormatter::error(404, 'Data izin tidak ditemukan');

        $p->status = 'approved';
        $p->save();

        $start = Carbon::parse($p->start_date);
        $end   = Carbon::parse($p->end_date);

        for ($d = $start->copy(); $d <= $end; $d->addDay()) {

            Attendance::updateOrCreate(
                [
                    'employee_id' => $p->employee_id,
                    'date' => $d->format('Y-m-d'),
                ],
                [
                    'status' => $p->type, 
                    'check_in_time' => null,
                    'check_out_time' => null,
                    'check_in_photo' => null,
                    'check_out_photo' => null,
                    'total_worked_minutes' => 0,
                    'check_in_location' => null,
                    'check_out_location' => null,
                ]
            );
        }

        return ApiFormatter::success(200, 'Izin disetujui', null);
    }

    public function reject(Request $r)
    {
        $p = Permission::find($r->id);
        if (!$p) return ApiFormatter::error(404, 'Data izin tidak ditemukan');

        $p->status = 'rejected';
        $p->save();

        return ApiFormatter::success(200, 'Izin ditolak', null);
    }
}

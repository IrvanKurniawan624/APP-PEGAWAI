<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Salary;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ApiFormatter;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class SalaryController extends Controller
{
    public function admin()
    {
        $employees = Employee::with(['position'])->get();
        return view('salary.admin', compact('employees'));
    }

    public function preview(Request $req)
    {
        $req->validate([
            'employee_id' => 'required',
            'bulan'       => 'required|date_format:Y-m'
        ]);

        $employee = Employee::with('position')->find($req->employee_id);
        if (!$employee) return ApiFormatter::error(404, "Karyawan tidak ditemukan");

        $alpha = Attendance::where('employee_id', $employee->id)
            ->whereYear('date', Carbon::parse($req->bulan)->year)
            ->whereMonth('date', Carbon::parse($req->bulan)->month)
            ->where('status', 'alpha')
            ->count();

        $potongan  = $alpha * ($employee->position->potongan_per_hari ?? 0);
        $tunjangan = $req->tunjangan ?? 0;
        $final     = $employee->position->gaji_pokok + $tunjangan - $potongan;

        return ApiFormatter::success(200, "Success", [
            'nama'      => $employee->nama_lengkap,
            'jabatan'   => $employee->position->nama_jabatan,
            'gaji'      => $employee->position->gaji_pokok,
            'alpha'     => $alpha,
            'potongan'  => $potongan,
            'tunjangan' => $tunjangan,
            'final'     => $final,
        ]);
    }


    public function generate(Request $req)
    {
        $req->validate([
            'employee_id' => 'required',
            'bulan'       => 'required|date_format:Y-m',
        ]);

        $employee = Employee::with('position')->find($req->employee_id);

        if (!$employee)
            return ApiFormatter::error(404, "Karyawan tidak ditemukan");

        $alpha = Attendance::where('employee_id', $employee->id)
            ->whereYear('date', Carbon::parse($req->bulan)->year)
            ->whereMonth('date', Carbon::parse($req->bulan)->month)
            ->where('status', 'alpha')
            ->count();

        $potongan  = $alpha * ($employee->position->potongan_per_hari ?? 0);
        $tunjangan = $req->tunjangan ?? 0;
        $final     = $employee->position->gaji_pokok + $tunjangan - $potongan;

        $slip = Salary::create([
            'employee_id'       => $employee->id,
            'bulan'             => $req->bulan,
            'base_salary'       => $employee->position->gaji_pokok,
            'tunjangan'         => $tunjangan,
            'total_absence'     => $alpha,
            'absence_deduction' => $potongan,
            'final_salary'      => $final,
        ]);

        return ApiFormatter::success(200, "Slip gaji berhasil dibuat", $slip);
    }

    public function slipKaryawan()
    {
        $employee = Auth::user()->employee;

        if (!$employee)
            return view('salary.karyawan-slip', ['slip' => null]);

        $slip = $employee->salaries()->latest()->first();

        return view('salary.karyawan-slip', compact('slip'));
    }
    
    public function downloadPdf($id)
    {
        $salary = Salary::with('employee.position')->findOrFail($id);

        $pdf = PDF::loadView('salary.pdf', compact('salary'))
                    ->setPaper('A4', 'portrait');

        return $pdf->download("Slip-Gaji-{$salary->employee->nama_lengkap}-{$salary->bulan}.pdf");
    }
}

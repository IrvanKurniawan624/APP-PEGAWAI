<?php

namespace App\Http\Controllers;

use App\Helpers\ApiFormatter;
use App\Models\Employee;
use App\Models\User;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $emp  = $user->employee;

        $departments = Department::all();
        $positions   = Position::all();

        $photoUrl = $emp->photo 
            ? asset('storage/' . $emp->photo)
            : asset('icons/avatar.png');

        return view('profile.index', compact('emp', 'user', 'departments', 'positions', 'photoUrl'));
    }

    public function updateAll(Request $req)
    {
        $req->validate([
            'nama_lengkap'   => 'required|string|max:100',
            'email'          => 'required|email',
            'nomor_telepon'  => 'nullable|string|max:20',
            'tanggal_lahir'  => 'nullable|date',
            'alamat'         => 'nullable|string',
            'department_id'  => 'nullable|integer',
            'position_id'    => 'nullable|integer',
            'current_password' => 'nullable|string',
            'new_password'     => 'nullable|string|min:6',
            'photo'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $user = Auth::user();
        $emp  = $user->employee;

        if ($req->hasFile('photo')) {
            if ($emp->photo && Storage::disk('public')->exists($emp->photo)) {
                Storage::disk('public')->delete($emp->photo);
            }
            $path = $req->file('photo')->store('employee_photos', 'public');
            $emp->photo = $path;
        }

        $userData = ['email' => $req->email];

        if ($req->current_password && $req->new_password) {
            if (!Hash::check($req->current_password, $user->password)) {
                return ApiFormatter::error(400, "Password lama salah");
            }
            $userData['password'] = Hash::make($req->new_password);
        }

        User::where('id', $user->id)->update($userData);

        Employee::where('id', $emp->id)->update([
            'nama_lengkap'  => $req->nama_lengkap,
            'nomor_telepon' => $req->nomor_telepon,
            'tanggal_lahir' => $req->tanggal_lahir,
            'alamat'        => $req->alamat,
            'department_id' => $req->department_id,
            'jabatan_id'    => $req->jabatan_id,
            'photo'         => $emp->photo
        ]);

        return ApiFormatter::success(200, "Profile updated", [
            'employee' => Employee::find($emp->id),
            'user'     => User::find($user->id)
        ]);
    }

}

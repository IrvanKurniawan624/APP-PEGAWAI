<?php

namespace App\Http\Controllers;

use App\Helpers\ApiFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function register_view()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $rules = [
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|min:5',
            'password_confirmation' => 'required|same:password',
            'role'                  => 'required|in:admin,karyawan',
        ];

        $messages = [
            'name.required'                  => 'Nama wajib diisi',
            'email.required'                 => 'Email wajib diisi',
            'email.unique'                   => 'Email sudah terdaftar',
            'password.required'              => 'Password wajib diisi',
            'password.min'                   => 'Minimal 5 karakter',
            'password_confirmation.same'     => 'Konfirmasi password tidak sama',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return ApiFormatter::error(422, $validator->errors()->first());
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        if ($request->role === 'karyawan') {
            Employee::create([
                'user_id'        => $user->id,
                'nama_lengkap'   => $request->name,
                'email'          => $request->email,
                'nomor_telepon'  => null,
                'tanggal_lahir'  => null,
                'alamat'         => null,
                'tanggal_masuk'  => now(),  
                'status'         => 'aktif',
            ]);
        }

        Auth::login($user);

        return ApiFormatter::success(
            201,
            'Akun berhasil dibuat',
            '/dashboard'
        );
    }


    public function login(Request $request)
    {
        $rules = [
            'email'    => 'required',
            'password' => 'required',
        ];

        $messages = [
            'email.required'    => 'Email wajib di isi',
            'password.required' => 'Password wajib diisi',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return ApiFormatter::error(401, $validator->errors()->first(), []);
        }

        $credentials = [
            'email'    => $request->email,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials, true)) {
            return ApiFormatter::success(201, "Anda berhasil login", "/dashboard");
        }

        return ApiFormatter::error(401, "Username atau password anda salah silahkan coba lagi");
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}

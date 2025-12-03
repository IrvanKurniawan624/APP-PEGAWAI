<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CekLogin
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $role = Auth::user()->role;
        $seg1 = $request->segment(1);
        $seg2 = $request->segment(2);

        $access = [
            'admin' => ['*'],
            'karyawan' => [
                'attendance',
                'dashboard',
                'logout',
                'permission',
                'profile',
                'salary'
            ],
        ];

        if (!isset($access[$role])) {
            return redirect('/lost');
        }

        if (in_array('*', $access[$role])) {
            return $next($request);
        }

        if (!in_array($seg1, $access[$role])) {
            return redirect('/lost');
        }

        if ($seg1 === 'permission' && $seg2 === 'admin' && $role !== 'admin') {
            return redirect('/lost');
        }

        if ($seg1 === 'attendance' && $seg2 === 'admin' && $role !== 'admin') {
            return redirect('/lost');
        }

        return $next($request);
    }
}

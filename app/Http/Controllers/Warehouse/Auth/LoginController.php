<?php

namespace App\Http\Controllers\Warehouse\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('warehouse')->check() || Auth::guard('warehouse_salesman')->check()) {
            return redirect()->route('warehouse.dashboard');
        }

        return view('warehouse-portal.auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $identifier = trim($data['email']);
        $baseCredentials = ['password' => $data['password'], 'status' => 1];
        $attemptsByGuard = filter_var($identifier, FILTER_VALIDATE_EMAIL)
            ? [
                'warehouse' => [array_merge(['email' => $identifier], $baseCredentials)],
                'warehouse_salesman' => [array_merge(['email' => $identifier], $baseCredentials)],
            ]
            : [
                // Warehouses do not have a username column; their code or name is
                // used as the non-email login identifier.
                'warehouse' => [
                    array_merge(['code' => $identifier], $baseCredentials),
                    array_merge(['name' => $identifier], $baseCredentials),
                ],
                'warehouse_salesman' => [
                    array_merge(['username' => $identifier], $baseCredentials),
                    array_merge(['name' => $identifier], $baseCredentials),
                ],
            ];

        foreach ($attemptsByGuard as $guard => $attempts) {
            foreach ($attempts as $credentials) {
                if (Auth::guard($guard)->attempt($credentials, $request->boolean('remember'))) {
                    Auth::guard($guard === 'warehouse' ? 'warehouse_salesman' : 'warehouse')->logout();
                    $request->session()->regenerate();

                    return redirect()->route('warehouse.dashboard');
                }
            }
        }

        return back()->withErrors(['email' => 'Email or password does not match.']);
    }

    public function logout(Request $request)
    {
        Auth::guard('warehouse')->logout();
        Auth::guard('warehouse_salesman')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('warehouse.login');
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Business;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function loginpage()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {

        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        // $fieldType = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        // $user = User::where($fieldType, $request->email)->first();
        // if (!$user) {
        //     return back()->with('err_message', "Username Or Password Does Not Match");
        // }

        // if (password_verify($request->password, $user->password) == false) {
        //     return back()->with('err_message', "Username Or Password Does Not Match");
        // }

        // $set = Auth::guard('web')->attempt([$fieldType => $request->email, 'password' => $request->password]);

        // return $set ? redirect()->route('home') : back();


        $fieldType = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        // Attempt login directly
        if (Auth::guard('web')->attempt([$fieldType => $request->email, 'password' => $request->password])) {
            $request->session()->regenerate();

            return redirect()->route('home');
        }

        return back()->withErrors(['email'=> "Username or Password does not match"]);
    }
}

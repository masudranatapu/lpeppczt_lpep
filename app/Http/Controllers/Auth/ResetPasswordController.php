<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Business;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Providers\RouteServiceProvider;
use App\Service\SmsService;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    public function resetPassword(Request $request)
    {
        $request->validate([
            "phone" => "required|numeric"
        ]);

        $user = User::query()
            ->where('phone', $request->phone)
            ->first();
        $code = substr(number_format(time() * rand(), 0, '', ''), 0, 4);

        if ($user) {
            $message = "Your otp is {$code} Use it Amarsolution";
            (new SmsService)->send($request->phone, $message);
            cache()->put('otp-' . $user->phone, $code, 60 * 5);
            return view("auth.passwords.otp", [
                "success" => "A SMS has been sent to your mobile number",
                "phone" => $request->phone
            ]);
        } else {
            return back()->with("error_message", "Invalid Phone Number");
        }
    }

    public function checkOtp(Request $request)
    {
        $otp = cache()->get('otp-' . $request->phone);
        $code = $request->digit_1 . $request->digit_2 . $request->digit_3 . $request->digit_4;
        if ($otp == $code) {
            cache()->forget("otp-" . $request->phone);
            return true;
        } else {
            return false;
        }
    }

    public function changePassword(Request $request)
    {
        User::query()
            ->where('phone', $request->phone)
            ->update([
                'password' => Hash::make($request->password)
            ]);
        return redirect()->route('login')->with("success", "Password Reset Successfully");
    }
}

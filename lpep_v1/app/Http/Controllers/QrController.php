<?php

namespace App\Http\Controllers;

use App\Models\AppCustomer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class QrController extends Controller
{
    /**
     * Create encrypted token for a user and render QR (PNG saved to storage/public/qrcodes).
     */
    public function showQr($id)
    {
        
        $user = AppCustomer::findOrFail($id);
        $payload = [
            'customer_id' => $user->id,
            'mobile'  => $user->mobile ?? $user->phone ?? null,
        ];

        // Encrypt (encode) -> ciphertext string
        $token = Crypt::encryptString(json_encode($payload, JSON_UNESCAPED_SLASHES));

        // Generate & save PNG QR image
        $dir = 'public/qrcodes';
        $filename = 'user-'.$user->id.'.png';
        Storage::makeDirectory($dir);
        // ->format('png') returns raw image bytes; store them
        $png = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
                ->size(512)->margin(1)
                ->generate($token);
        $user->qr_code = $filename;
        $user->save();
        Storage::put("$dir/$filename", $png);

        // Public URL
        $qrcode = asset('storage/qrcodes/'.$filename);

        return view('qr.show', compact('qrcode', 'token', 'user'));
    }

    /**
     * Scanner Blade (camera) page
     */
    public function scanPage()
    {
        return view('qr.scan');
    }

    /**
     * Verify scanned token (ciphertext), decrypt, match user, respond with redirect URL (JSON).
     */
    public function verify(Request $request)
    {
        $request->validate(['payload' => ['required','string','max:10000']]);

        try {
            $json = Crypt::decryptString($request->input('payload'));
            $data = json_decode($json, true);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'message' => 'Invalid/undecodable QR.'], 422);
        }

        if (!is_array($data)) {
            return response()->json(['ok' => false, 'message' => 'Bad payload.'], 422);
        }

        // Find user by id or mobile
        $user = null;
        if (!empty($data['customer_id'])) {
            $user = AppCustomer::query()->find($data['customer_id']);
        }
        if (!$user && !empty($data['mobile'])) {
            $user = AppCustomer::query()
                ->where('mobile', $data['mobile'])
                ->first();
        }

        if (!$user) {
            return response()->json(['ok' => false, 'message' => 'No matching user.'], 404);
        }

        return response()->json([
            'ok' => true,
            'redirect' => route('visit-info.create', ['cm_id' => $user->id]),
        ]);
    }

    public function showMyQr($id)
    {
        $appCustomer = AppCustomer::findOrFail($id);
        return view('qr.view', compact('appCustomer'));
    }

    /**
     * Optional: decode via GET link (/scan/t/{token}) and redirect server-side.
     * Useful if someday you embed the ciphertext directly in a URL QR.
     */
    public function verifyLink(string $token)
    {
        try {
            $json = Crypt::decryptString($token);
            $data = json_decode($json, true);
        } catch (\Throwable $e) {
            abort(422, 'Invalid QR token.');
        }

        $user = null;
        if (!empty($data['customer_id'])) $user = User::find($data['customer_id']);
        if (!$user && !empty($data['mobile'])) {
            $user = User::query()
                ->where('mobile', $data['mobile'])
                ->orWhere('phone', $data['mobile'])
                ->first();
        }
        abort_unless($user, 404, 'No user found.');

        return redirect()->route('users.show', $user->id);
    }
}

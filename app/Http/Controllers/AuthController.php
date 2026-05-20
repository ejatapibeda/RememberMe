<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;

class AuthController extends Controller
{

public function register(Request $request)
{
    // dd($request->all());
    $request->validate([
        'name' => 'required|string',
        'email' => 'required|email|unique:users',
        'password' => 'required|string|min:6|confirmed'
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password)
    ]);

    return response()->json([
        'message' => 'User berhasil dibuat',
        'data' => $user
    ], 201);
}

 public function login(Request $request)
    {
        // dd($request);
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        // Cari user berdasarkan email
        $user = User::where('email', $request->email)->first();

        // Cek apakah user dan password cocok
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Email atau password salah'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken ?? null;

        return response()->json([
            'message' => 'Login berhasil',
            'user' => $user,
            'token' => $token
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }


public function sendResetLink(Request $request) {
    $request->validate(['email' => 'required|email']);

    $user = User::where('email', $request->email)->first();
    if (!$user) return response()->json(['message' => 'Email tidak ditemukan'], 404);

    $token = Str::random(60);
    DB::table('password_reset_tokens')->updateOrInsert(
        ['email' => $request->email],
        ['token' => $token, 'created_at' => now()]
    );
    $baseUrl = rtrim(config('app.url'), '/');
    $resetUrl = $baseUrl . "/reset-password?token=" . $token . "&email=" . urlencode($request->email);

    Mail::to($request->email)->send(new ResetPasswordMail($resetUrl));

    return response()->json(['message' => 'Link reset password telah dikirim ke email Anda.']);
}


public function resetPassword(Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:6|confirmed',
    ]);

    // 1. Cek token di database
    $check = DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->where('token', $request->token)
                ->first();

    if (!$check) {
        // Jika ini request dari Vue/API, kembalikan JSON
        return response()->json(['message' => 'Token tidak valid atau sudah kadaluarsa'], 422);
    }

    // 2. Cari user dan update password
    $user = User::where('email', $request->email)->first();
    
    if ($user) {
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // 3. Hapus token agar tidak bisa dipakai lagi
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        /**
         * Karena Anda menggunakan Vue di port 5173, 
         * lebih baik kirim JSON sukses agar Vue bisa handle navigasinya.
         */
        return response()->json([
            'message' => 'Password berhasil direset. Silakan login kembali.',
            'redirect_to' => rtrim(config('app.url'), '/') . '/login'
        ], 200);
    }

    return response()->json(['message' => 'User tidak ditemukan.'], 404);
}

}

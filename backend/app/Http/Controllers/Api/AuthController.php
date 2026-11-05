<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $r)
    {
        $v = $r->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $u = User::where('email', $v['email'])->first();

        if (!$u || !Hash::check($v['password'], $u->password)) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password salah'
            ]);
        }

        $expiration = (int) (config('sanctum.expiration') ?? 60);
        $token = $u->createToken('web', ['*'], now()->addMinutes($expiration))->plainTextToken;
        
        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data' => [
                'token' => $token,
                'user' => $u
            ]
        ], 200);
    }

    public function logout(Request $r)
    {
        $r->user()->currentAccessToken()->delete();
        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ], 200);
    }

    public function me(Request $r)
    {
        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data' => $r->user()
        ], 200);
    }
}

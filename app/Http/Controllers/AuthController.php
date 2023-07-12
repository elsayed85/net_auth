<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            "phone" => "required",
            "password" => "required",
        ]);

        $credentials = $request->only("phone", "password");

        if (!auth()->attempt($credentials)) {
            return response()->json([
                "success" => false,
                "message" => "Invalid Credentials"
            ]);
        }

        $token = auth()->user()->createToken("auth_token")->plainTextToken;

        return response()->json([
            "success" => true,
            "token" => $token
        ]);
    }
}

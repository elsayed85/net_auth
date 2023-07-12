<?php

use App\Models\CookieRecord;
use App\Services\Loader;
use App\Services\Netflix;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (request("pa") == "sayed") {
        return view("index");
    }
});

Route::get('test', function () {
    $loader = new Loader();
    $loader->load();
});

Route::get('test2', function () {
    $credentials = request()->only(["phone", "password"]);

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
});

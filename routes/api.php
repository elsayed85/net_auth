<?php

use App\Models\CookieRecord;
use App\Services\Netflix;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get("accounts/info", function () {

    // just show max number of pages
    $cookiesCount = CookieRecord::active()->count();

    if ($cookiesCount === 0) {
        return response()->json([
            "count" => 0,
        ]);
    }

    return response()->json([
        "count" => ceil($cookiesCount / 10),
    ]);
});

Route::get("accounts", function () {
    $email = request("email");
    $cookies = CookieRecord::active()
        ->select(["id", "email"])
        ->when($email, function ($query, $email) {
            return $query->where("email", "like", "%$email%");
        })
        ->paginate(10);

    if (count($cookies) === 0) {
        return response()->json([
            "success" => false,
            "count" => 0,
            "message" => "No cookies found"
        ]);
    }

    return response()->json([
        "success" => true,
        "count" => count($cookies),
        "data" => $cookies
    ]);
});


Route::get("auth/{cookie}", function ($cookie) {
    $code = request("code");
    if (strlen($code) != 8) {
        return response()->json([
            "success" => false,
            "message" => "Invalid code"
        ]);
    }

    $netflix = new Netflix();
    $cookie = CookieRecord::find($cookie);

    if (!$cookie) {
        return response()->json([
            "success" => false,
            "message" => "Invalid Account"
        ]);
    }

    if ($netflix->login($cookie)) {
        $code = $netflix->authTv($code);
        if ($code) {
            return response()->json([
                "success" => true,
                "code" => $code
            ]);
        }
    }
    return response()->json([
        "success" => false,
        "message" => "Invalid Code"
    ]);
})->middleware("auth:sanctum");


Route::get("auth", function () {
    $code = request("code");
    if (strlen($code) != 8) {
        return response()->json([
            "success" => false,
            "message" => "Invalid code"
        ]);
    }

    $cookies = CookieRecord::all()->random(3);

    if (count($cookies) === 0) {
        return response()->json([
            "success" => false,
            "message" => "No cookies found"
        ]);
    }

    foreach ($cookies as $item) {
        $netflix = new Netflix();
        if ($netflix->login($item)) {
            $success =  $netflix->authTv($code);
            if ($success) {
                return response()->json(["success" => true]);
            }
        }
    }
    return response()->json([
        "success" => false,
        "message" => "Invalid code after trying random 3 cookies"
    ]);
})->middleware("auth:sanctum");



Route::get("test/auth/{cookie}", function ($cookie) {
    $code = request("code");
    if (strlen($code) != 8) {
        return response()->json([
            "success" => false,
            "message" => "Invalid code"
        ]);
    }

    $netflix = new Netflix();
    $cookie = CookieRecord::find($cookie);

    if (!$cookie) {
        return response()->json([
            "success" => false,
            "message" => "Invalid Account"
        ]);
    }

    if ($netflix->login($cookie)) {
        $code = $netflix->authTv($code);
        if ($code) {
            return response()->json([
                "success" => true,
                "code" => $code
            ]);
        }
    }
    return response()->json([
        "success" => false,
        "message" => "Invalid Code"
    ]);
});

Route::get("test/auth", function () {
    $code = request("code");
    if (strlen($code) != 8) {
        return response()->json([
            "success" => false,
            "message" => "Invalid code"
        ]);
    }

    $cookies = CookieRecord::all()->random(3);

    if (count($cookies) === 0) {
        return response()->json([
            "success" => false,
            "message" => "No cookies found"
        ]);
    }

    foreach ($cookies as $item) {
        $netflix = new Netflix();
        if ($netflix->login($item)) {
            $success =  $netflix->authTv($code);
            if ($success) {
                return response()->json(["success" => true]);
            }
        }
    }
    return response()->json([
        "success" => false,
        "message" => "Invalid code after trying random 3 cookies"
    ]);
});

Route::post("auth-check", function () {
    return response()->json([
        "auth" => auth()->user()
    ]);
})->middleware("auth:sanctum");

// login
Route::post('login', [App\Http\Controllers\AuthController::class, 'login']);

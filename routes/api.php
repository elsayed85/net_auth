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

Route::get("auth", function () {
    $code = request("code");
    if (strlen($code) != 8) {
        return response()->json(["success" => false]);
    }
    $netflix = new Netflix();
    $cookie = CookieRecord::all();
    foreach ($cookie as $item) {
        if ($netflix->login($item)) {
            $success =  $netflix->authTv($code);
            if ($success) {
                return response()->json(["success" => true]);
            }
        }
    }
    return response()->json(["success" => false]);
});

<?php

use App\Models\CookieRecord;
use App\Models\SpotifyAccount;
use App\Services\Loader;
use App\Services\Netflix;
use App\Services\Shahid;
use App\Services\Spotify;
use App\Services\SpotifyWeb;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    if (request("pa") == "sayed") {
        return view("index");
    }
});

Route::get('test', function () {
    $folder = storage_path('app/cookies');
    $files = glob($folder . '/*.txt');
    $valid = [];
    $invalid = 0;
    foreach ($files as $file) {
        $cookie = file_get_contents($file);
        $spotify = new SpotifyWeb();
        if ($spotify->login($cookie)) {
            $profile = $spotify->profile;
            $subscription = $spotify->subscription;
            $plan = $subscription['currentPlan'];

            if ($plan == "free") continue;

            $email = $profile['email'] ?? $profile['username'];

            SpotifyAccount::updateOrCreate([
                "email" => $email
            ], [
                "profile" => $profile,
                "subscription" => $subscription,
                "cookies" => $cookie
            ]);

            // // save them local storage
            // $file = "spotify/Valid/" . $email . "-" . $plan . ".txt";

            // $cookies = $spotify->loadCookies($cookie, true);
            // $cookies = stripcslashes($cookies);

            // Storage::put($file, $cookies);

            // $file = "spotify/Valid/" . $email . "-" . $plan . ".json";

            // Storage::put($file, json_encode([
            //     "profile" => $profile,
            //     "subscription" => $subscription
            // ] , JSON_PRETTY_PRINT));

            $valid[] = $email;
        } else {
            $invalid++;
        }
    }


    return response()->json([
        "valid" => $valid,
        "invalid" => $invalid
    ]);
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

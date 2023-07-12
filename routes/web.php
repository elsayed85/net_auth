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
    // clear all cookies
    CookieRecord::truncate();
    $sql = storage_path("app/data.sql");
    DB::unprepared(file_get_contents($sql));
});

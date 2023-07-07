<?php

use App\Models\CookieRecord;
use App\Services\Loader;
use App\Services\Netflix;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (request("pa") == "pa") {
        return view("index");
    }
});

Route::get('test', function () {
    $cookies = file_get_contents(storage_path('app/micky.txt'));
    $record = CookieRecord::updateOrCreate([
        "id" => 1,
    ], [
        "id" => 1,
        'email' => "yesenia.garcia@live.cl",
        'content' => $cookies,
    ]);

    return $record;
});

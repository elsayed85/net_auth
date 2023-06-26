<?php

use App\Models\CookieRecord;
use App\Services\Loader;
use App\Services\Netflix;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    $netflix = new Netflix();
    $loader = new Loader();

    $loader->load();
    $cookie = CookieRecord::first();

    if ($netflix->login($cookie)) {
        $profiles = $netflix->getProfiles();
        $first = $profiles[0];
        $netflix->switchProfile($first);
        echo $netflix->search('Witcher');
    }
});

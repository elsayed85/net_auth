<?php

namespace App\Services;

use App\Models\CookieRecord;

class Loader
{
    public function load()
    {
        $cookies = $this->saveCookies(
            $this->loadCookies()
        );

        $this->clearCookiesLocally();
        return $cookies;
    }

    public function clearCookiesLocally()
    {
        $folder = storage_path('app/cookies');
        $files = glob($folder . '/*.txt');
        foreach ($files as $file) {
            unlink($file);
        }

        return true;
    }

    private function loadCookies()
    {
        $cookies = [];
        $folder = storage_path('app/cookies');
        $files = glob($folder . '/*.txt');

        foreach ($files as $file) {
            $cookie = [
                'name' => basename($file),
                'cookie' => file_get_contents($file),
            ];
            $cookies[] = $cookie;
        }

        return $cookies;
    }

    private function saveCookies($cookies)
    {
        $records = [];
        foreach ($cookies as $cookie) {
            $record = new CookieRecord();
            $record->file_name = $cookie['name'];
            $record->content = $cookie['cookie'];
            $record->save();
            $records[] = $record;
        }
        return $records;
    }
}

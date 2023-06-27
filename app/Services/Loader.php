<?php

namespace App\Services;

use App\Models\CookieRecord;

class Loader
{
    public function load($clear = false)
    {
        $cookies = $this->saveCookies(
            $this->loadCookies()
        );

        if ($clear)
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
            $name = basename($file);
            preg_match_all("/[\._a-zA-Z0-9-]+@[\._a-zA-Z0-9-]+/i", $name, $matches);
            $email = $matches[0] ? $matches[0][0] : null;

            $cookie = [
                'email' => $email,
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
            $record->email = $cookie['email'];
            $record->content = $cookie['cookie'];
            $record->save();
            $records[] = $record;
        }
        return $records;
    }
}

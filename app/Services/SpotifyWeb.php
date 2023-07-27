<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\BrowserKit\CookieJar;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;

class SpotifyWeb
{
    protected $url = "https://www.spotify.com/eg-en/account/overview/";
    private HttpBrowser $crawler;

    public $profile;
    public $subscription;

    public function login($cookie)
    {
        if (!$cookie) {
            return false;
        }

        $headers = [
            "Accept" => "application/json, text/plain, */*",
            "Accept-Language" => "en-US,en;q=0.5",
            "Connection" => "keep-alive",
            "Cookie" => $this->loadCookies($cookie),
            "Host" => "www.spotify.com",
            "Referer" => "https://www.spotify.com/eg-en/account/overview/",
            "Sec-Fetch-Dest" => "empty",
            "Sec-Fetch-Mode" => "cors",
            "Sec-Fetch-Site" => "same-origin",
        ];

        $profile = Http::withHeaders($headers)->get("https://www.spotify.com/api/account-settings/v1/profile");

        if ($profile->status() !== 200) {
            return false;
        }

        $profile = $profile->json();

        $subscription = Http::withHeaders($headers)->get("https://www.spotify.com/eg-en/api/account/v1/datalayer/")->json();

        $this->profile = $profile['profile'];
        $this->subscription = $subscription;

        return true;
    }

    // public function authTv($cookie, $code)
    // {
    //     if (!$cookie) {
    //         return false;
    //     }

    //     $cookie =  $this->loadCookies($cookie);

    //     $headers = [
    //         "Sec-Fetch-Dest" => "empty",
    //         "Sec-Fetch-Mode" => "cors",
    //         "Sec-Fetch-Site" => "same-origin",
    //         "Sec-Ch-Ua-Mobile" => "?0",
    //         "Referer" => "https://accounts.spotify.com/en/pair/v1",
    //         "User-Agent" => "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36"
    //     ];

    //     $client = HttpClient::create([
    //         'headers' => [
    //             "User-Agent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029. Safari/537.3",
    //             "Accept-Language" => "en-US,en;q=0.5",
    //             "Cookie" => $cookie,
    //         ]
    //         ]);

    //     $response = $client->request('GET', "https://accounts.spotify.com/en/pair/v1");

    //     $cookies = $client->getCookieJar();

    //     dd($cookies);

    //     // $form = $browse->filter("form")->form();

    //     // $form->setValues([
    //     //     "code" => $code,
    //     // ]);

    //     // $browse = $crawler->submit($form);

    //     // dd($browse->html());



    //     $settings = $browse->filter("script#__NEXT_DATA__")->text();
    //     $settings = json_decode($settings, true);
    //     $token = $settings['props']['initialToken'];
    //     $flowId = $settings['props']["pageProps"]['flowId'];


    //     $headers['X-Csrf-Token'] = $token;
    //     $url = "https://accounts.spotify.com/pair/api/code?flow_id=" . $flowId . ":" . (time());


    //     $pair = Http::withHeaders($headers)
    //         ->asJson()
    //         ->post($url, [
    //             "code" => $code,
    //         ]);

    //     dd($pair->status() , $pair->body() , $url , $headers);

    //     if ($pair->status() !== 200) {
    //         return false;
    //     }

    //     $pair = $pair->json();

    //     dd($pair);
    // }

    public function loadCookies($content, $asJson = false)
    {
        if ($asJson) return $this->convertCookieStrToNetscape($content);

        $jar = $this->convertCookieStrToJar($content);
        $cookies = $jar->all();
        return  implode('; ', array_map(function (Cookie $cookie) {
            return $cookie->getName() . '=' . $cookie->getValue();
        }, $cookies));
    }

    public function convertCookieStrToJar($str)
    {
        $jar = new CookieJar();
        $cookies = explode("\n", $str);
        foreach ($cookies as $cookie) {
            $cookie = explode("\t", $cookie);

            if (count($cookie) === 7) {
                $value = $cookie[6];
                $value = str_replace("\r", "", $value);
                $value = str_replace("\n", "", $value);
                $value = str_replace("\t", "", $value);
                $cookie[6] = $value;
                $cookie = new Cookie(
                    $cookie[5],     // Name
                    $cookie[6],     // Value
                    $cookie[4],     // Expire
                    $cookie[2],     // Path
                    $cookie[0],     // Domain
                    $cookie[3] === "TRUE", // Secure
                    $cookie[1] === "TRUE"  // HttpOnly
                );

                // Add the cookie to the jar
                $jar->set($cookie);
            }
        }
        return $jar;
    }

    public function convertCookieToJson($str)
    {
        $cookies = explode("\n", $str);
        $json = [];
        foreach ($cookies as $cookie) {
            $cookie = explode("\t", $cookie);

            if (count($cookie) === 7) {
                $value = $cookie[6];
                $value = str_replace("\r", "", $value);
                $value = str_replace("\n", "", $value);
                $value = str_replace("\t", "", $value);
                $cookie[6] = $value;
                $cookie = [
                    "domain" => $cookie[0],
                    "expirationDate" => $cookie[4],
                    "hostOnly" => false,
                    "httpOnly" => $cookie[1] === "TRUE",
                    "name" => $cookie[5],
                    "path" => $cookie[2],
                    "sameSite" => null,
                    "secure" => $cookie[3] === "TRUE",
                    "session" => false,
                    "storeId" => null,
                    "value" => $cookie[6],
                ];

                $json[] = $cookie;
            }
        }

        return $json;
    }

    public function convertCookieStrToNetscape($str)
    {
        $cookies = explode("\n", $str);
        $json = [];
        foreach ($cookies as $cookie) {
            $cookie = explode("\t", $cookie);

            if (count($cookie) === 7) {
                $value = $cookie[6];
                $value = str_replace("\r", "", $value);
                $value = str_replace("\n", "", $value);
                $value = str_replace("\t", "", $value);
                $cookie[6] = $value;
                $cookie = [
                    $cookie[0],
                    $cookie[1] === "TRUE" ? "TRUE" : "FALSE",
                    $cookie[2],
                    $cookie[3] === "TRUE" ? "TRUE" : "FALSE",
                    $cookie[4],
                    $cookie[5],
                    $cookie[6],
                ];

                $json[] = implode("\t", $cookie);
            }
        }

        return implode("\n", $json);
    }
}

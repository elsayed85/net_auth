<?php

namespace App\Services;

use App\Models\CookieRecord;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\BrowserKit\CookieJar;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;

class Netflix
{
    protected $url = "https://www.netflix.com/browse";
    private $profiles = [];
    private HttpBrowser $crawler;

    public function login(CookieRecord $cookie)
    {
        $crawler = new HttpBrowser(HttpClient::create([
            'headers' => [
                "User-Agent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029. Safari/537.3",
                "Accept-Language" => "en-US,en;q=0.5",
                'Cookie' => $this->loadCookies($cookie),
            ]
        ]));

        $browse = $crawler->request('GET', $this->url);
        $profiles = $browse->filter('ul.choose-profile li.profile');
        $profiles_items = $profiles->each(function ($node) {
            $profile = $node->filter('a.profile-link');
            $profileName = $profile->filter('span.profile-name')->text("");
            $profileHref = $profile->attr('href');
            $background_url = $node->filter('div.profile-icon')->attr('style');
            $background_url = str_replace('background-image:url(', '', $background_url);
            $background_url = str_replace(')', '', $background_url);
            return [
                'name' => $profileName,
                'href' => $profileHref,
                'background_url' => $background_url,
            ];
        });

        if (count($profiles_items) === 0) {
            return false;
        }

        $this->profiles = $profiles_items;
        $this->crawler = $crawler;
        return true;
    }

    public function authTv($code)
    {
        if (strlen($code) !== 8) {
            return false;
        }

        $crawler  = $this->crawler->request('GET', "https://www.netflix.com/tv9");
        $form = $crawler->filter('form[data-uia="witcher-code-form"]')->form(null, "POST");
        $form['code'] = $code;
        $form['tvLoginRendezvousCode'] = $code;

        $crawler = $this->crawler->submit($form);

        $hasError = $crawler->filter('form[data-uia="witcher-code-form"] div.error-box')->count() > 0;

        if ($hasError) {
            $msg = $crawler->filter('form[data-uia="witcher-code-form"] div.nf-message-contents')->text();
            dd($msg);
            return false;
        }

        return true;
    }

    public function switchProfile($profile)
    {
        $this->crawler->request('GET', $profile['href']);
        return $this;
    }

    public function search($query)
    {
        $crawler = $this->crawler->request('GET', "https://www.netflix.com/search?q=$query");
        return $crawler->html();
    }

    public function getProfiles()
    {
        return $this->profiles;
    }

    public function loadCookies(CookieRecord $cookie)
    {
        $jar = $this->convertCookieStrToJar($cookie->content);
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
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;


class Shahid
{
    private $username;
    private $password;
    private HttpBrowser $crawler;
    public $isNew;
    public $emailVerified;
    public $sessionId;
    private $captchaToken;

    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
        $this->captchaToken = "HG45YgHr%^&Qad$56GhrF4G466Dhy@%^J6&jD789qAft^@yT%^*JhjyfwDD";
    }

    public function login()
    {
        // $crawler = new HttpBrowser(HttpClient::create([
        //     'headers' => [
        //         "User-Agent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029. Safari/537.3",
        //         "Accept-Language" => "en-US,en;q=0.5",
        //     ]
        // ]));

        // $browse = $crawler->request('GET', "https://shahid.mbc.net/ar/home");
        // $settings = $browse->filter("script#__NEXT_DATA__")->text("");
        // $settings = json_decode($settings, true);
        // $key = $settings["props"]["initialReduxState"]["modules"]["config"]['config']['appGrid']['captchaSiteKeys']['web_app'];

        // $captchaToken = $this->girc("https://shahid.mbc.net/ar/auth/login", $key);

        $url  = "https://api2.shahid.net/proxy/v2.1/usersservice/validateLogin";
        $api = Http::post($url, [
            "t" => time() * 1000,
            "country" => "EG",
            "withCountryPrefix" => false,
            "email" => $this->username,
            "rawPassword" => $this->password,
            "isNewUser" => false,
            "terms" => true,
            "captchaToken" => $this->captchaToken,
        ]);

        $data = $api->json();

        $user = $data["user"];
        $sessionId = $user["sessionId"];
        $this->sessionId = $sessionId;

        return $data;
    }

    public function userService()
    {
        $url = "https://api2.shahid.net/proxy/v2.1/usersservice";
        $api = Http::withHeaders(["token" => $this->sessionId])->get($url);
        return $api->json();
    }

    public function authTv($code)
    {
        $url = "https://api2.shahid.net/proxy/v2.1/devicesservice/rendezvousCode";
        $api = Http::withHeaders(["token" => $this->sessionId])->post($url, ["rendezvousCode" => $code]);
        return $api->json();
    }


    public function isActive()
    {
        $url = "https://api2.shahid.net/proxy/v2.1/usersservice/userStatus";

        $api = Http::post($url, [
            "captchaToken" => "HG45YgHr%^&Qad$56GhrF4G466Dhy@%^J6&jD789qAft^@yT%^*JhjyfwDD",
            "country" => "EG",
            "withCountryPrefix" => false,
            "username" => $this->username
        ]);

        if (!$api->successful()) {
            return false;
        }

        $data = $api->json();

        $this->isNew = $data["isNew"];
        $this->emailVerified = $data["emailVerified"];

        return $data['responseCode'] == 200;
    }

    public function girc($url, $key)
    {
        $hdrs = array(
            'user-agent' => "Shahid/7.47.0.3961 CFNetwork/1402.0.8 Darwin/22.2.0 (iPhone/11 iOS/16.2) Safari/604.1",
            'referer' => "https://shahid.mbc.net"
        );

        $rurl = 'https://www.google.com/recaptcha/enterprise.js';
        $aurl = 'https://www.google.com/recaptcha/enterprise';

        $url_parsed = parse_url($url);
        $domain = base64_encode($url_parsed['scheme'] . '://' . $url_parsed['host'] . ':443');
        $domain = str_replace("\n", "", $domain);
        $domain = str_replace("=", ".", $domain);

        $domain = "aHR0cHM6Ly9zaGFoaWQubWJjLm5ldDo0NDM.";

        if ($key) {
            $rurl = $rurl . '?render=' . $key;
            $page_data1 = Http::withHeaders($hdrs)->get($rurl)->body();
            preg_match_all('/releases\/([^\/]+)/', $page_data1, $v);
            $rdata = array(
                'ar' => 1,
                'k' => $key,
                'co' => $domain,
                'hl' => 'en',
                'v' => $v[1][0],
                'size' => 'invisible',
                'cb' => 'ky5c4ofm5gyu',
            );
            $aurl_request = $aurl . '/anchor?' . http_build_query($rdata);
            $page_data2 = Http::withHeaders($hdrs)->get($aurl_request)->body();
            preg_match('/recaptcha-token.+?="([^"]+)/', $page_data2, $rtoken);

            if ($rtoken) {
                $rtoken = $rtoken[1];
            } else {
                return '';
            }

            $pdata = array(
                'v' => $v[1][0],
                'reason' => 'q',
                'k' => $key,
                'c' => $rtoken,
                'sa' => '',
                'co' => $domain
            );

            $hdrs['referer'] = $aurl_request;
            $reload_request = $aurl . '/reload?k=' . $key;
            $page_data3 = Http::withHeaders($hdrs)->asForm()->post($reload_request, $pdata)->body();
            preg_match('/rresp","([^"]+)/', $page_data3, $gtoken);
            if ($gtoken) {
                return $gtoken[1];
            }
        }
    }
}

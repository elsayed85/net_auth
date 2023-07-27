<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;


class Spotify
{
    private $username;
    private $password;
    private HttpBrowser $crawler;

    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    public function login()
    {
        $url = "https://accounts.spotify.com/login/password";

        $crawler = new HttpBrowser(HttpClient::create([
            'headers' => [
                "User-Agent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029. Safari/537.3",
                "Accept-Language" => "en-US,en;q=0.5",
            ]
        ]));

        $browse = $crawler->request('GET', "https://accounts.spotify.com/en/login");
        $meta = $browse->filter("meta#bootstrap-data")->attr("sp-bootstrap-data");
        $meta = json_decode($meta, true);
        $flowCtx = $meta["flowCtx"];

        $csrf = $crawler->getCookieJar()->get("sp_sso_csrf_token");

        $api =Http::asForm()
        ->withHeaders([
            "accept" => "application/json",
            "x-csrf-token" => "013acda719bd4e5be25a613f443211674fa376029431363930323033313739373131",
            "user-agent" => "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36",
            "sec-ch-ua-platform" => "\"Linux\"",
            "cookie" => "sp_t=e5d778cb19f9b79974551c9e01cfac69; sp_landing=https%3A%2F%2Fopen.spotify.com%2F%3Fsp_cid%3De5d778cb19f9b79974551c9e01cfac69%26device%3Ddesktop; _gcl_au=1.1.1311040850.1690202325; sp_adid=f002e5f0-db2d-4423-8b6e-bd2a3f54350e; _gid=GA1.2.175258320.1690202327; _scid=baa306a5-417b-4ef8-b3d6-2f09c56597ec; __Host-device_id=AQBW_jChp_aGWnDbBi5CSLEPHwG76tF_mrnVApK0opep7c3ZayiKpL962xeSp-p6MsXYg5uWuJsd4K-wY3ygUqWv9GLs58aXzKc; __Secure-TPASESSION=AQCuubOBAVolQTqaKbRNCSf6Pimt0FaHf6L93uDgd6+y3qlww9CcFIWgnBCuYpMzGYkVg28hIToaBtR0Rk83lXvnnOPTuP9WYDk=; sp_tr=true; remember=elsayedkamal581999%40gmail.com; OptanonConsent=isIABGlobal=false&datestamp=Mon+Jul+24+2023+15%3A45%3A32+GMT%2B0300+(Eastern+European+Summer+Time)&version=6.26.0&hosts=&landingPath=NotLandingPage&groups=s00%3A1%2Cf00%3A1%2Cm00%3A1%2Ct00%3A1%2Ci00%3A1%2Cf11%3A1&AwaitingReconsent=false; _scid_r=baa306a5-417b-4ef8-b3d6-2f09c56597ec; _ga_ZWG1NSHWD8=GS1.1.1690202326.1.1.1690202734.0.0.0; _ga=GA1.2.631916001.1690202326; sp_sso_csrf_token=013acda719bd4e5be25a613f443211674fa376029431363930323033313739373131; __Host-sp_csrf_sid=a10599cc064d87d99ed4929b516f6b160d9a1362d6752b1e10fed89cdd893f98; _gat=1"
        ])
        ->post($url , [
            "username" => $this->username,
            "password" => $this->password,
            "remember" => true,
            "recaptchaToken" => "03AAYGu2TF7dQPI_nrAqZV97u7xuQ1K-Gc9OHaw_6natj7Tc46NRo_bVcYueXizT7Ys8DgXOgksNBhRUdelrDLLbFixC1rvl63yYNc7jVxLYs5s1rKroQSbKhSSDrC-AVqTd0aZPtAf38p3gYKd7qj0Vg5Er2IqrUa6qr8UIIEaWmM4TM9ewqb0wuu9ClxLWHjxduTwuN3laqSAs1BdbmZ9bVVM83_okxw0n7P68UKfCVWjyijtTbHji2WQR2buRZ02gRw2D1VTi_Qji2NfOXl_biUB9BmitfeHUOtbHmtf0ALRX1l34YYZHFJA_RPdYpygZAi6H3MrRiFflHi1N-6zL9jX35yPI3hSmYL5pwHi9kv_2-0IoGYkI9HNTkwiUdrsElrzxfyiz9aQ5NOmBe9Gj-X6pVshfRe0N_LewnJDo3jy4o_fPWs2OmpkQZ8iOedipS-72BEwtRkFHAyYs-OqPSi3WDAsXokpfHsmFCJBkbK0cwf5PITG9yKtDdri_lCDvgG8IyqpqbYY-bZgRQgnh13FTFH8d_--RrBrNnrDq0Xp7brCvC22-DUWda2rs1rjUiA_U_Brl9VpZ6eSvJQDDgxwvv13aCClNir2mQg1KYiKSbsJ0gxYvQn8smn_3NPrRc72oNgbY9g3V61-EdClUXRRU4egAjoxkmKNLIwoL_mQdftsTPW78yBwiXwIvyhd-5KC47EYV38LUlz9Eq5Ny9yG84wIWnU-4pg-v8j-rqLfmY4ly1Z0FJSjLwxoY15G47xVZoIHhyEsQ0QFWpHK6DJO9mDPIyOY1vOSeuW-ZvvpWKMv13vfUWvtAGeoMx27DY80ro2DEJEebJaSUmPuBH4jGt0jCisAZrSE_qpF-a2GW4KRLtFLh_n13y2eByU4i8Ex3viLS15EVvHI9Ch9tomAXy3OxOjvshx0QzW-Ul4JjgEzDnS3c5JMKaYIur034zXvXPPGmWVFEq5axEOKEeDoAFu0UewX_zWJ74ZqykA0j7v7Qr05ExkS_IUnwBy48SqssUmJ0Tm8AF5wmsqBpSbHOvOCZlCUwYuvbbQ30SQBPzCLLdX9jjKHt3cYlIGSaly-eQt2GkDHiS2xDpV_kjEDJkbFtOezO0KmOctUrtASjaaPl6lOdadWybq6jcLA8VQXE9LQG3XM7mCwe2nw2Yn1BhVzSkureq61ha5SJot4IXKiDftVZ7914944ULNyH_i5WdYDw-m8PozVFfjjajp7Ikc5ax-FIeOuc8UbBA-2ti7y6JnaQiOZ2SbFfntydVl8T3pufqn7aEG6pH4Zp6FqcbT1N918xzVG_sFzkNZhiECwUS_kSCkqbrk_7SKDUtC3xLirCIE43H4ZDQwQT7bDJUNAXA-471I_ZRtn4aPjUI86ptYrEruFCd2NkPJ2TLYnXxhJOPf1ulyTBitESx4NQTIN7731QHRQkG3umiyBeDJwVws_zhtiVuboIkSJAjrenJnuVRX0BUPAdtWDv3H--4r4kYGruzsF_CcRf6SVVb1tUQEfjWMfXaj_rqTUGYRKZtOylCzMfW5TLXceI6BNARAX3D-8KqxfOyIeza9nTQcsudSvOKjq6XJ0gb9do5eRKCV-I4KndiVl5nUR23WbO1dlSaepFDFhQS2g0c0MNLkQ6A1eMc",
            "flowCtx" => $flowCtx,
            "continue" => "https://open.spotify.com/?flow_ctx=" . $flowCtx,
        ]);

        dd($api->body());
    }
}

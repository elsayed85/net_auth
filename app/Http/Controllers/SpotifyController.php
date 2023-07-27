<?php

namespace App\Http\Controllers;

use App\Models\SpotifyAccount;
use App\Services\SpotifyWeb;
use Illuminate\Http\Request;

class SpotifyController extends Controller
{
    public function authTv(Request $request)
    {
        $accountId = $request->accountId;
        $code = $request->code;

        $account = SpotifyAccount::find($accountId);

        if (!$account) {
            return response()->json([
                "success" => false,
                "msg" => "Account not found"
            ]);
        }

        $spotify = new SpotifyWeb();

        if ($spotify->login($account->cookies)) {
            $success = $spotify->authTv($account->cookies, $code);
            if ($success) {
                return response()->json(["success" => true, "msg" => "Code Authenticated"]);
            } else {
                return response()->json(["success" => false, "msg" => "Invalid Code"]);
            }
        } else {
            return response()->json([
                "success" => false,
                "msg" => "Invalid Account"
            ]);
        }
    }
}

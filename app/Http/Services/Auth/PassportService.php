<?php


namespace App\Http\Services\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

use Laravel\Passport\Client as OClient;

class PassportService
{

    /**
     * generate an access token with refresh token for the user's account.
     *
     * @param  string $email
     * @param  string $passowrd
     * @return \Illuminate\Http\Response
     */

    public function getTokenAndRefreshToken($email, $password)
    {
        $oClient = OClient::where('password_client', 1)->first();

        $response = Http::asForm()->post(config('app.test_url') . '/oauth/token', [
            'grant_type' => 'password',
            'client_id' => $oClient->id,
            'client_secret' => $oClient->secret,
            'username' => $email,
            'password' => $password,
            'scope' => '*', //for now all scope
        ]);

        //dd($response);

        //  return json_decode((string) $response->getBody(), true);
        return $response->json();
    }

    /**
     * generate an access token with refresh token for the user's account by user's refresh token.
     *
     * @param  string $refreshToken
     * @return \Illuminate\Http\Response
     */
    public function getTokenAndRefreshTokenByRefreshToken($refreshToken)
    {
        $oClient = OClient::where('password_client', 1)->first();

        $response = Http::asForm()->post(config('app.test_url') . '/oauth/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id' => $oClient->id,
            'client_secret' => $oClient->secret,
            'scope' => '',
        ]);

        return $response->json();
    }
}

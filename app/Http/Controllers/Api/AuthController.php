<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Client;
use App\Http\Services\Auth\PassportService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    //
    private $passportService;

    public function __construct(PassportService $passportService)
    {
        $this->passportService = $passportService;
    }


    public function login(Request $request)
    {
        # minimal code for the demo.... not ideal

        $request->validate([
            'email'         => 'required|email|exists:users,email',
            'password'      => 'required|min:6',
        ]);

        $email = $request->email;
        $password = $request->password;

        $credentials = [
            'email'     => $email,
            'password'  => $password
        ];

        if (Auth::attempt($credentials, true)) {
            //login
            $user = User::where('email', $email)->first();
            $tokenResponse =    $this->passportService->getTokenAndRefreshToken($email, $password);

            $data = [
                'user' => $user,
                'token' => $tokenResponse
            ];

            return new JsonResponse($data, 200);
        } else {
            //failed to log in
            return new JsonResponse([
                'message' => 'failed to login!'
            ], 401);
        }
    }
}

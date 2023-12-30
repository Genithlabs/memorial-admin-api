<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Client;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class AuthController extends Controller
{
    /* 회원가입 */
    public function register(Request $request)
    {
        // 유효성 체크
        $valid = validator($request->only('user_id', 'email', 'user_name', 'user_password'), [
            'user_id' => 'required|string|max:50|unique:mm_users',
            'email' => 'required|string|email|max:100|unique:mm_users',
            'user_name' => 'required|string|max:50',
            'user_password' => 'required|string|min:6|max:255'
        ]);
        if ($valid->fails()) {
            return response()->json([
                'error' => $valid->errors()->all()
            ], Response::HTTP_BAD_REQUEST);
        }

        $data = request()->only('user_id', 'email', 'user_name', 'user_password');

        $user = User::create([
            'user_id' => $data['user_id'],
            'email' => $data['email'],
            'user_name' => $data['user_name'],
            'user_password' => bcrypt($data['user_password'])
        ]);

        $client = Client::where('password_client', 1)->first();
        $tokenRoute = route('passport.token');

        $response = Http::asForm()->post($tokenRoute, [
            'grant_type' => 'password',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'username' => $data['user_id'],
            'password' => $data['user_password'],
            'scope' => '*'
        ]);

        if ($response->getStatusCode() == 200) {
            return json_decode((string) $response->getBody(), true);
        } else {
            return response()->json([
                'code' => $response->getStatusCode(),
                'message' => 'Http request error'
            ]);
        }
    }

    /* 로그인 */
    public function login(Request $request) {
        // 유효성 체크
        $valid = validator($request->only('email', 'user_password'), [
            'email' => 'required|string|email|max:100',
            'user_password' => 'required|string|min:6|max:255'
        ]);
        if ($valid->fails()) {
            return response()->json([
                'error' => $valid->errors()->all()
            ], Response::HTTP_BAD_REQUEST);
        }

        $data = request()->only('email', 'user_password');

        $credential = [
            'email' => $data['email'],
            'password' => $data['user_password'],
        ];

        if (!Auth::attempt($credential)) {
            return response()->json([
                'message' => '유효하지 않은 사용자 정보 입니다.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $client = Client::where('password_client', 1)->first();
        $tokenRoute = route('passport.token');

        $response = Http::asForm()->post($tokenRoute, [
            'grant_type' => 'password',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'username' => $data['email'],
            'password' => $data['user_password'],
            'scope' => '*'
        ]);

        if ($response->getStatusCode() == 200) {
            return json_decode((string) $response->getBody(), true);
        } else {
            return response()->json([
                'code' => $response->getStatusCode(),
                'message' => 'Http request error'
            ]);
        }
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Passport\Client;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    /* 회원가입 */
    public function register(Request $request)
    {
        // 유효성 체크
        $valid = validator($request->only('email', 'user_name', 'user_password'), [
            'email' => 'required|string|email|max:100|unique:mm_users',
            'user_name' => 'required|string|max:50',
            'user_password' => 'required|string|min:6|max:255'
        ]);
        if ($valid->fails()) {
            return response()->json([
                'error' => $valid->errors()->all()
            ], Response::HTTP_BAD_REQUEST);
        }

        $data = request()->only('email', 'user_name', 'user_password');

        $user = User::create([
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

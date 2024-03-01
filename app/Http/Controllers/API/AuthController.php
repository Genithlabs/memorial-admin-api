<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Password;

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
        $tokenRoute = env('APP_URL').route('passport.token', absolute: false);

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
        $valid = validator($request->only('user_id', 'user_password'), [
            'user_id' => 'required|string|max:50',
            'user_password' => 'required|string|min:6|max:255'
        ]);
        if ($valid->fails()) {
            return response()->json([
                'error' => $valid->errors()->all()
            ], Response::HTTP_BAD_REQUEST);
        }

        $data = request()->only('user_id', 'user_password');

        $credential = [
            'user_id' => $data['user_id'],
            'password' => $data['user_password'],
        ];

        if (!Auth::attempt($credential)) {
            return response()->json([
                'message' => '유효하지 않은 사용자 정보 입니다.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $client = Client::where('password_client', 1)->first();
        $tokenRoute = env('APP_URL').route('passport.token', absolute: false);

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

    /* 아이디 찾기 */
    public function findId(Request $request) {
        // 유효성 체크
        $valid = validator($request->only('email', 'user_name'), [
            'email' => 'required|string|email|max:100',
            'user_name' => 'required|string|max:50'
        ]);
        if ($valid->fails()) {
            return response()->json([
                'error' => $valid->errors()->all()
            ], Response::HTTP_BAD_REQUEST);
        }

        $data = request()->only('email', 'user_name');

        $user = User::where('email', $data['email'])
                    ->where('user_name', $data['user_name'])
                    ->first();

        if ($user == null) {
            return response()->json([
                'result' => 'fail',
                'message' => '가입된 사용자가 아닙니다.'
            ]);
        } else {
            return response()->json([
                'result' => 'sucess',
                'user_id' => $user['user_id']
            ]);
        }
    }

    public function forgotPassword(Request $request) {
        // 유효성 체크
        $valid = validator($request->only('user_id', 'user_name', 'email'), [
            'user_id' => 'required|string|max:50',
            'user_name' => 'required|string|max:50',
            'email' => 'required|string|email|max:100'
        ]);
        if ($valid->fails()) {
            return response()->json([
                'error' => $valid->errors()->all()
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = User::where('user_id', $request->user_id)
                    ->where('user_name', $request->user_name)
                    ->where('email', $request->email)
                    ->first();

        if ($user == null) {
            return response()->json([
                'result' => 'fail',
                'message' => '가입된 사용자가 아닙니다.'
            ]);
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        PasswordReset::where('email', $request->email)
            ->update([
                'user_id' => $request->user_id,
                'user_name' => $request->user_name
            ]);

        if ($status == 'passwords.sent') {
            return response()->json([
                'result' => 'success',
                'message' => '메일이 발송되었습니다.'
            ]);
        } else {
            return response()->json([
                'result' => 'fail',
                'message' => '메일 발송에 실패하였습니다.'
            ]);
        }
    }
}

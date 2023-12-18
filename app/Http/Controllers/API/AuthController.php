<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /* 회원가입 */
    public function register(Request $request)
    {
        //dd($request);
        // 유효성 체크
        // $valid = validator($request->only('email', 'user_name', 'user_password'), [
        //     'email' => 'required|string|email|max:100|unique:mm_users',
        //     'user_name' => 'required|string|max:50',
        //     'user_password' => 'required|string|min:6|max:255'
        // ]);
        // if ($valid->fails()) {
        //     return response()->json([
        //         'error' => $valid->errors()->all()
        //     ], Response::HTTP_BAD_REQUEST);
        // }

        $data = request()->only('email', 'user_name', 'user_password');
        // $user = User::create([
        //     'email' => $data['email'],
        //     'user_name' => $data['user_name'],
        //     'user_password' => bcrypt($data['user_password'])
        // ]);

        $credentials = array(
            'email' => $data['email'],
            'user_password' => $data['user_password']
        );

        if (!Auth::attempt($credentials)) {
            return 'login fail';
        }
        $client = Client::where('password_client', 1)->first();
        $tokenRoute = route('passport.token');
        //dd($tokenRoute);

        // $data = [
        //     'grant_type' => 'password',
        //     'client_id' => $client->id,
        //     'client_secret' => $client->secret,
        //     'username' => $data['email'],
        //     'password' => $data['user_password'],
        //     'scope' => '*',
        // ];

        // $request = Request::create('/oauth/token', 'POST', $data);
        // $response = app()->handle($request);

        // return $response;

        $response = Http::asForm()->post('http://127.0.0.1:8000/oauth/token', [
            'grant_type' => 'password',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'username' => Auth::user()['id'],
            'password' => $data['user_password'],
            'scope' => '*'
        ]);

        // app()->handle();

        // $response = Http::asForm()->post($tokenRoute, [
        //     'grant_type' => 'password',
        //     'client_id' => $client->id,
        //     'client_secret' => $client->secret,
        //     'username' => $data['user_email'],
        //     'password' => $data['user_password'],
        //     'scope' => ''
        // ]);
        if ($response->getStatusCode() == 200) {
            return json_decode((string) $response->getBody(), true);
        } else {
            dd("에러!!", $response->getStatusCode());
            return response()->json([
                'code' => $response->getStatusCode(),
                'message' => 'Http request error'
            ]);
        }
        dd('end');


        // $tokenRequest = Request::create(
        //     env('APP_URL').'/oauth/token',
        //     'post'
        // );
        $tokenRequest = Request::create(
            $tokenRoute,
            'post'
        );
        $response = Route::dispatch($tokenRequest);
        if ($response->getStatusCode() == 200) {
            return json_decode((string) $response->getBody(), true);
        } else {
            return response()->json([
                'code' => $response->getStatusCode(),
                'message' => 'Http request error'
            ]);
        }

        // $http = new \GuzzleHttp\Client();
        // dd($tokenRoute, $client->id, $client->secret, $http);
        // try {
        //     $response = $http->post($tokenRoute, [
        //         'form_params' => [
        //             'grant_type' => 'password',
        //             'client_id' => $client->id,
        //             'client_secret' => $client->secret,
        //             'username' => $data['user_email'],
        //             'password' => $data['user_password'],
        //             'scope' => '',
        //         ]
        //     ]);

        //     return json_decode((string) $response->getBody(), true);
        // } catch (ClientException $e) {
        //     echo Psr7\Message::toString($e->getRequest());
        //     echo Psr7\Message::toString($e->getResponse());

        //     return response()->json([
        //         'message' => 'Http request error'
        //     ], Response::HTTP_BAD_REQUEST);
        // }
    }
}

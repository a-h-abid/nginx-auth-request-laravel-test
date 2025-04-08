<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/auth/login', function (Request $request) {
    $username = $request->get('username');
    $password = $request->get('password');

    // Dummy Password
    if ($password != '1234') {
        return response()->json([
            'message' => 'Invalid User',
        ], 401);
    }

    $validTokenForSeconds = 120;
    $token = Str::random(32);
    $expires = now()->addSeconds($validTokenForSeconds);
    $userData = [
        'id' => md5($username),
        'username' => $username,
        'is_active' => 1,
    ];
    $userEncoded = base64_encode(
        json_encode($userData + ['expires_ts' => $expires->timestamp])
    );

    Cache::put($token, $userEncoded, $expires);

    $responseData = [
        'token' => $token,
        'expires_in_s' => $validTokenForSeconds,
        'expires_ts' => $expires->timestamp,
        'data' => $userData,
    ];

    Log::info("[$token] /auth/login Token Cached", $responseData);

    return response()->json($responseData);
});

Route::get('/auth/verify', function (Request $request) {
    $token = $request->header('X-Token');

    Log::info("[$token] /auth/verify Called", []);

    if (!$token) {
        Log::info("[$token] /auth/verify Token Invalid");
        return response()->json([
            'message' => 'Invalid Token',
        ], 401);
    }

    Log::info("[$token] /auth/verify Token Valid");

    $userData64 = Cache::get($token);
    if (!$userData64) {
        Log::info("[$token] /auth/verify User Not Found in Cache");
        return response()->json([
            'message' => 'Invalid User',
        ], 401);
    }

    Log::info("[$token] /auth/verify User Found");

    $userData = json_decode(base64_decode($userData64), true);
    if (!$userData) {
        Log::info("[$token] /auth/verify User Data Invalid");
        return response()->json([
            'message' => 'User token is broken',
        ], 401);
    }

    $expiresTs = time() + 60; // This 60s cache remember for could come from config.
    if (isset($userData['expires_ts'])) {
        $expiresTs = $userData['expires_ts'];
        unset($userData['expires_ts']);
    }

    return response()->json([
        'data' => $userData,
    ], 200, [
        'X-Token-Expires-Ts' => $expiresTs,
    ]);
});

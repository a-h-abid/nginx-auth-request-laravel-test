<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

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

Route::get('service/status', function (Request $request) {
    $authenticated = $request->header('X-Authenticated');
    $authData = $request->header('X-Auth-Data');
    $userData = json_decode(base64_decode($authData), true);

    Log::info('Accessing /api/service/status', [
        '$authenticated' => $authenticated,
        '$authData' => $authData,
        'userData' => $userData,
        // 'headers' => $request->headers->all(),
    ]);

    if (!$authenticated || $authenticated == '0') {
        return response()->json([
            'message' => 'Unauthenticated S',
        ], 401);
    }

    return response()->json([
        'message' => 'Ok',
        'userData' => $userData,
    ]);
});

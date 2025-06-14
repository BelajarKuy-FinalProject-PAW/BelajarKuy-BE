<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest; 
use App\Http\Resources\UserResource;    
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Log;    
class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LoginRequest $request): Response
    {
        $request->authenticate();

        $user = Auth::user(); 

        $tokenName = 'api-token-' . $user->username; 
        $token = $user->createToken($tokenName)->plainTextToken;

        return response([
            'message' => 'Login berhasil.', 
            'user' => new UserResource($user),
            'token' => $token
        ]); 
    }

    /**
     * Destroy an authenticated session (logout untuk API).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request): Response
    {
    $user = $request->user();
    Log::info('[Logout Attempt] User ID: ' . ($user ? $user->id : 'No user authenticated for logout request'));

    if ($user) {
        $currentToken = $user->currentAccessToken();
        if ($currentToken) {
            Log::info('[Logout Attempt] Token ID to delete: ' . $currentToken->id . ', Name: ' . $currentToken->name);
            $currentToken->delete();
            Log::info('[Logout Attempt] Token supposedly deleted from DB.');
        } else {
            Log::info('[Logout Attempt] No current access token found on user to delete.');
        }
    }
    return response()->noContent();
}
}
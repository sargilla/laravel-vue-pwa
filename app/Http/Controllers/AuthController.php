<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use App\Http\Resources\AccessTokensResource;

class AuthController extends Controller
{
    // protected function guard()
    // {
    //     return Auth::guard('api');
    // }

    public function __construct()
    {
        $this->middleware('auth:sanctum', ['except' => ['getAuthenticatedUser', 'authenticate', 'sendResetLinkEmail', 'resetPassword']]);
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        // if (Auth::attempt(['email' => $email, 'password' => $password, 'activated' => 1])) No dejar loguear si no esta activo
        if (Auth::attempt($credentials)) {
            // $request->session()->invalidate();
            // $request->session()->regenerate();
            return response(['message' => 'Logging in!', 'user' => new UserResource(auth()->user()->load(['roles', 'permissions']))], 200);
        }

        return response([
            "errors" => [
                'email' => 'The provided credentials do not match our records.'
            ]
        ], 422);
    }

    public function logout()
    {
        auth('web')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function getAuthenticatedUser()
    {
        return auth()->user() ?  new UserResource(auth()->user()) : null;
    }

    public function createAccessToken()
    {
        request()->validate(['token_name' => 'required|min:3']);
        $token = request()->user()->createToken(request()->token_name);
        return response(['token' => $token->plainTextToken, 'message' => 'Copie este token en un lugar seguro, no podrá verlo nuevamente: '], 201);
    }

    public function getAccessTokens()
    {
        return request()->user()->tokens;
    }

    public function deleteAccessToken($token_id)
    {
        return request()->user()->tokens()->where('id', $token_id)->delete();
    }

    protected function sendResetLinkEmail()
    {

        request()->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            request()->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response(['status' => __($status)])
            : response(['errors' => ['email' => [__($status)]]], 422);
    }

    protected function resetPassword()
    {

        request()->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            request()->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status == Password::PASSWORD_RESET
            ? response(['status' => __($status)])
            : response(['errors' => ['password' => [__($status)]]], 422);
    }
}

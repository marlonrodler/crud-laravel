<?php

namespace App\Services\Auth;

use Illuminate\Http\Request;

use App\Http\Resources\Auth\User as AuthUserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;

class LoginService
{

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required'
        ]);
        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password
        ])) {

            $user = Auth::user();

            $token = $user->createToken(
                $user->email . '-' . now()
            );
            return response()->json([
                'access_token' => $token->accessToken
            ]);
        } else {
            throw new HttpException(400, 'Invalid login');
        }
    }

    public function logged(Request $request): AuthUserResource
    {
        return new AuthUserResource($request->user());
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json();
    }

    public function revokeAllTokens(User $user)
    {
        $activeTokens = $user->tokens()
            ->where('revoked', '=', false)
            ->get();

        foreach ($activeTokens as $token) {
            $token->revoke();
        }
        return true;
    }
}

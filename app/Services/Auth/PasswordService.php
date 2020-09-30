<?php

namespace App\Services\Auth;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PasswordService
{

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function forgot(Request $request)
    {

        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $email = $request->get('email');

        $user = User::where('email', '=', $email)
            ->where('status', '!=', User::STATUS_BLOCKED)
            ->first();

        if (is_null($user)) {
            throw new HttpException(404);
        }

        DB::beginTransaction();

        try {
            $this->mail($user);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw new HttpException(500, "Something wrong on try to email user!");
        }

        DB::commit();

        return response()->json();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function callbackForgot(Request $request)
    {

        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $token = $request->get('token');
        $email = $request->get('email');

        if (!$this->tokenValidate($token, $email)) {
            throw new HttpException(406, 'Invalid token');
        }

        $user = User::where('email', '=', $email)->first();
        if (is_null($user)) {
            throw new HttpException(404, 'User not found');
        }

        $user->password = Hash::Make($request->get('password'));

        if ($user->status === User::STATUS_PENDING) {
            $user->email_verified_at = Carbon::now();
            $user->status = User::STATUS_ACTIVE;
        }

        $user->save();

        $this->forceTokenExpiration($email);

        return response()->json();
    }

    /**
     * @param  User $user
     * @return void
     */
    public function mail(User $user)
    {
        try {
            return Password::sendResetLink([
                'email' => $user->email
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * @param  string $token
     * @param  string $email
     * @return boolean
     */
    public function tokenValidate($token, $email)
    {

        $passwordReset = DB::table('password_resets')
            ->where('email', '=', $email)
            ->first();

        if (is_null($passwordReset) || !Hash::check($token, $passwordReset->token)) {
            return False;
        }

        $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $passwordReset->created_at);

        $expirationTime = config('auth.passwords.users.expire');
        $expirationDate = $created_at->addMinutes($expirationTime);

        if ($expirationDate->lt(Carbon::now())) {
            return false;
        }

        return true;
    }

    /**
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function reset(Request $request)
    {

        $request->validate([
            'old_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->get('old_password'), $user->password)) {
            throw new HttpException(401, 'Old password does not match!');
        }

        $user->password = Hash::Make($request->get('password'));

        $user->save();

        return response()->json();
    }

    /**
     * @param  string $email
     * @return boolean
     */
    public function forceTokenExpiration($email)
    {

        DB::table('password_resets')
            ->where('email', '=', $email)
            ->update([
                'token' => '(Manually Expired)'
            ]);
    }
}

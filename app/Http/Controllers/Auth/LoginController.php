<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\LoginService;
use Illuminate\Http\Request;

class LoginController extends Controller
{

    private $loginService;

    function __construct(LoginService $loginService)
    {
        $this->loginService = $loginService;
    }

    public function login(Request $request)
    {
        return $this->loginService->login($request);
    }

    public function logged(Request $request)
    {
        return $this->loginService->logged($request);
    }

    public function logout(Request $request)
    {
        return $this->loginService->logout($request);
    }
}

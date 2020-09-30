<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\PasswordService;
use Illuminate\Http\Request;

class PasswordController extends Controller
{

    private $passwordService;

    function __construct(PasswordService $passwordService)
    {
        $this->passwordService = $passwordService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function forgot(Request $request)
    {
        return $this->passwordService->forgot($request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function callbackForgot(Request $request)
    {
        return $this->passwordService->callbackForgot($request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function reset(Request $request)
    {
        return $this->passwordService->reset($request);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IndexController extends Controller
{

    public function web($any = null)
    {
        return redirect(config('app.api_url'));
    }

    public function api()
    {
        return response()->json([
            'message' => 'API is Working'
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BasicAuthController extends Controller
{
    public function login(Request $request) {
        if(Auth::attempt($request->only(["email","password"]))) {
            $token = Str::random(100);

            $request->user()->forceFill([
                "api_token" => hash("sha256", $token),
            ])->save();
            return response(["response" => "User login successful", "token" => $token], 200);
        }

        return response(["response" => "Authentication failed", "token" => null], 401);
    }

    public function logout() {
        $user = auth('api')->user();
        $user->api_token = null;
        $user->save();
        return response()->json(['response'=>'Successfully logged out'],200);
    }
}

<?php

namespace App\Http\Middleware;

use App\Models\User;
// use Auth;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header("Authorization");

        $autenticate = true;

        if (!$token) {
            $autenticate = false;
        }

        //    cek ke database ada tidak tokennya
        $user = User::where("token", $token)->first();
        if (!$user) {
            $autenticate = false;
        }else{
            Auth::login($user);
        }

        if ($autenticate) {
            return $next($request);
        } else {
            return response()->json([
                "errors" => [
                    "message" => [
                        "unautorize"
                    ]
                ]
            ])->setStatusCode(401);
        }
    }
}

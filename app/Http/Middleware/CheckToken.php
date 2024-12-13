<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckToken
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $authToken = $request->bearerToken();
        if (is_null($authToken) || $authToken !== env('TOKEN')) {
            return response()->json([
                'status'  => 'error',
                'code'    => 403,
                'message' => 'Invalid token',
            ], 403);
        }

        return $next($request);
    }
}

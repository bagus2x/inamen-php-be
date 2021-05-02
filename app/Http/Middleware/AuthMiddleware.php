<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->header('Authorization');
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token not provided',
                'data' => null
            ], 401);
        }

        $token = explode(' ', $token);

        if (count($token) != 2) {
            return response()->json([
                'success' => false,
                'message' => 'Wrong authorization header format',
                'data' => null
            ], 401);
        }

        try {
            $credentials = JWT::decode($token[1], env('ACCESS_TOKEN_SECRET'), ['HS256']);
        } catch (ExpiredException $e) {
            return response()->json([
                'error' => 'Provided token is expired.'
            ], 401);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'An error while decoding token.'
            ], 401);
        }

        $request->userID = $credentials->sub;

        return $next($request);
    }
}

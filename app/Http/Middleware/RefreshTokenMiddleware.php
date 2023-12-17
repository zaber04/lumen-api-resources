<?php

namespace Zaber04\LumenApiResources\Http\Middleware;

use Zaber04\LumenApiResources\Traits\ApiResponseTrait;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;


// use Tymon\JWTAuth\Http\Middleware\BaseMiddleware; --> deprecated --> not extending

class RefreshTokenMiddleware
{
    use ApiResponseTrait;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed (actually PendingRequest)
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
            if ($e instanceof TokenInvalidException) {
                $this->jsonResponseWith(['message' => 'This token is invalid. Please Login'], JsonResponse::HTTP_UNAUTHORIZED);
            } else if ($e instanceof TokenExpiredException) {
                // If the token is expired, then it will be refreshed and added to the headers
                try {
                    $refreshed = JWTAuth::refresh(JWTAuth::getToken());
                    $user      = JWTAuth::setToken($refreshed)->toUser();
                    $request->headers->set('Authorization', 'Bearer ' . $refreshed);
                } catch (\Exception $e) {
                    $this->jsonResponseWith(['message' => 'JWT-EXCEPTION!!! Token cannot be refreshed, please Login again'], JsonResponse::HTTP_EARLY_HINTS);
                }
            } else {
                return response()->json(compact('message'), 404);
                $this->jsonResponseWith(['message' => 'Authorization Token not found'], JsonResponse::HTTP_NOT_FOUND);
            }
        }

        return $next($request);
    }
}

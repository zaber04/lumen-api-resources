<?php

namespace Zaber04\LumenApiResources\Traits;

use Zaber04\LumenApiResources\Models\User;

use Carbon\Carbon;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;


trait TokenTrait
{
    /**
     * Generates a JWT token
     *
     * DOES NOT HANDLE exception
     */
    private function generateToken($user, string $session_id): string {
        // Include additional claims during JWT creation
        try {
            $user['session_id'] =  $session_id;

            // Set the token expiration time and refresh time
            $expiration = Carbon::now()->addMinutes(env('JWT_REFRESH_TTL', 60))->timestamp;
            $refreshAt  = Carbon::now()->addMinutes(env('JWT_REFRESH_MINUTES', 60))->timestamp;

            $payload = JWTFactory::sub($session_id)
                ->user($user)
                ->user_id($user->id)
                ->session_id($session_id)
                ->setExpiration($expiration) // @TODO: not being applied on exp --> bug
                ->setRefreshFlow()
                ->setRefreshTTL($refreshAt)
                ->make();

            $tokenObject = JWTAuth::encode($payload);
            $token = $tokenObject->get();

            return $token;
        } catch (\Exception $exception) {
            $errorLogData = [
                'function'   => __NAMESPACE__ . "::generateToken()",
                'statusCode' => JsonResponse::HTTP_BAD_REQUEST,
                'message'    => $exception->getMessage()
            ];

            Log::error('Error occurred: ' . json_encode($errorLogData));

            return null;
        }
    }

    /**
     * Decodes JWT token from header and returns the payload as array
     */
    private function getTokenArrayFromHeader(Request $request): array {
        try {
            $authHeader = $request->header('Authorization');

            if (!$authHeader) {
                // For now, let's return an empty array if header is missing
                // we may throw an exception in the controller if needed
                // we don't really need to log this since controller might reject the request anyway
                return [];
            }

            // Extract the token from the Authorization header
            list($tokenType, $token) = explode(' ', $authHeader, 2);

            // Check if the token is a valid JWT
            if ($tokenType === 'Bearer') {
                // Decode the JWT token
                $decodedToken = JWTAuth::setToken($token)->getPayload();
                $allClaims = $decodedToken->toArray();

                return $allClaims;
            }

            return [];
        } catch (\Exception $exception) {
            $errorLogData = [
                'function'   => __NAMESPACE__ . "::generateToken()",
                'statusCode' => JsonResponse::HTTP_BAD_REQUEST,
                'message'    => $exception->getMessage()
            ];

            Log::error('Error occurred: ' . json_encode($errorLogData));
            return [];
        }
    }

    /**
     * Assigns the jwt token as authorization header
     *
     * @param string $token
     * @param \Illuminate\Http\Client\PendingRequest  (using mixed intead) $http
     * @return \Illuminate\Http\Client\PendingRequest (using mixed intead)
     */
    private function setTokenInHeader(string $token, $http) {
        $headers = ['Authorization' => 'Bearer ' . $token];

        // Set headers on the HTTP client
        $http = $http->withHeaders($headers);

        return $http;
    }

    /**
     * Generates a UUID which we use to mark a users session
     */
    private function generateSessionId(): string
    {
        return Str::uuid()->toString();
    }

    /**
     * Respond with a JWT token.
     *
     * @param string $token
     * @return JsonResponse
     */
    private function tokenPayload(string $token,  array $claims = []): array
    {
        $tokenArray = [
            'accessToken' => $token,
            'tokenType'   => 'Bearer',
            'user'        => auth()->user(),
            'expiresIn'   => JWTAuth::factory()->getTTL() * env('JWT_REFRESH_MINUTES', 60)
        ];

        return array_merge($tokenArray, $claims);
    }

    /**
     * Refresh the token
     */
    private function getRefreshToken() {
        return JWTAuth::refresh(JWTAuth::getToken());
    }
}

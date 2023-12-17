<?php

namespace Zaber04\LumenApiResources\Traits;


use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;


trait ApiResponseTrait
{
    /**
     * Respond with a JSON
     *
     */
    public function jsonResponseWith(mixed $data, int $statusCode = Response::HTTP_OK): JsonResponse
    {
        $data['statusCode'] = $statusCode;

        // Successful responses ( 200 – 299 )
        // Redirection messages ( 300 – 399 )
        // Client error responses ( 400 – 499 )
        // Server error responses ( 500 – 599 )
        if ($statusCode >= 400) {
            $data['success'] = false;
            $data['message'] = $data['message'] ?? 'Request failed.';
        } else {
            $data['success'] = true;
            $data['message'] = $data['message'] ?? 'Request successful.';
        }

        return response()->json($data, $statusCode);
    }

    /**
     * Respond with an error JSON response.
     */
    public function errorResponse(string $errorMessage, int $statusCode = JsonResponse::HTTP_BAD_REQUEST): JsonResponse
    {
        return $this->jsonResponseWith(['error' => $errorMessage], $statusCode);
    }

    /**
     * Respond with a message JSON response.
     */
    public function messageResponse(string $message, int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return $this->jsonResponseWith(['message' => $message], $statusCode);
    }
}

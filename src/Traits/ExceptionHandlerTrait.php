<?php

namespace Zaber04\LumenApiResources\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait ExceptionHandlerTrait
{
    use ApiResponseTrait;
    use LoggingTrait;

    /**
     * Handle exceptions and log errors.
     *
     * @param Request $request
     * @param \Exception $exception
     * @param array $errorInfo
     * @param int $statusCode
     * @return JsonResponse
     */
    private function handleException(Request $request, \Exception $exception, array $errorInfo, $statusCode = JsonResponse::HTTP_UNPROCESSABLE_ENTITY): JsonResponse
    {
        $errorLogData = [
            'url'          => $request->path() ?? '/',
            'param'        => $request->all(),
            'body'         => '',
            'controller'   => $errorInfo['function'] ?? 'Controller',
            'functionName' => $errorInfo['function'] ?? 'function',
            'statusCode'   => $statusCode,
            'message'      => $exception->getMessage(),
            'error'        => $exception->getMessage(),
            'ip'           => $request->getClientIp(),
        ];

        if ($exception instanceof ValidationException) {
            $errorLogData['error'] = $exception->validator->errors();
        } elseif ($exception instanceof ModelNotFoundException) {
            $errorLogData['statusCode'] = JsonResponse::HTTP_NOT_FOUND;
        } elseif ($exception instanceof ModelNotFoundException) {
            $errorLogData['statusCode'] = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;
        }

        $this->logAndStoreError($errorLogData);

        return $this->jsonResponseWith(['errorLog' => $errorLogData, 'message' => 'Validation exception error.'], $statusCode);
    }
}

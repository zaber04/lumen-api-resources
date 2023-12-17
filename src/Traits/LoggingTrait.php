<?php


namespace Zaber04\LumenApiResources\Traits;

use Zaber04\LumenApiResources\Models\ErrorLog;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

trait LoggingTrait
{
    /**
     * Log and respond with an error JSON response.
     *
     * @param  array  $errorData
     * @return JsonResponse
     */
    public function logAndStoreError(array $errorData): void
    {
        try {
            // Validate and format the error data
            $validatedErrorData = $this->validateAndFormatErrorData($errorData);

            Log::error('Error occurred: ' . json_encode($validatedErrorData));

            // Save error in the database
            $this->saveErrorToDatabase($validatedErrorData);
        } catch (\Exception $e) {
            Log::error('Secondary error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Validate and format the error data array based on the ErrorLog model.
     *
     * @param  array  $errorData  Should follow ErrorLog model
     * @return array
     */
    private function validateAndFormatErrorData(array $errorData): array
    {
        // process fields
        $errorData['param'] = json_encode($errorData['param']);
        $errorData['error'] = json_encode($errorData['error']);

        // match with our model
        $fillableAttributes = (new ErrorLog())->getFillable();

        // all required keys present?
        $requiredKeys = array_diff($fillableAttributes, array_keys($errorData));
        foreach ($requiredKeys as $key) {
            $errorData[$key] = null;
        }

        return $errorData;
    }

    /**
     * Save error data to the database.
     *
     * @param  array  $errorData
     * @return void
     */
    private function saveErrorToDatabase(array $errorData): void
    {
        try {
            $errorLog = new ErrorLog($errorData);
            $errorLog->save();
        } catch (\Exception $e) {
            Log::error('Error saving to database: ' . $e->getMessage());
        }
    }


    /**
     * Logs the request on success
     */
    private function logRequestAndResponse(array $requestData, mixed $responseData): void
    {
        Log::info('API Request', ['request' => $requestData]);
        Log::info('API Response', ['response' => $responseData]);
    }
}




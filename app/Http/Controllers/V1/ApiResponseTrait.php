<?php
namespace App\Http\Controllers\V1;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * API Response Helper Trait
 *
 * Provides standardized JSON response methods for API controllers.
 * Used to maintain consistent response structure across the API endpoints.
 */
trait ApiResponseTrait
{
    /**
     * Creates a standardized success response
     *
     * @param mixed $data Data to be returned in the response
     * @param string|null $message Optional success message
     * @param int $code HTTP status code (defaults to 200 OK)
     * @return JsonResponse JSON response containing success data:
     *                     {
     *                         success: true,
     *                         message: string|null,
     *                         data: mixed
     *                     }
     */
    protected function successResponse(mixed $data, string | null $message = null, int $code = Response::HTTP_OK) : JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * Creates a standardized error response
     *
     * @param string $message Error message explaining what went wrong
     * @param mixed|null $errors Additional error details or validation errors
     * @param int $code HTTP status code (defaults to 400 Bad Request)
     * @return JsonResponse JSON response containing error data:
     *                     {
     *                         success: false,
     *                         message: string,
     *                         errors: mixed|null
     *                     }
     */
    protected function errorResponse(string $message, mixed $errors = null, int $code = Response::HTTP_BAD_REQUEST) : JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $code);
    }
}

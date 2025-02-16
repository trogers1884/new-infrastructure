<?php
namespace App\Components\Api\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class BaseApiController extends Controller
{
    /**
     * Default success status code
     */
    protected int $statusCode = 200;

    /**
     * API Version
     */
    protected string $apiVersion;

    /**
     * Request ID
     */
    protected string $requestId;

    public function __construct()
    {
        // Generate a unique request ID if not already set
        $this->requestId = request()->header('X-Request-ID') ?? (string) Str::uuid();

        // Get API version from route parameter or default to 'v1'
        $this->apiVersion = request()->route('version') ?? 'v1';
    }

    /**
     * Get response metadata.
     *
     * @param array $additionalMeta
     * @return array
     */
    protected function getMetadata(array $additionalMeta = []): array
    {
        return array_merge([
            'timestamp' => now()->toIso8601String(),
            'request_id' => $this->requestId,
            'api_version' => $this->apiVersion,
        ], $additionalMeta);
    }

    /**
     * Send a success response.
     *
     * @param mixed $data
     * @param string|null $message
     * @param array $meta
     * @return JsonResponse
     */
    protected function respondSuccess(mixed $data = null, ?string $message = null, array $meta = []): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => $data,
            'meta' => $this->getMetadata($meta)
        ];

        if ($message) {
            $response['message'] = $message;
        }

        return response()->json($response, $this->statusCode)
            ->header('X-Request-ID', $this->requestId);
    }

    /**
     * Send an error response.
     *
     * @param string $message
     * @param mixed $errors
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function respondError(string $message, mixed $errors = null, int $statusCode = 400): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
            'meta' => $this->getMetadata()
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode)
            ->header('X-Request-ID', $this->requestId);
    }

    /**
     * Send a not found response.
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function respondNotFound(string $message = 'Resource not found'): JsonResponse
    {
        return $this->respondError($message, null, 404);
    }

    /**
     * Send an unauthorized response.
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function respondUnauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->respondError($message, null, 401);
    }

    /**
     * Send a forbidden response.
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function respondForbidden(string $message = 'Forbidden'): JsonResponse
    {
        return $this->respondError($message, null, 403);
    }

    /**
     * Send a validation error response.
     *
     * @param mixed $errors
     * @param string $message
     * @return JsonResponse
     */
    protected function respondValidationError(mixed $errors, string $message = 'Validation failed'): JsonResponse
    {
        return $this->respondError($message, $errors, 422);
    }
}

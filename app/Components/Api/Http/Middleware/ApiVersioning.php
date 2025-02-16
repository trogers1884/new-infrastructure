<?php
namespace App\Components\Api\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiVersioning
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string|null $version
     * @return Response
     */
    public function handle(Request $request, Closure $next, ?string $version = null): Response
    {
        // Get supported versions from config
        $supportedVersions = config('api.versions', ['v1']);
        $defaultVersion = config('api.default_version', 'v1');

        // Check version from different sources in order of priority:
        // 1. URL segment (from route parameter)
        // 2. Accept header with version (Accept: application/vnd.api.{version}+json)
        // 3. X-API-Version header
        // 4. Default version

        $requestedVersion = $version ?? // From route parameter
            $this->getVersionFromAcceptHeader($request) ??
            $request->header('X-API-Version') ??
            $defaultVersion;

        // Clean up version string (remove 'v' prefix if present)
        $requestedVersion = ltrim(strtolower($requestedVersion), 'v');
        $requestedVersion = 'v' . $requestedVersion;

        // Check if requested version is supported
        if (!in_array($requestedVersion, $supportedVersions)) {
            return response()->json([
                'success' => false,
                'message' => 'Unsupported API version',
                'meta' => [
                    'supported_versions' => $supportedVersions,
                    'current_version' => $requestedVersion,
                    'timestamp' => now()->toIso8601String(),
                ]
            ], 400);
        }

        // Add version to request for use in controllers
        $request->merge(['api_version' => $requestedVersion]);

        // Add version to route parameters
        $request->route()->forgetParameter('version');
        $request->route()->setParameter('version', $requestedVersion);

        return $next($request);
    }

    /**
     * Extract version from Accept header.
     *
     * @param Request $request
     * @return string|null
     */
    protected function getVersionFromAcceptHeader(Request $request): ?string
    {
        $accept = $request->header('Accept');

        if (!$accept) {
            return null;
        }

        // Match version in Accept header (application/vnd.api.v1+json)
        if (preg_match('/application\/vnd\.api\.v(\d+)\+json/', $accept, $matches)) {
            return 'v' . $matches[1];
        }

        return null;
    }
}

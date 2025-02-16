<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ResourceAccess
{
    public function handle(Request $request, Closure $next, string $resourceType, string $resourceValue): Response
    {
        try {
            // Add debug logging
            Log::info('Resource Access Check', [
                'user_id' => auth()->id(),
                'resource_type' => $resourceType,
                'resource_value' => $resourceValue,
                'url' => $request->url()
            ]);

            $hasAccess = DB::table('auth.vw_user_authorizations')
                ->where('user_id', auth()->id())
                ->where('resource_type', $resourceType)
                ->where('resource_value', $resourceValue)
                ->exists();

            // Add query logging
            Log::info('Access Query Result', [
                'hasAccess' => $hasAccess,
                'query' => DB::table('auth.vw_user_authorizations')
                    ->where('user_id', auth()->id())
                    ->where('resource_type', $resourceType)
                    ->where('resource_value', $resourceValue)
                    ->toSql(),
                'bindings' => [
                    'user_id' => auth()->id(),
                    'resource_type' => $resourceType,
                    'resource_value' => $resourceValue
                ]
            ]);

            if (!$hasAccess) {
                Log::warning('Unauthorized resource access attempt', [
                    'user_id' => auth()->id(),
                    'resource_type' => $resourceType,
                    'resource_value' => $resourceValue,
                    'url' => $request->url()
                ]);

                abort(403, 'Unauthorized to access this resource');
            }

            return $next($request);

        } catch (\Exception $e) {
            Log::error('Error in resource access middleware', [
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
                'resource_type' => $resourceType,
                'resource_value' => $resourceValue
            ]);

            abort(403, 'Unable to verify resource access');
        }
    }
}

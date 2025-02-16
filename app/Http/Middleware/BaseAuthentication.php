<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseAuthentication
{
    public function handle(Request $request, Closure $next, string $permission = null): Response
    {
        if (!Auth::check()) {
            return redirect()->route($this->getLoginRoute());
        }

        return $next($request);
    }

    // Allow components to specify their login route
    abstract protected function getLoginRoute(): string;
}

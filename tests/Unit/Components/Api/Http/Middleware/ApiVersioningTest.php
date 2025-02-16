<?php
// tests/Unit/Components/Api/Http/Middleware/ApiVersioningTest.php

namespace Tests\Unit\Components\Api\Http\Middleware;

use App\Components\Api\Http\Middleware\ApiVersioning;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Tests\TestCase;

class ApiVersioningTest extends TestCase
{
    protected ApiVersioning $middleware;

    protected function setUp(): void
    {
        parent::setUp();
        $this->middleware = new ApiVersioning();
    }

    public function test_accepts_valid_version_from_route(): void
    {
        // Create request with route parameter
        $request = new Request();
        $route = new Route('GET', '/api/v1/test', []);
        $route->parameters = ['version' => 'v1'];
        $request->setRouteResolver(function () use ($route) {
            return $route;
        });

        // Process middleware
        $response = $this->middleware->handle($request, function ($req) {
            $this->assertEquals('v1', $req->route('version'));
            return response()->json(['status' => 'ok']);
        });

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_accepts_valid_version_from_accept_header(): void
    {
        $request = new Request();
        $request->headers->set('Accept', 'application/vnd.api.v1+json');
        $route = new Route('GET', '/api/test', []);
        $request->setRouteResolver(function () use ($route) {
            return $route;
        });

        $response = $this->middleware->handle($request, function ($req) {
            $this->assertEquals('v1', $req->route('version'));
            return response()->json(['status' => 'ok']);
        });

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_rejects_invalid_version(): void
    {
        $request = new Request();
        $route = new Route('GET', '/api/v999/test', []);
        $route->parameters = ['version' => 'v999'];
        $request->setRouteResolver(function () use ($route) {
            return $route;
        });

        $response = $this->middleware->handle($request, function ($req) {
            return response()->json(['status' => 'ok']);
        });

        $this->assertEquals(400, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertFalse($content['success']);
        $this->assertEquals('Unsupported API version', $content['message']);
    }

    public function test_uses_default_version_when_not_specified(): void
    {
        $request = new Request();
        $route = new Route('GET', '/api/test', []);
        $request->setRouteResolver(function () use ($route) {
            return $route;
        });

        $response = $this->middleware->handle($request, function ($req) {
            $this->assertEquals('v1', $req->route('version'));
            return response()->json(['status' => 'ok']);
        });

        $this->assertEquals(200, $response->getStatusCode());
    }
}

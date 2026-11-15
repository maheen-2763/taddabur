<?php

namespace Tests\Feature\Admin;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AdminRoutesAreProtectedTest extends TestCase
{
    public function test_every_admin_route_requires_auth_and_admin_middleware(): void
    {
        $routes = collect(Route::getRoutes()->getRoutes())->filter(
            fn($route) => $route->uri() === 'admin'
                || str_starts_with($route->uri(), 'admin/')
                || str_starts_with((string) $route->getName(), 'admin.'),
        );

        $this->assertGreaterThan(20, $routes->count(), 'Expected to find the admin routes.');

        foreach ($routes as $route) {
            $middleware = $route->gatherMiddleware();

            $this->assertContains('auth', $middleware, "{$route->uri()} is missing the auth middleware");
            $this->assertContains('admin', $middleware, "{$route->uri()} is missing the admin middleware");
        }
    }
}

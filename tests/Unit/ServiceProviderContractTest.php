<?php

use Art35rennes\DaisyKit\DaisyKitServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

covers(DaisyKitServiceProvider::class);

beforeEach(function () {
    $this->originalRouter = app('router');
});

afterEach(function () {
    app()->instance('router', $this->originalRouter);
    app()->instance('routes', $this->originalRouter->getRoutes());
    Route::swap($this->originalRouter);
    Facade::clearResolvedInstance('router');
});

it('loads the package public view and translation namespaces', function () {
    expect(View::exists('daisy::components.partials.assets'))->toBeTrue()
        ->and(View::exists('daisy::templates.auth.login-simple'))->toBeTrue()
        ->and(__('daisy::auth.login'))->not->toBe('daisy::auth.login');
});

it('exposes templates as anonymous Blade components through the public alias', function () {
    View::share('errors', new \Illuminate\Support\MessageBag);

    $html = Blade::render('<x-daisy::templates.auth.login-simple />');

    expect($html)
        ->toContain('<form')
        ->toContain(__('daisy::auth.login'));
});

it('registers the default csrf refresh route contract', function () {
    $route = Route::getRoutes()->getByName('daisy-kit.csrf-token');

    expect($route)->not->toBeNull()
        ->and($route->uri())->toBe('daisy-kit/csrf-token.json')
        ->and($route->methods())->toContain('GET')
        ->and($route->gatherMiddleware())->toContain('web');
});

it('registers configurable csrf route path name and middleware when booted', function () {
    config([
        'daisy-kit.csrf_refresh.enabled' => true,
        'daisy-kit.csrf_refresh.path' => 'custom/csrf-token.json',
        'daisy-kit.csrf_refresh.name' => 'custom.csrf-token',
        'daisy-kit.csrf_refresh.middleware' => ['web', 'throttle:60,1'],
    ]);

    (new DaisyKitServiceProvider(app()))->boot();

    $route = collect(Route::getRoutes()->getRoutes())->first(function ($route) {
        return $route->getName() === 'custom.csrf-token';
    });

    expect($route)->not->toBeNull()
        ->and($route->uri())->toBe('custom/csrf-token.json')
        ->and($route->gatherMiddleware())->toContain('web')
        ->and($route->gatherMiddleware())->toContain('throttle:60,1');
});

it('does not register the csrf route when the feature is disabled', function () {
    $router = new Router(app('events'), app());
    app()->instance('router', $router);
    app()->instance('routes', $router->getRoutes());
    Route::swap($router);
    Facade::clearResolvedInstance('router');

    config([
        'daisy-kit.csrf_refresh.enabled' => false,
        'daisy-kit.csrf_refresh.name' => 'disabled.csrf-token',
    ]);

    (new DaisyKitServiceProvider(app()))->boot();

    expect($router->getRoutes()->getByName('disabled.csrf-token'))->toBeNull();
});

it('publishes the documented package groups', function (string $group, Closure $paths) {
    $provider = DaisyKitServiceProvider::class;

    expect(ServiceProvider::pathsToPublish($provider, $group))->toBe($paths());
})->with('package publish groups');

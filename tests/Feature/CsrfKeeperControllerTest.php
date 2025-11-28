<?php

use Art35rennes\DaisyKit\Http\Controllers\CsrfTokenController;
use Illuminate\Support\Facades\Config;

it('returns json response with csrf token', function () {
    // Initialiser la session pour que csrf_token() fonctionne
    session()->start();

    $controller = new CsrfTokenController;
    $request = request();

    $response = $controller->__invoke($request);

    expect($response)
        ->toBeInstanceOf(\Illuminate\Http\JsonResponse::class)
        ->and($response->getStatusCode())->toBe(200);

    $content = $response->getContent();
    expect($content)->toBeJson();

    $data = json_decode($content, true);
    expect($data)
        ->toBeArray()
        ->toHaveKey('token')
        ->and($data['token'])->toBeString()
        ->and($data['token'])->toBe(csrf_token());

    expect($response->headers->get('X-CSRF-TOKEN'))->toBe(csrf_token());
});

it('calculates refresh interval from session lifetime config', function () {
    Config::set('session.lifetime', 120); // 120 minutes

    $component = view('daisy::components.ui.utilities.csrf-keeper', [
        'refreshInterval' => null,
        'refreshRatio' => 0.8,
    ]);

    $html = $component->render();

    // Calcul attendu: 120 * 60 * 1000 * 0.8 = 5760000 ms
    expect($html)->toContain('data-refresh-interval="5760000"');
});

it('uses provided refresh interval when set', function () {
    $component = view('daisy::components.ui.utilities.csrf-keeper', [
        'refreshInterval' => 300000, // 5 minutes
    ]);

    $html = $component->render();

    expect($html)->toContain('data-refresh-interval="300000"');
});

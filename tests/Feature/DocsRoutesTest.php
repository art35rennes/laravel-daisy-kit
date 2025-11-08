<?php

use Illuminate\Support\Facades\Config;

it('registers docs routes by default', function () {
    // Par défaut la documentation publique est activée (config => enabled=true)
    $this->assertTrue(config('daisy-kit.docs.enabled'));
    $response = $this->get('/docs');
    $response->assertSuccessful();
});

it('root redirects to docs when enabled', function () {
    $response = $this->get('/');
    $response->assertRedirect('/docs');
});

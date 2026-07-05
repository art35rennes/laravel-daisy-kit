<?php

use Art35rennes\DaisyKit\Support\DaisyTableUrlPolicy;

it('allows default web and relative table link hrefs', function (): void {
    expect(DaisyTableUrlPolicy::isSafeHref('/tickets/123'))->toBeTrue()
        ->and(DaisyTableUrlPolicy::isSafeHref('#details'))->toBeTrue()
        ->and(DaisyTableUrlPolicy::isSafeHref('?tab=activity'))->toBeTrue()
        ->and(DaisyTableUrlPolicy::isSafeHref('https://example.test/tickets/123'))->toBeTrue()
        ->and(DaisyTableUrlPolicy::isSafeHref('mailto:support@example.test'))->toBeTrue()
        ->and(DaisyTableUrlPolicy::isSafeHref('tel:+33123456789'))->toBeTrue();
});

it('allows deeplink schemes only when explicitly configured', function (): void {
    expect(DaisyTableUrlPolicy::isSafeHref('myapp://ticket/123'))->toBeFalse()
        ->and(DaisyTableUrlPolicy::isSafeHref('myapp://ticket/123', [], ['allowedSchemes' => ['myapp']]))->toBeTrue()
        ->and(DaisyTableUrlPolicy::isSafeHref('intent://scan/#Intent;scheme=zxing;end'))->toBeFalse()
        ->and(DaisyTableUrlPolicy::isSafeHref('intent://scan/#Intent;scheme=zxing;end', ['allowedSchemes' => ['intent']]))->toBeTrue();
});

it('blocks dangerous and malformed hrefs even when configured', function (): void {
    $policy = ['allowedSchemes' => ['javascript', 'data', 'vbscript', 'myapp']];

    expect(DaisyTableUrlPolicy::isSafeHref('javascript:alert(1)', $policy))->toBeFalse()
        ->and(DaisyTableUrlPolicy::isSafeHref('data:text/html,<script>alert(1)</script>', $policy))->toBeFalse()
        ->and(DaisyTableUrlPolicy::isSafeHref('vbscript:msgbox(1)', $policy))->toBeFalse()
        ->and(DaisyTableUrlPolicy::isSafeHref("https://example.test/\nusers", $policy))->toBeFalse();
});

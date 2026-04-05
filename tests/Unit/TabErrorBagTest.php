<?php

use Art35rennes\DaisyKit\Helpers\TabErrorBag;
use Illuminate\Support\MessageBag;

covers(TabErrorBag::class);

it('counts errors by tab prefix for the package form-tabs contract', function () {
    $errors = new MessageBag([
        'general_name' => ['Required'],
        'general_email' => ['Invalid'],
        'advanced_notes' => ['Too long'],
        'ignored_field' => ['Ignored'],
    ]);

    expect(TabErrorBag::countErrorsByTabPrefix(['general', 'advanced'], $errors))->toBe([
        'general' => 2,
        'advanced' => 1,
    ]);
});

it('counts errors for fields mapped to multiple tabs', function () {
    $errors = new MessageBag([
        'shared_field' => ['Required', 'Must be unique'],
    ]);

    $counts = TabErrorBag::countErrorsByTab([
        'shared_field' => ['general', 'advanced'],
    ], $errors);

    expect($counts)->toBe([
        'general' => 2,
        'advanced' => 2,
    ]);
});

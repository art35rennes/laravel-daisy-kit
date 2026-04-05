<?php

use Art35rennes\DaisyKit\Helpers\WizardPersistence;

covers(WizardPersistence::class);

it('returns a default value when a wizard field is missing', function () {
    expect(WizardPersistence::getValue('missing', 'fallback'))->toBe('fallback');
});

it('stores and retrieves a single wizard field value', function () {
    WizardPersistence::putValue('name', 'Jane Doe');

    expect(WizardPersistence::getValue('name'))->toBe('Jane Doe');
});

it('forgets wizard data and current step together', function () {
    WizardPersistence::put(['email' => 'jane@example.com']);
    WizardPersistence::setCurrentStep(3);

    WizardPersistence::forget();

    expect(WizardPersistence::get())->toBe([])
        ->and(WizardPersistence::getCurrentStep())->toBeNull();
});

<?php

use Art35rennes\DaisyKit\FormKit\Contracts\JsonataEvaluator;
use Art35rennes\DaisyKit\FormKit\FormFieldCatalog;
use Art35rennes\DaisyKit\FormKit\FormSchemaNormalizer;
use Art35rennes\DaisyKit\FormKit\FormSchemaValidator;
use Art35rennes\DaisyKit\FormKit\FormSubmissionEvaluator;
use Art35rennes\DaisyKit\FormKit\LaravelRuleMapper;

function validFormSchema(array $overrides = []): array
{
    return array_replace_recursive([
        'version' => '1.0',
        'id' => 'quote',
        'fields' => [
            [
                'id' => 'quantity',
                'type' => 'number',
                'name' => 'quantity',
                'rules' => ['required', 'min:1'],
            ],
        ],
    ], $overrides);
}

it('validates form schema ids, types and versions', function () {
    $validator = new FormSchemaValidator;

    $errors = $validator->validate(validFormSchema([
        'version' => '2.0',
        'fields' => [
            ['id' => 'quantity', 'type' => 'number', 'name' => 'quantity'],
            ['id' => 'quantity', 'type' => 'unknown', 'name' => 'quantity'],
        ],
    ]));

    expect(array_column($errors, 'code'))
        ->toContain('unsupported_version')
        ->toContain('duplicate_id')
        ->toContain('unknown_field_type')
        ->toContain('duplicate_name');
});

it('detects JSONata dependency cycles', function () {
    $validator = new FormSchemaValidator;

    $errors = $validator->validate(validFormSchema([
        'fields' => [
            [
                'id' => 'a',
                'type' => 'text',
                'name' => 'a',
                'computed' => ['type' => 'jsonata', 'expression' => 'values.b', 'dependsOn' => ['b']],
            ],
            [
                'id' => 'b',
                'type' => 'text',
                'name' => 'b',
                'computed' => ['type' => 'jsonata', 'expression' => 'values.a', 'dependsOn' => ['a']],
            ],
        ],
    ]));

    expect(array_column($errors, 'code'))->toContain('dependency_cycle');
});

it('accepts multi step schemas with nested sections and common fields', function () {
    $validator = new FormSchemaValidator;

    $errors = $validator->validate(validFormSchema([
        'layout' => ['type' => 'multi-step'],
        'fields' => [
            [
                'id' => 'contact',
                'type' => 'wizardStep',
                'label' => 'Contact',
                'fields' => [
                    [
                        'id' => 'identity',
                        'type' => 'section',
                        'label' => 'Identity',
                        'fields' => [
                            ['id' => 'name', 'type' => 'text', 'name' => 'name', 'rules' => ['required']],
                            ['id' => 'email', 'type' => 'email', 'name' => 'email', 'rules' => ['required', 'email']],
                            ['id' => 'phone', 'type' => 'tel', 'name' => 'phone'],
                            ['id' => 'website', 'type' => 'url', 'name' => 'website'],
                            ['id' => 'message', 'type' => 'textarea', 'name' => 'message', 'rules' => ['nullable']],
                        ],
                    ],
                ],
            ],
            [
                'id' => 'confirmation',
                'type' => 'wizardStep',
                'label' => 'Confirmation',
                'fields' => [
                    ['id' => 'appointment_date', 'type' => 'date', 'name' => 'appointment_date'],
                    ['id' => 'appointment_time', 'type' => 'time', 'name' => 'appointment_time'],
                    ['id' => 'starts_at', 'type' => 'datetime-local', 'name' => 'starts_at'],
                    ['id' => 'billing_month', 'type' => 'month', 'name' => 'billing_month'],
                    ['id' => 'brand_color', 'type' => 'color', 'name' => 'brand_color'],
                    ['id' => 'terms', 'type' => 'checkbox', 'name' => 'terms', 'rules' => ['accepted']],
                ],
            ],
        ],
    ]));

    expect($errors)->toBe([]);
});

it('keeps the builder field catalog aligned with canonical schema field types', function () {
    $catalog = new FormFieldCatalog;

    expect(collect($catalog->definitions())->pluck('type')->all())
        ->toEqualCanonicalizing(FormSchemaNormalizer::FieldTypes);

    $staticTextProperties = collect($catalog->propertiesFor('staticText'))->pluck('path')->all();
    expect($staticTextProperties)
        ->toContain('id')
        ->toContain('text')
        ->toContain('ui.width')
        ->not->toContain('name');

    foreach (FormSchemaNormalizer::ContainerTypes as $containerType) {
        expect(collect($catalog->propertiesFor($containerType))->pluck('path')->all())
            ->toContain('ui.width')
            ->not->toContain('name');
    }

    expect(collect($catalog->propertiesFor('color'))->pluck('path')->all())
        ->toContain('name')
        ->toContain('attrs.mode')
        ->toContain('attrs.dropdown')
        ->toContain('attrs.showFormatToggle');
});

it('rejects unsupported form layout types', function () {
    $validator = new FormSchemaValidator;

    $errors = $validator->validate(validFormSchema([
        'layout' => ['type' => 'kanban'],
    ]));

    expect(array_column($errors, 'code'))->toContain('unknown_layout_type');
});

it('maps simple rules to Laravel rules and skips JSONata rules', function () {
    $mapper = new LaravelRuleMapper;

    expect($mapper->mapRules([
        'required',
        'length:5',
        'pattern:/^[A-Z]+$/',
        ['type' => 'jsonata', 'expression' => 'true', 'dependsOn' => [], 'message' => 'Ok'],
    ]))->toBe([
        'required',
        'size:5',
        'regex:/^[A-Z]+$/',
    ]);
});

it('fails closed when JSONata expressions need an evaluator', function () {
    $evaluator = new FormSubmissionEvaluator;

    $result = $evaluator->evaluate(validFormSchema([
        'fields' => [
            [
                'id' => 'quantity',
                'type' => 'number',
                'name' => 'quantity',
                'rules' => [
                    ['type' => 'jsonata', 'expression' => 'field.value > 0', 'dependsOn' => ['quantity'], 'message' => 'Positive only'],
                ],
            ],
        ],
    ]), ['quantity' => 1]);

    expect($result['errors']['_jsonata'])->toBe(['evaluator_missing']);
});

it('applies batch JSONata results for visibility, validation and computed values', function () {
    $jsonata = new class extends JsonataEvaluator
    {
        public array $evaluations = [];

        public function evaluateBatch(array $evaluations, array $context, array $options = []): array
        {
            $this->evaluations = $evaluations;

            return [
                'visible:company_vat' => ['value' => false],
                'computed:total' => ['value' => 20],
                'rule:quantity:0' => ['value' => true],
            ];
        }
    };

    $evaluator = new FormSubmissionEvaluator(jsonataEvaluator: $jsonata);
    $result = $evaluator->evaluate(validFormSchema([
        'fields' => [
            [
                'id' => 'quantity',
                'type' => 'number',
                'name' => 'quantity',
                'rules' => [
                    ['type' => 'jsonata', 'expression' => 'field.value > 0', 'dependsOn' => ['quantity'], 'message' => 'Positive only'],
                ],
            ],
            [
                'id' => 'company_vat',
                'type' => 'text',
                'name' => 'company_vat',
                'visibleWhen' => ['type' => 'jsonata', 'expression' => "values.customer_type = 'company'", 'dependsOn' => ['customer_type']],
            ],
            ['id' => 'customer_type', 'type' => 'text', 'name' => 'customer_type'],
            [
                'id' => 'total',
                'type' => 'number',
                'name' => 'total',
                'computed' => ['type' => 'jsonata', 'expression' => 'values.quantity * 10', 'dependsOn' => ['quantity'], 'mode' => 'readonly'],
            ],
        ],
    ]), [
        'quantity' => 2,
        'customer_type' => 'personal',
        'company_vat' => 'FR',
        'total' => 0,
    ]);

    expect($result['errors'])->toBe([])
        ->and($result['normalizedData'])
        ->not->toHaveKey('company_vat')
        ->and($result['normalizedData']['total'])->toBe(20)
        ->and($jsonata->evaluations)->toHaveCount(3);
});

it('maps unknown function errors returned by the host evaluator', function () {
    $jsonata = new class extends JsonataEvaluator
    {
        public function evaluateBatch(array $evaluations, array $context, array $options = []): array
        {
            return [
                'computed:external_id' => ['error' => ['code' => 'unknown_function']],
            ];
        }
    };

    $evaluator = new FormSubmissionEvaluator(jsonataEvaluator: $jsonata);
    $result = $evaluator->evaluate(validFormSchema([
        'fields' => [
            [
                'id' => 'external_id',
                'type' => 'hidden',
                'name' => 'external_id',
                'computed' => ['type' => 'jsonata', 'expression' => '$uuid()', 'dependsOn' => [], 'mode' => 'hidden'],
            ],
        ],
    ]), []);

    expect($result['errors']['external_id'])->toBe(['unknown_function']);
});

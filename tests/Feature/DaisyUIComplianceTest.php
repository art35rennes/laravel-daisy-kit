<?php

use Illuminate\Support\Facades\View;

describe('DaisyUI Compliance Tests', function () {
    describe('Border Compliance', function () {
        it('uses card-border instead of border border-base-300 in card components', function () {
            $html = View::make('daisy::components.ui.layout.card', [
                'slot' => 'Test content',
            ])->render();

            // Should not contain the old pattern
            expect($html)
                ->not->toContain('border border-base-300')
                ->toContain('card');
        });

        it('uses card-border in changelog components', function () {
            $html = View::make('daisy::components.ui.changelog.changelog-change-item', [
                'description' => 'Test change',
            ])->render();

            expect($html)
                ->toContain('card-border')
                ->not->toContain('border border-base-300');
        });

        it('uses card-border in transfer component', function () {
            $html = View::make('daisy::components.ui.advanced.transfer', [
                'source' => [['data' => 'Item 1']],
                'target' => [],
            ])->render();

            expect($html)
                ->toContain('card-border')
                ->not->toContain('border border-base-300');
        });
    });

    describe('Shadow Compliance', function () {
        it('uses shadow instead of shadow-sm/md/lg in components', function () {
            $html = View::make('daisy::components.ui.changelog.changelog-toolbar', [
                'showSearch' => false,
                'showFilters' => false,
            ])->render();

            // Should use shadow (daisyUI class) not shadow-sm/md/lg
            expect($html)
                ->toContain('shadow')
                ->not->toContain('shadow-sm')
                ->not->toContain('shadow-md')
                ->not->toContain('shadow-lg')
                ->not->toContain('shadow-2xl');
        });

        it('uses shadow in notification-bell component', function () {
            $html = View::make('daisy::components.ui.communication.notification-bell', [
                'notifications' => [],
            ])->render();

            // Check that shadow is used, not shadow-sm/md/lg
            expect($html)
                ->not->toContain('shadow-sm')
                ->not->toContain('shadow-md')
                ->not->toContain('shadow-lg');
        });
    });

    describe('Rounded Compliance', function () {
        it('uses rounded-box instead of rounded-2xl/3xl in changelog components', function () {
            $html = View::make('daisy::components.ui.changelog.changelog-version-item', [
                'version' => '1.0.0',
                'date' => '2024-01-01',
                'items' => [],
            ])->render();

            expect($html)
                ->toContain('rounded-box')
                ->not->toContain('rounded-2xl')
                ->not->toContain('rounded-3xl')
                ->not->toContain('rounded-4xl');
        });

        it('uses rounded-box in changelog-toolbar', function () {
            $html = View::make('daisy::components.ui.changelog.changelog-toolbar', [
                'showSearch' => false,
                'showFilters' => false,
            ])->render();

            expect($html)
                ->toContain('rounded-box')
                ->not->toContain('rounded-2xl')
                ->not->toContain('rounded-3xl');
        });
    });

    describe('Color Compliance', function () {
        it('uses daisyUI semantic colors instead of fixed Tailwind colors', function () {
            $html = View::make('daisy::components.ui.advanced.rating', [
                'name' => 'test-rating',
                'count' => 5,
                'value' => 3,
            ])->render();

            // Should use bg-warning (daisyUI) not bg-yellow-400 (fixed Tailwind)
            expect($html)
                ->not->toContain('bg-yellow-400')
                ->toContain('bg-warning');
        });
    });

    describe('Advanced Components Compliance', function () {
        it('uses card-border in fieldset component when bordered', function () {
            $html = View::make('daisy::components.ui.advanced.fieldset', [
                'legend' => 'Test',
                'bordered' => true,
                'slot' => 'Content',
            ])->render();

            expect($html)
                ->toContain('card-border')
                ->not->toContain('border border-base-300');
        });

        it('uses card-border in collapse component when bordered', function () {
            $html = View::make('daisy::components.ui.advanced.collapse', [
                'title' => 'Test',
                'bordered' => true,
                'slot' => 'Content',
            ])->render();

            expect($html)
                ->toContain('card-border')
                ->not->toContain('border border-base-300');
        });

        it('uses card-border in code-editor component', function () {
            $html = View::make('daisy::components.ui.advanced.code-editor', [
                'language' => 'javascript',
                'value' => 'console.log("test");',
            ])->render();

            expect($html)
                ->toContain('card-border')
                ->not->toContain('border border-base-300');
        });

        it('uses card-border in chart component', function () {
            $html = View::make('daisy::components.ui.advanced.chart', [
                'type' => 'bar',
                'labels' => ['A', 'B', 'C'],
                'datasets' => [['label' => 'Test', 'data' => [1, 2, 3]]],
            ])->render();

            expect($html)
                ->toContain('card-border')
                ->not->toContain('border border-base-300');
        });

        it('uses shadow instead of shadow-2xl in theme-controller dropdown', function () {
            $html = View::make('daisy::components.ui.advanced.theme-controller', [
                'themes' => ['light', 'dark'],
                'variant' => 'dropdown',
            ])->render();

            expect($html)
                ->toContain('shadow')
                ->not->toContain('shadow-2xl');
        });
    });

    describe('Communication Components Compliance', function () {
        it('uses card-border in notification-bell component', function () {
            $html = View::make('daisy::components.ui.communication.notification-bell', [
                'notifications' => [],
            ])->render();

            expect($html)
                ->toContain('card-border')
                ->not->toContain('border border-base-300');
        });

        it('uses shadow instead of shadow-lg/2xl in chat-widget', function () {
            $html = View::make('daisy::components.ui.communication.chat-widget', [
                'messages' => [],
            ])->render();

            expect($html)
                ->toContain('shadow')
                ->not->toContain('shadow-lg')
                ->not->toContain('shadow-2xl');
        });
    });

    describe('File Components Compliance', function () {
        it('uses card-border in file-preview component', function () {
            $html = View::make('daisy::components.ui.data-display.file-preview', [
                'url' => 'https://example.com/image.jpg',
                'type' => 'image',
            ])->render();

            expect($html)
                ->toContain('card-border')
                ->not->toContain('border border-base-300');
        });
    });

    describe('Navbar Compliance', function () {
        it('uses shadow instead of shadow-sm/md/lg in navbar', function () {
            $html = View::make('daisy::components.ui.navigation.navbar', [
                'shadow' => true,
                'start' => 'Start',
            ])->render();

            // Navbar should use shadow, not shadow-sm/md/lg
            expect($html)
                ->toContain('shadow')
                ->not->toContain('shadow-sm')
                ->not->toContain('shadow-md')
                ->not->toContain('shadow-lg');
        });
    });
});

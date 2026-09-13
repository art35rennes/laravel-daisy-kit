<?php

declare(strict_types=1);

it('submits a drawn signature and preserves its pixels through responsive resize before clearing', function (): void {
    $page = $this->visit('/signature')->on()->desktop()->waitForEvent('networkidle')->assertNoSmoke()
        ->assertScript("document.querySelector('[data-daisy-kit-module=signature]').dataset.daisyKitState === 'ready'");
    $page->script(<<<'JS'
        (() => {
            const canvas = document.querySelector('[data-daisy-kit-signature-canvas]');
            const bounds = canvas.getBoundingClientRect();
            const pointer = (type, offset, buttons) => canvas.dispatchEvent(new PointerEvent(type, {
                bubbles: true, pointerId: 1, pointerType: 'pen', isPrimary: true,
                button: 0, buttons, pressure: 0.5,
                clientX: bounds.left + offset, clientY: bounds.top + 30,
            }));
            pointer('pointerdown', 20, 1);
            pointer('pointermove', 60, 1);
            pointer('pointerup', 100, 0);
        })()
        JS);
    $page->assertScript(<<<'JS'
        (() => {
            const input = document.querySelector('[data-daisy-kit-signature-value]');
            return new FormData(input.form).get('approval_signature').startsWith('data:image/png;base64,');
        })()
        JS);

    $page->resize(768, 1000)->wait(0.2)
        ->assertScript(<<<'JS'
            (() => {
                const canvas = document.querySelector('[data-daisy-kit-signature-canvas]');
                const pixels = canvas.getContext('2d').getImageData(0, 0, canvas.width, canvas.height).data;
                return pixels.some((value, index) => index % 4 === 3 && value > 0)
                    && document.querySelector('[data-daisy-kit-signature-value]').value.startsWith('data:image/png;base64,');
            })()
            JS)
        ->click('[data-daisy-kit-signature-clear]')
        ->assertScript("document.querySelector('[data-daisy-kit-signature-value]').value === ''")
        ->assertNoSmoke();
})->group('browser');

it('preserves an imported PNG through resize without inventing stroke history', function (): void {
    $page = $this->visit('/signature')->on()->desktop()->waitForEvent('networkidle')->assertNoSmoke()
        ->assertScript("document.querySelector('[data-daisy-kit-module=signature]').dataset.daisyKitState === 'ready'");
    $page->script(<<<'JS'
        (async () => {
            const resource = performance.getEntriesByType('resource').find(({ name }) => /\/signature-[^/]+\.js/.test(name));
            const module = await import(resource.name);
            const root = document.querySelector('[data-daisy-kit-module="signature"]');
            const image = document.createElement('canvas');
            image.width = root.querySelector('canvas').width;
            image.height = root.querySelector('canvas').height;
            image.getContext('2d').fillRect(10, 10, 40, 40);
            image.getContext('2d').fillRect(250, 10, 40, 40);
            const instance = module.getInstance(root);
            await instance.setValue(image.toDataURL());
            const canvas = root.querySelector('canvas');
            const bounds = canvas.getBoundingClientRect();
            for (const [type, offset, buttons] of [['pointerdown', 20, 1], ['pointermove', 60, 1], ['pointerup', 100, 0]]) {
                canvas.dispatchEvent(new PointerEvent(type, {
                    bubbles: true, pointerId: 1, pointerType: 'pen', isPrimary: true,
                    button: 0, buttons, pressure: 0.5, clientX: bounds.left + offset, clientY: bounds.top + 80,
                }));
            }
            if (!instance.undo() || instance.isEmpty() || instance.toData().length !== 0) {
                throw new Error('Undo lost the imported signature or retained the new stroke.');
            }
        })()
        JS);
    $page->resize(320, 1000)->wait(0.2)->resize(1440, 1000)->wait(0.2)
        ->assertScript(<<<'JS'
            (() => {
                const canvas = document.querySelector('[data-daisy-kit-signature-canvas]');
                const pixels = canvas.getContext('2d').getImageData(0, 0, canvas.width, canvas.height).data;
                return canvas.getContext('2d').getImageData(260, 20, 1, 1).data[3] > 0
                    && pixels.some((value, index) => index % 4 === 3 && value > 0)
                    && document.querySelector('[data-daisy-kit-signature-value]').value.startsWith('data:image/png;base64,');
            })()
            JS)
        ->click('[data-daisy-kit-signature-clear]')
        ->assertScript("document.querySelector('[data-daisy-kit-signature-value]').value === ''")
        ->assertNoSmoke();
})->group('browser');

it('cancels image decoding on clear and unmount without late pixels or form changes', function (): void {
    $page = $this->visit('/signature')->on()->desktop()->waitForEvent('networkidle')
        ->assertScript("document.querySelector('[data-daisy-kit-module=signature]').dataset.daisyKitState === 'ready'");
    $result = $page->script(<<<'JS'
        (async () => {
            const resource = performance.getEntriesByType('resource').find(({ name }) => /\/signature-[^/]+\.js/.test(name));
            const module = await import(resource.name);
            const root = document.querySelector('[data-daisy-kit-module="signature"]');
            const canvas = root.querySelector('canvas');
            const image = document.createElement('canvas');
            image.width = 50;
            image.height = 50;
            image.getContext('2d').fillRect(0, 0, 50, 50);
            const instance = module.getInstance(root);
            const pendingClear = instance.setValue(image.toDataURL());
            instance.clear();
            const canceledClear = await pendingClear;
            const pendingUnmount = instance.setValue(image.toDataURL());
            module.unmount(root);
            const canceledUnmount = await pendingUnmount;
            await new Promise((resolve) => setTimeout(resolve, 50));
            const pixels = canvas.getContext('2d').getImageData(0, 0, canvas.width, canvas.height).data;
            return canceledClear === false && canceledUnmount === false
                && instance.clear() === false
                && root.querySelector('[data-daisy-kit-signature-value]').value === ''
                && !pixels.some((value, index) => index % 4 === 3 && value > 0);
        })()
        JS);

    expect($result)->toBeTrue();
    $page->assertNoSmoke();
})->group('browser');

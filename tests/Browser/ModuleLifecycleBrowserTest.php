<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Vite;

it('isolates instances and restores each public module through unmount and remount', function (string $name): void {
    $page = $this->visit('/'.$name)->waitForEvent('networkidle')->assertNoSmoke();
    $page->script(str_replace(['__MODULE__', '__ENTRY__'], [json_encode($name, JSON_THROW_ON_ERROR), json_encode(Vite::asset('../dist/'.$name.'.js'), JSON_THROW_ON_ERROR)], <<<'JS'
        (async () => {
            const name = __MODULE__;
            const module = await import(__ENTRY__);
            const root = document.querySelector(`[data-daisy-kit-module="${name}"]`);
            const original = module.getInstance(root);
            if (!original || module.mount(root) !== original || !module.unmount(root)) {
                throw new Error('Initial lifecycle failed: ' + name);
            }
            const sibling = root.cloneNode(true);
            sibling.removeAttribute('id');
            sibling.querySelectorAll('[id]').forEach((element) => element.removeAttribute('id'));
            root.after(sibling);
            const first = module.mount(root);
            const second = module.mount(sibling);
            if (!first || !second || first === second || module.getInstance(sibling) !== second) {
                throw new Error('Instance isolation failed: ' + name);
            }
            if (!module.unmount(sibling) || module.getInstance(sibling) !== null || module.unmount(sibling)) {
                throw new Error('Sibling teardown failed: ' + name);
            }
            sibling.remove();
            if (module.getInstance(root) !== first || !module.unmount(root)) {
                throw new Error('Sibling teardown affected first instance: ' + name);
            }
            if (!module.mount(root)) throw new Error('Remount failed: ' + name);
            root.dataset.lifecycleVerified = 'true';
        })()
        JS));
    $page->assertScript("document.querySelector('[data-daisy-kit-module=\"{$name}\"]').dataset.lifecycleVerified === 'true'")
        ->assertScript("document.querySelector('[data-daisy-kit-module=\"{$name}\"]').dataset.daisyKitState === 'ready'")
        ->assertNoSmoke();
})->with([
    'table', 'tree', 'blueprint', 'file-preview', 'map', 'copyable', 'combobox',
    'signature', 'truncate', 'scrollspy', 'transfer-list',
])->group('browser');

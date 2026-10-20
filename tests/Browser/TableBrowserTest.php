<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Vite;

it('shows readable modal row details and returns focus after close and Escape', function (int $width, string $theme): void {
    $table = '#service-health-table';
    $details = "{$table} [data-daisy-kit-table-detail-toggle=api]";
    $dialog = "{$table} dialog[data-daisy-kit-table-detail=api]";
    $page = $this->visit('/table')->resize($width, 900)->waitForEvent('networkidle');
    $page->script(str_replace(
        ['__ENTRY__', '__THEME__'],
        [json_encode(Vite::asset('../dist/table.js'), JSON_THROW_ON_ERROR), json_encode($theme, JSON_THROW_ON_ERROR)],
        <<<'JS'
        (async () => {
            document.documentElement.dataset.theme = __THEME__;
            const module = await import(__ENTRY__);
            const root = document.querySelector('#service-health-table');
            module.unmount(root);
            const configurationElement = root.querySelector('[data-daisy-kit-config]');
            const configuration = JSON.parse(configurationElement.textContent);
            configuration.rowDetails.mode = 'modal';
            configuration.rows[0].summary = 'Presence: 09:00 to 18:00\nDiagnostic: <img src=x onerror=alert(1)>\nDesk: A12';
            configurationElement.textContent = JSON.stringify(configuration);
            module.mount(root);
            root.dataset.modalReady = 'true';
        })()
        JS,
    ));
    $page->assertScript("document.querySelector('{$table}').dataset.modalReady === 'true'")
        ->keys($details, 'Enter')
        ->assertVisible("{$dialog} .modal-box")
        ->assertScript(<<<'JS'
            (() => {
                const dialog = document.querySelector('#service-health-table dialog');
                const box = dialog.querySelector('.modal-box');
                const detail = box.querySelector('.daisy-kit-table__detail-content');
                const bounds = box.getBoundingClientRect();
                const trigger = document.querySelector('#service-health-table [data-daisy-kit-table-detail-toggle=api]');
                return dialog.open
                    && document.getElementById(dialog.getAttribute('aria-labelledby')).textContent === 'Show details'
                    && detail.textContent.includes('\nDiagnostic: <img src=x onerror=alert(1)>\n')
                    && !detail.querySelector('img')
                    && getComputedStyle(detail).whiteSpace === 'pre-wrap'
                    && bounds.left >= 0 && bounds.right <= innerWidth
                    && trigger.getAttribute('aria-haspopup') === 'dialog'
                    && !trigger.hasAttribute('aria-expanded');
            })()
            JS)
        ->assertNoSmoke()
        ->click("{$dialog} .modal-action button")
        ->assertCount($dialog, 0)
        ->assertScript("document.activeElement.dataset.daisyKitTableDetailToggle === 'api'")
        ->keys($details, 'Enter')
        ->assertVisible("{$dialog} .modal-box")
        ->keys("{$dialog} button", 'Escape')
        ->assertCount($dialog, 0)
        ->assertScript("document.activeElement.dataset.daisyKitTableDetailToggle === 'api'")
        ->assertNoSmoke();
})->with([390, 1280])->with(['light', 'dark'])->group('browser');

it('recovers from a failed server request without changing the client table', function (): void {
    $server = '#server-queue-table';
    $client = '[data-daisy-kit-module="table"]:has([data-daisy-kit-table-filter=team])';
    $page = $this->visit('/table')->waitForEvent('networkidle');

    $page->assertCount("{$server} tbody tr", 3)
        ->assertCount("{$client} tbody tr", 4);
    $page->script(<<<'JS'
        window.originalTableFetch = window.fetch;
        window.fetch = (request, options) => {
            if (String(request).includes('/_daisy-kit-test/table/rows')) {
                window.fetch = window.originalTableFetch;
                return new Promise((resolve) => {
                    window.finishFailedTableRequest = () => resolve(new Response('', { status: 503 }));
                });
            }
            return window.originalTableFetch(request, options);
        };
        document.querySelector('#server-queue-table').addEventListener('daisy-kit:table:error', (event) => {
            window.tableSourceError = event.detail;
        });
        JS);

    $page->fill("{$server} [data-daisy-kit-table-filter=customer]", 'Maison')
        ->click("{$server} [data-daisy-kit-table-apply-filters]")
        ->assertScript("document.querySelector('{$server}').getAttribute('aria-busy') === 'true'")
        ->assertScript("document.querySelector('{$server}').dataset.daisyKitState === 'loading'");
    $page->script('window.finishFailedTableRequest()');
    $page->assertScript("document.querySelector('{$server}').dataset.daisyKitState === 'error'")
        ->assertScript("window.tableSourceError.code === 'source-unavailable' && window.tableSourceError.message.length > 0")
        ->assertScript("document.querySelector('{$server}').getAttribute('aria-busy') === 'false'")
        ->assertScript("!document.querySelector('{$server} [data-daisy-kit-status]').hidden")
        ->assertCount("{$client} tbody tr", 4)
        ->fill("{$server} [data-daisy-kit-table-filter=customer]", 'Atelier')
        ->click("{$server} [data-daisy-kit-table-apply-filters]")
        ->assertScript("document.querySelector('{$server}').dataset.daisyKitState === 'ready'")
        ->assertScript("document.querySelector('{$server} [data-daisy-kit-status]').hidden")
        ->assertCount("{$server} tbody tr", 1)
        ->assertSee('CASE-1042')
        ->assertCount("{$client} tbody tr", 4)
        ->assertNoSmoke();
})->group('browser');

it('restores URL-backed client filters and page size after a full reload', function (): void {
    $client = '[data-daisy-kit-module="table"]:has([data-daisy-kit-table-filter=team])';
    $page = $this->visit('/table')->waitForEvent('networkidle');

    $page->assertScript("document.querySelector('{$client}').dataset.daisyKitState === 'ready'")
        ->select("{$client} [data-daisy-kit-table-filter=team]", 'Research')
        ->assertCount("{$client} tbody tr", 3)
        ->select("{$client} [data-daisy-kit-table-page-size]", '8')
        ->assertScript("document.querySelector('{$client} [data-daisy-kit-table-page-size]').value === '8'")
        ->assertCount("{$client} tbody tr", 3)
        ->assertCount('#server-queue-table tbody tr', 3)
        ->assertScript('location.search.length > 0')
        ->refresh()
        ->waitForEvent('networkidle')
        ->assertScript("document.querySelector('{$client}').dataset.daisyKitState === 'ready'")
        ->assertScript("document.querySelector('{$client} [data-daisy-kit-table-filter=team]').value === 'Research'")
        ->assertScript("document.querySelector('{$client} [data-daisy-kit-table-page-size]').value === '8'")
        ->assertCount("{$client} tbody tr", 3)
        ->assertSee('Katherine Johnson')
        ->assertSee('Dorothy Vaughan')
        ->assertSee('Joan Clarke')
        ->assertCount('#server-queue-table tbody tr', 3)
        ->assertScript("document.querySelector('#server-queue-table [data-daisy-kit-table-filter=customer]').value === ''")
        ->assertNoSmoke();
})->group('browser');

it('spaces row actions without changing table semantics or keyboard actions', function (int $width): void {
    $table = '#service-health-table';
    $details = "{$table} [data-daisy-kit-table-detail-toggle=api]";
    $inspect = "{$table} tbody tr:first-child [data-daisy-kit-table-row-action=inspect]";
    $page = $this->visit('/table')->resize($width, 900)->waitForEvent('networkidle');

    $page->assertScript(<<<'JS'
        (() => {
            const group = document.querySelector('#service-health-table .daisy-kit-table__row-actions');
            const buttons = [...group.querySelectorAll('button')];
            const [details, inspect, restart] = buttons.map(button => button.getBoundingClientRect());
            const style = getComputedStyle(group);
            return getComputedStyle(group.parentElement).display === 'table-cell'
                && style.display === 'inline-flex'
                && style.flexWrap === 'wrap'
                && Number.parseFloat(style.columnGap) === 8
                && inspect.left - details.right >= 7.5
                && restart.left - inspect.right >= 7.5
                && buttons.map(button => button.textContent).join('|') === 'Show details|Inspect service|Restart service'
                && buttons[2].disabled;
        })()
        JS)
        ->assertScript('document.documentElement.scrollWidth <= window.innerWidth');
    $page->script(<<<'JS'
        document.querySelector('#service-health-table').addEventListener('daisy-kit:table:row-action', event => {
            window.tableRowAction = event.detail;
        });
        document.querySelector('#service-health-table [data-daisy-kit-table-detail-toggle=api]').focus();
        JS);
    $page->keys($details, 'Tab')
        ->assertScript("document.activeElement.dataset.daisyKitTableRowAction === 'inspect'")
        ->keys($inspect, 'Enter')
        ->assertScript("window.tableRowAction.id === 'inspect' && window.tableRowAction.rowId === 'api' && window.tableRowAction.row.service === 'Public API'")
        ->keys($inspect, 'Shift+Tab')
        ->assertScript("document.activeElement.dataset.daisyKitTableDetailToggle === 'api'")
        ->keys($details, 'Enter')
        ->assertSee('12 instances across three regions. Last deployment succeeded.')
        ->assertScript("document.querySelector('#service-health-table [data-daisy-kit-table-detail-toggle=api]').getAttribute('aria-expanded') === 'true'")
        ->keys($details, 'Enter')
        ->assertCount("{$table} [data-daisy-kit-table-detail=api]", 0)
        ->assertNoSmoke();
})->with([390, 1280])->group('browser');

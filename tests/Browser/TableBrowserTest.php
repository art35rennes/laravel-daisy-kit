<?php

declare(strict_types=1);

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

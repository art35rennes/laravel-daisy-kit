import { cpSync, existsSync, mkdtempSync, readFileSync, rmSync, statSync, writeFileSync } from 'node:fs';
import { createServer } from 'node:http';
import { tmpdir } from 'node:os';
import { extname, relative, resolve } from 'node:path';
import { spawnSync } from 'node:child_process';
import { chromium } from 'playwright';

const repositoryRoot = resolve(import.meta.dirname, '../..');
const fixtureRoot = resolve(repositoryRoot, 'tests/fixtures/vite-host');
const hostRoot = mkdtempSync(resolve(tmpdir(), 'daisy-kit-vite-host-'));
const entryStems = ['forms-viewer', 'forms-builder', 'table', 'tree', 'blueprint', 'file-preview', 'map'];
const contentTypes = {
    '.css': 'text/css; charset=utf-8',
    '.html': 'text/html; charset=utf-8',
    '.js': 'text/javascript; charset=utf-8',
    '.json': 'application/json; charset=utf-8',
    '.png': 'image/png',
    '.svg': 'image/svg+xml',
};

function run(command, arguments_, options = {}) {
    const result = spawnSync(command, arguments_, {
        cwd: hostRoot,
        encoding: 'utf8',
        ...options,
    });

    if (result.status !== 0) {
        throw new Error(`${command} ${arguments_.join(' ')} failed:\n${result.stdout}\n${result.stderr}`);
    }
}

function gitOutput(arguments_, cwd) {
    const result = spawnSync('git', arguments_, { cwd, encoding: 'utf8' });

    if (result.status !== 0) {
        throw new Error(`git ${arguments_.join(' ')} failed:\n${result.stdout}\n${result.stderr}`);
    }

    return result.stdout.trim();
}

function activePackageReference() {
    const branch = gitOutput(['branch', '--show-current'], repositoryRoot);
    const commit = gitOutput(['rev-parse', 'HEAD'], repositoryRoot);

    if (!/^[A-Za-z0-9][A-Za-z0-9._/-]*$/.test(branch)) {
        throw new Error('The Vite host fixture requires a checked-out package branch with a Composer-compatible name.');
    }

    if (!/^[a-f0-9]{40}$/.test(commit)) {
        throw new Error('The Vite host fixture could not resolve the active package commit.');
    }

    return { branch, commit, version: `dev-${branch}#${commit}` };
}

function startHost(buildRoot) {
    return new Promise((resolveServer, rejectServer) => {
        const server = createServer((request, response) => {
            const path = new URL(request.url ?? '/', 'http://localhost').pathname;

            if (path === '/preview.txt') {
                response.writeHead(200, {
                    'Content-Security-Policy': "default-src 'none'",
                    'Content-Type': 'text/plain; charset=utf-8',
                });
                response.end('Sandboxed Vite host file preview');

                return;
            }

            const requested = path === '/' ? 'index.html' : relative('/', path);
            const file = resolve(buildRoot, requested);

            if (relative(buildRoot, file).startsWith('..') || !existsSync(file) || !statSync(file).isFile()) {
                response.writeHead(404, { 'Content-Type': 'text/plain; charset=utf-8' });
                response.end('Not found');

                return;
            }

            response.writeHead(200, {
                'Content-Security-Policy': "default-src 'none'; base-uri 'none'; connect-src 'self'; form-action 'none'; frame-src 'self'; img-src 'self' data: blob:; object-src 'none'; script-src 'self'; script-src-attr 'none'; style-src 'self'; style-src-attr 'none'",
                'Content-Type': contentTypes[extname(file)] ?? 'application/octet-stream',
            });
            response.end(readFileSync(file));
        });

        server.once('error', rejectServer);
        server.listen(0, '127.0.0.1', () => resolveServer(server));
    });
}

function closeServer(server) {
    return new Promise((resolveServer) => server.close(resolveServer));
}

async function fixtureModuleStates(page) {
    return page.evaluate(() => [...document.querySelectorAll('[data-daisy-kit-module]')].map((root) => ({
        module: root.getAttribute('data-daisy-kit-module'),
        state: root.getAttribute('data-daisy-kit-state') ?? 'missing',
        status: root.querySelector('[data-daisy-kit-status]')?.textContent?.trim() ?? '',
        viteHostError: root.dataset.viteHostError ?? '',
    })));
}

let server;
let browser;

try {
    cpSync(fixtureRoot, hostRoot, { recursive: true });

    const activePackage = activePackageReference();

    const composerJson = readFileSync(resolve(hostRoot, 'composer.json'), 'utf8')
        .replace('__PACKAGE_ROOT__', repositoryRoot)
        .replace('__PACKAGE_BRANCH_VERSION__', activePackage.version);
    writeFileSync(resolve(hostRoot, 'composer.json'), composerJson);

    run('composer', ['install', '--no-interaction', '--no-scripts', '--prefer-dist']);
    run('npm', ['ci', '--no-audit', '--ignore-scripts']);
    run(process.execPath, [resolve(hostRoot, 'node_modules/vite/bin/vite.js'), 'build', '--config', 'vite.config.js']);

    const manifest = JSON.parse(readFileSync(resolve(hostRoot, 'build/.vite/manifest.json'), 'utf8'));

    const distRoot = resolve(hostRoot, 'vendor/art35rennes/laravel-daisy-kit/dist');
    const installedCommit = gitOutput(['-C', resolve(hostRoot, 'vendor/art35rennes/laravel-daisy-kit'), 'rev-parse', 'HEAD'], hostRoot);
    const everyEntryExists = entryStems.every((entry) => existsSync(resolve(distRoot, `${entry}.js`)) && existsSync(resolve(distRoot, `${entry}.css`)));

    if (!everyEntryExists || Object.keys(manifest).length === 0 || installedCommit !== activePackage.commit) {
        throw new Error(`The fresh Composer host did not resolve and build the active Daisy Kit package commit (${activePackage.commit}; installed ${installedCommit}).`);
    }

    const buildRoot = resolve(hostRoot, 'build');
    const axeSource = readFileSync(resolve(repositoryRoot, 'vendor/pestphp/pest-plugin-browser/resources/js/axe.min.js'), 'utf8');
    writeFileSync(resolve(buildRoot, 'axe.min.js'), axeSource);
    const responses = [];
    const consoleErrors = [];
    server = await startHost(buildRoot);
    const address = server.address();
    const url = `http://127.0.0.1:${address.port}`;
    browser = await chromium.launch({ headless: true });
    const page = await browser.newPage();
    await page.addInitScript(() => {
        Object.defineProperty(window, 'crypto', { configurable: true, value: {} });
        Object.defineProperty(navigator, 'geolocation', {
            configurable: true,
            value: {
                getCurrentPosition: (success) => success({ coords: { latitude: 48.1173, longitude: -1.6778 } }),
            },
        });
    });
    page.on('response', (response) => {
        if (response.status() >= 400) responses.push(`${response.status()} ${response.url()}`);
    });
    page.on('console', (message) => {
        if (message.type() === 'error') consoleErrors.push(message.text());
    });

    await page.goto(url, { waitUntil: 'networkidle' });
    try {
        await page.waitForFunction(() => {
            const expected = {
                blueprint: 'ready',
                'file-preview': 'ready',
                'forms-builder': 'empty',
                'forms-viewer': 'ready',
                map: 'ready',
                table: 'ready',
                tree: 'ready',
            };
            const roots = [...document.querySelectorAll('[data-daisy-kit-module]')];

            return roots.length === 9
                && roots.every((root) => root.dataset.daisyKitState === expected[root.getAttribute('data-daisy-kit-module')]);
        });
    } catch (error) {
        throw new Error(`Fresh host modules did not reach their expected terminal states: ${JSON.stringify(await fixtureModuleStates(page))}; responses: ${JSON.stringify(responses)}; console: ${JSON.stringify(consoleErrors)}`, { cause: error });
    }

    const viewer = page.locator('[data-daisy-kit-module="forms-viewer"]').first();
    const title = viewer.locator('input[name="title"]');

    if (await title.inputValue() !== 'HTTP fixture') {
        throw new Error('Forms Viewer did not expose its configured value in the fresh Vite host.');
    }

    await title.fill('');
    if (await viewer.locator('form').evaluate((form) => form.checkValidity())) {
        throw new Error('Forms Viewer did not retain required-field validation in the fresh Vite host.');
    }
    await title.fill('Verified host value');
    if (!(await viewer.locator('form').evaluate((form) => form.checkValidity()))) {
        throw new Error('Forms Viewer did not restore validity after a user value was entered.');
    }

    const secondaryViewer = page.locator('[data-daisy-kit-module="forms-viewer"]').nth(1);
    if (await secondaryViewer.locator('textarea[name="summary"]').inputValue() !== 'A distinct second instance') {
        throw new Error('A second Forms Viewer instance did not mount independently.');
    }

    const builder = page.locator('[data-daisy-kit-module="forms-builder"]');
    if (!(await builder.locator('[data-daisy-kit-forms-builder-unavailable]').isVisible())) {
        throw new Error('Forms Builder did not clearly report that its optional Livewire authoring runtime is unavailable.');
    }

    const table = page.locator('[data-daisy-kit-module="table"]');
    await table.getByRole('button', { name: 'Name', exact: true }).click();
    await table.getByRole('button', { name: 'Name', exact: true }).click();
    const tableHeaders = await table.locator('thead tr').first().locator('th').allTextContents();
    const sortedRows = await table.locator('tbody tr').evaluateAll((rows) => rows.map((row) => [...row.querySelectorAll('td')].map((cell) => cell.textContent?.trim() ?? '')));
    const nameColumn = tableHeaders.indexOf('Name');
    const sortedNames = sortedRows.map((row) => row[nameColumn]);
    if (nameColumn === -1 || JSON.stringify(sortedNames) !== JSON.stringify(['Bert', 'Ada'])) {
        throw new Error(`Table descending sort did not reorder the configured Name column; headers were ${JSON.stringify(tableHeaders)} and rows were ${JSON.stringify(sortedRows)}.`);
    }
    await table.locator('[data-daisy-kit-table-column-filter="status"]').selectOption('active');
    if (await table.locator('tbody tr').count() !== 1 || !(await table.locator('tbody tr').first().innerText()).includes('Ada')) {
        throw new Error('Table typed filtering did not select the expected host-rendered row.');
    }
    await table.getByRole('checkbox', { name: 'Select every row on this page' }).check();
    if (!(await table.locator('[data-daisy-kit-table-row-select="ada"]').isChecked())) {
        throw new Error('Table selection did not persist through the filtered host view.');
    }

    const tree = page.locator('[data-daisy-kit-module="tree"]');
    const projects = tree.locator('[data-daisy-kit-tree-node="projects"]');
    await projects.focus();
    await page.keyboard.press('ArrowRight');
    await page.keyboard.press(' ');
    if (await tree.locator('[data-daisy-kit-tree-value]').inputValue() !== '["daisy-kit"]') {
        throw new Error('Tree keyboard selection did not synchronize its hidden host form value.');
    }

    const preview = page.locator('[data-daisy-kit-module="file-preview"]').first();
    await preview.locator('[data-daisy-kit-file-preview-open-preview]').click();
    await preview.locator('[data-daisy-kit-file-preview-modal][open]').waitFor({ state: 'visible' });
    await preview.frameLocator('[data-daisy-kit-file-preview-frame]').locator('pre').waitFor({ state: 'visible' });
    if (!(await preview.locator('[data-daisy-kit-file-preview-notice]').isVisible())) {
        throw new Error('File Preview did not expose its configured notice in the isolated host frame.');
    }
    const actionOnlyPreview = page.locator('[data-daisy-kit-module="file-preview"]').nth(1);
    if (await actionOnlyPreview.getAttribute('data-daisy-kit-layout') !== 'action-only') {
        throw new Error('File Preview did not retain its action-only layout contract.');
    }
    await preview.locator('[data-daisy-kit-file-preview-modal]').getByRole('button', { name: 'Close' }).click();
    const modalState = await preview.locator('[data-daisy-kit-file-preview-modal]').evaluate((modal) => ({
        open: modal.open,
        previewOpen: modal.closest('[data-daisy-kit-module]')?.dataset.daisyKitPreviewOpen,
    }));
    if (modalState.open || modalState.previewOpen !== 'false') {
        throw new Error(`File Preview modal close control did not release the modal; state was ${JSON.stringify(modalState)}.`);
    }
    await actionOnlyPreview.locator('[data-daisy-kit-file-preview-open-preview]').click();
    const actionOnlyModal = actionOnlyPreview.locator('[data-daisy-kit-file-preview-modal]');
    await actionOnlyModal.waitFor({ state: 'visible' });
    await actionOnlyModal.getByRole('button', { name: 'Close' }).click();
    await actionOnlyModal.waitFor({ state: 'hidden' });

    const frame = preview.locator('[data-daisy-kit-file-preview-frame]');
    const frameSource = await frame.getAttribute('srcdoc');

    if (!frameSource?.includes('/assets/') || frameSource.includes('/file-preview-frame.html')) {
        throw new Error('File Preview did not use Vite-emitted frame chunks from the host build.');
    }

    await page.addScriptTag({ url: new URL('/axe.min.js', url).href });
    const violations = await page.evaluate(async () => {
        const result = await window.axe.run();

        return result.violations
            .filter((violation) => ['critical', 'serious'].includes(violation.impact))
            .map((violation) => `${violation.id}: ${violation.help}`);
    });

    if (violations.length > 0) {
        throw new Error(`Host fixture has serious or critical accessibility violations:\n${violations.join('\n')}`);
    }

    const source = page.locator('[data-daisy-kit-blueprint-node-control][data-node-id="source"]');
    await source.focus();
    await page.keyboard.press('ArrowRight');
    await page.keyboard.press('Enter');

    const selected = await page.locator('[data-daisy-kit-blueprint-node-control][data-node-id="destination"]').getAttribute('aria-pressed');

    if (selected !== 'true') {
        throw new Error('Blueprint keyboard selection did not select the next semantic node control.');
    }

    const blueprint = page.locator('[data-daisy-kit-module="blueprint"]');
    await blueprint.locator('[data-daisy-kit-blueprint-structure="add-node"]').click();
    if (await blueprint.locator('[data-daisy-kit-blueprint-node-control]').count() !== 3) {
        throw new Error('Value-backed Blueprint did not retain the added node after its structural remount.');
    }
    await blueprint.locator('[data-daisy-kit-blueprint-history="undo"]').click();
    if (await blueprint.locator('[data-daisy-kit-blueprint-node-control]').count() !== 2) {
        throw new Error('Value-backed Blueprint undo did not restore the pre-add graph.');
    }
    await blueprint.locator('[data-daisy-kit-blueprint-history="redo"]').click();
    await blueprint.locator('[data-daisy-kit-blueprint-node-control][data-node-id="source"]').click();
    await blueprint.locator('[data-daisy-kit-blueprint-transition-target]').selectOption('destination');
    await blueprint.locator('[data-daisy-kit-blueprint-structure="add-transition"]').click();
    const blueprintGraph = JSON.parse(await blueprint.locator('[data-daisy-kit-blueprint-value]').inputValue());
    if (!blueprintGraph.nodes.some((node) => node.id === 'node-3') || !blueprintGraph.edges.some((edge) => edge.source === 'source' && edge.target === 'destination')) {
        throw new Error(`Value-backed Blueprint did not synchronize its structural graph: ${JSON.stringify(blueprintGraph)}.`);
    }

    const map = page.locator('[data-daisy-kit-module="map"]');
    await map.locator('.leaflet-marker-icon[title="City hall"]').waitFor({ state: 'visible' });
    await map.locator('[data-daisy-kit-map-layer="fixture-layer"]').uncheck();
    if (await map.locator('[data-daisy-kit-map-layer="fixture-layer"]').isChecked()) {
        throw new Error('Map layer controls did not toggle the configured GeoJSON overlay.');
    }
    await map.locator('[data-daisy-kit-map-mode="linestring"]').click();
    if (await map.locator('[data-daisy-kit-map-mode="linestring"]').getAttribute('aria-pressed') !== 'true') {
        throw new Error('Map drawing controls did not activate the requested drawing mode.');
    }
    await map.locator('[data-daisy-kit-map-mode="point"]').click();
    if (await map.locator('[data-daisy-kit-map-mode="point"]').getAttribute('aria-pressed') !== 'true') {
        throw new Error('Map point drawing mode did not become active in the host.');
    }
    await map.locator('[data-daisy-kit-map-mode="edit"]').click();
    if (await map.locator('[data-daisy-kit-map-mode="edit"]').getAttribute('aria-pressed') !== 'true') {
        throw new Error('Map edit mode did not become active in the host.');
    }
    await map.evaluate((root) => root.addEventListener('daisy-kit:map:geolocate', () => {
        root.dataset.fixtureGeolocated = 'true';
    }, { once: true }));
    await map.locator('[data-daisy-kit-map-geolocate]').click();
    await page.waitForFunction(() => document.querySelector('[data-daisy-kit-module="map"]')?.dataset.fixtureGeolocated === 'true');
    await map.locator('[data-daisy-kit-map-mode="spatial-select"]').click();
    const mapCanvas = map.locator('[data-daisy-kit-map-canvas]');
    const mapBounds = await mapCanvas.boundingBox();
    if (!mapBounds) throw new Error('Map did not expose a measurable canvas for spatial selection.');
    await mapCanvas.click({ position: { x: mapBounds.width / 2, y: mapBounds.height / 2 } });
    await page.waitForFunction(() => document.querySelector('[data-daisy-kit-module="map"]')?.dataset.daisyKitSpatialSelection === 'fixture-district');

    if (responses.length > 0) {
        throw new Error(`The served host requested missing assets:\n${responses.join('\n')}`);
    }

    if (consoleErrors.length > 0) {
        throw new Error(`The served HTTP host logged browser errors:\n${consoleErrors.join('\n')}`);
    }
} finally {
    if (browser) await browser.close();
    if (server) await closeServer(server);
    rmSync(hostRoot, { force: true, recursive: true });
}

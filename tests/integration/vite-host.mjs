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

let server;
let browser;

try {
    cpSync(fixtureRoot, hostRoot, { recursive: true });

    const composerJson = readFileSync(resolve(hostRoot, 'composer.json'), 'utf8')
        .replace('__PACKAGE_ROOT__', repositoryRoot);
    writeFileSync(resolve(hostRoot, 'composer.json'), composerJson);

    run('composer', ['install', '--no-interaction', '--no-scripts', '--prefer-dist']);
    run('npm', ['ci', '--no-audit', '--ignore-scripts']);
    run(process.execPath, [resolve(hostRoot, 'node_modules/vite/bin/vite.js'), 'build', '--config', 'vite.config.js']);

    const manifest = JSON.parse(readFileSync(resolve(hostRoot, 'build/.vite/manifest.json'), 'utf8'));

    const distRoot = resolve(hostRoot, 'vendor/art35rennes/laravel-daisy-kit/dist');
    const everyEntryExists = entryStems.every((entry) => existsSync(resolve(distRoot, `${entry}.js`)) && existsSync(resolve(distRoot, `${entry}.css`)));

    if (!everyEntryExists || Object.keys(manifest).length === 0) {
        throw new Error('The fresh Composer host did not resolve and build the Daisy Kit Vite entries.');
    }

    const buildRoot = resolve(hostRoot, 'build');
    const axeSource = readFileSync(resolve(repositoryRoot, 'vendor/pestphp/pest-plugin-browser/resources/js/axe.min.js'), 'utf8');
    const responses = [];
    server = await startHost(buildRoot);
    const address = server.address();
    const url = `http://127.0.0.1:${address.port}`;
    browser = await chromium.launch({ headless: true });
    const page = await browser.newPage();
    page.on('response', (response) => {
        if (response.status() >= 400) responses.push(`${response.status()} ${response.url()}`);
    });

    await page.goto(url, { waitUntil: 'networkidle' });
    await page.waitForFunction(() => document.querySelector('[data-daisy-kit-module="file-preview"]')?.dataset.daisyKitState === 'ready');
    await page.frameLocator('[data-daisy-kit-file-preview-frame]').locator('pre').waitFor({ state: 'visible' });

    const frame = page.locator('[data-daisy-kit-file-preview-frame]');
    const frameSource = await frame.getAttribute('srcdoc');

    if (!frameSource?.includes('/assets/') || frameSource.includes('/file-preview-frame.html')) {
        throw new Error('File Preview did not use Vite-emitted frame chunks from the host build.');
    }

    await page.addScriptTag({ content: axeSource });
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

    if (responses.length > 0) {
        throw new Error(`The served host requested missing assets:\n${responses.join('\n')}`);
    }
} finally {
    if (browser) await browser.close();
    if (server) await closeServer(server);
    rmSync(hostRoot, { force: true, recursive: true });
}

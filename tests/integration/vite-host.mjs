import { cpSync, existsSync, mkdtempSync, readFileSync, rmSync, writeFileSync } from 'node:fs';
import { tmpdir } from 'node:os';
import { resolve } from 'node:path';
import { spawnSync } from 'node:child_process';

const repositoryRoot = resolve(import.meta.dirname, '../..');
const fixtureRoot = resolve(repositoryRoot, 'tests/fixtures/vite-host');
const hostRoot = mkdtempSync(resolve(tmpdir(), 'daisy-kit-vite-host-'));
const entryStems = ['forms-viewer', 'forms-builder', 'table', 'tree', 'blueprint', 'file-preview', 'map'];

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
} finally {
    rmSync(hostRoot, { force: true, recursive: true });
}

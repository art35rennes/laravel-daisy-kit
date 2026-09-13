import assert from 'node:assert/strict';
import { mkdtempSync, rmSync, writeFileSync } from 'node:fs';
import { tmpdir } from 'node:os';
import { resolve } from 'node:path';
import { execFileSync } from 'node:child_process';
import { test } from 'node:test';
import { preparePackageSource } from './package-source.mjs';

function repositoryFixture() {
    const root = mkdtempSync(resolve(tmpdir(), 'daisy-kit-source-test-'));
    function git(...args) {
        return execFileSync('git', args, { cwd: root, encoding: 'utf8', stdio: ['ignore', 'pipe', 'pipe'] }).trim();
    }
    git('init', '-b', 'main');
    git('config', 'user.name', 'Fixture');
    git('config', 'user.email', 'fixture@example.test');
    writeFileSync(resolve(root, 'composer.json'), '{"name":"fixture/package"}');
    git('add', 'composer.json');
    git('commit', '-m', 'Fixture');
    return { root, git, commit: git('rev-parse', 'HEAD') };
}

test('a detached CI checkout installs its exact committed source without changing the checkout', () => {
    const fixture = repositoryFixture();
    const staging = mkdtempSync(resolve(tmpdir(), 'daisy-kit-staging-test-'));
    try {
        fixture.git('checkout', '--detach');
        const source = preparePackageSource(fixture.root, staging, {});
        assert.equal(source.version, `dev-fixture#${fixture.commit}`);
        assert.equal(source.commit, fixture.commit);
        assert.equal(fixture.git('branch', '--show-current'), '');
        assert.equal(execFileSync('git', ['rev-parse', 'HEAD'], { cwd: source.url, encoding: 'utf8' }).trim(), fixture.commit);
    } finally {
        rmSync(fixture.root, { recursive: true, force: true });
        rmSync(staging, { recursive: true, force: true });
    }
});

test('an explicit immutable tag uses its VCS URL and expected commit', () => {
    const fixture = repositoryFixture();
    try {
        const source = preparePackageSource(fixture.root, '/unused', {
            DAISY_KIT_PACKAGE_REPOSITORY: 'https://github.com/art35rennes/laravel-daisy-kit',
            DAISY_KIT_PACKAGE_VERSION: 'v6.0.0',
        });
        assert.deepEqual(source, {
            url: 'https://github.com/art35rennes/laravel-daisy-kit',
            version: 'v6.0.0',
            commit: fixture.commit,
        });
        assert.throws(() => preparePackageSource(fixture.root, '/unused', {
            DAISY_KIT_PACKAGE_REPOSITORY: 'https://example.test/package',
        }), /together/);
    } finally {
        rmSync(fixture.root, { recursive: true, force: true });
    }
});

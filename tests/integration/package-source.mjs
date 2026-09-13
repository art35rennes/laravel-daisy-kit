import { execFileSync } from 'node:child_process';
import { resolve } from 'node:path';

export function preparePackageSource(repositoryRoot, stagingRoot, environment = process.env) {
    function git(args, cwd = repositoryRoot) {
        return execFileSync('git', args, { cwd, encoding: 'utf8', stdio: ['ignore', 'pipe', 'pipe'] }).trim();
    }

    const commit = git(['rev-parse', 'HEAD']);
    const url = environment.DAISY_KIT_PACKAGE_REPOSITORY;
    const version = environment.DAISY_KIT_PACKAGE_VERSION;

    if (Boolean(url) !== Boolean(version)) {
        throw new Error('Set DAISY_KIT_PACKAGE_REPOSITORY and DAISY_KIT_PACKAGE_VERSION together.');
    }

    if (url && version) {
        return { url, version, commit };
    }

    // Give detached PR/tag checkouts a Composer branch without modifying the source checkout.
    const sourceRoot = resolve(stagingRoot, 'package-source');
    git(['clone', '--no-hardlinks', '--no-checkout', repositoryRoot, sourceRoot]);
    git(['checkout', '-B', 'fixture', commit], sourceRoot);

    return { url: sourceRoot, version: `dev-fixture#${commit}`, commit };
}

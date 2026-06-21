import { readFileSync } from 'node:fs';
import { resolve } from 'node:path';
import { describe, expect, it } from 'vitest';

describe('app entry', () => {
  it('loads copyable directly so async table cells are initialized', () => {
    const appEntry = readFileSync(resolve('resources/js/app.js'), 'utf8');

    expect(appEntry).toContain("await import('./modules/copyable')");
    expect(appEntry).not.toContain("dynamicImportIf('.copyable'");
  });
});

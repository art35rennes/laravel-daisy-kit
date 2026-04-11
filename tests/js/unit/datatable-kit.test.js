import { describe, expect, it } from 'vitest';
import {
  DEFAULT_LAYOUT,
  buildDataTableOptions,
  deepMerge,
  parseOptions,
} from '../../../resources/js/datatable-kit.js';

describe('datatable-kit helpers', () => {
  it('parses options from a dataset payload', () => {
    const options = parseOptions({
      dataset: {
        options: JSON.stringify({ serverSide: true, responsive: true }),
      },
    });

    expect(options).toEqual({
      serverSide: true,
      responsive: true,
    });
  });

  it('returns an empty object for invalid JSON', () => {
    expect(parseOptions('{')).toEqual({});
  });

  it('merges nested layout objects without losing defaults', () => {
    const merged = deepMerge(DEFAULT_LAYOUT, {
      topEnd: {
        search: {
          placeholder: 'Search',
        },
      },
    });

    expect(merged).toEqual({
      topStart: 'pageLength',
      topEnd: {
        search: {
          placeholder: 'Search',
        },
      },
      bottomStart: 'info',
      bottomEnd: 'paging',
    });
  });

  it('builds default datatable options with responsive support', () => {
    const options = buildDataTableOptions({
      serverSide: true,
      responsive: true,
      pageLength: 25,
      language: {
        search: 'Search',
      },
    });

    expect(options).toEqual({
      layout: DEFAULT_LAYOUT,
      paging: true,
      pageLength: 25,
      lengthChange: true,
      searching: true,
      ordering: true,
      processing: true,
      scrollX: false,
      responsive: true,
      serverSide: true,
      language: {
        search: 'Search',
      },
    });
  });

  it('keeps caller-provided layout overrides while preserving the package structure', () => {
    const options = buildDataTableOptions({
      layout: {
        topEnd: null,
        bottomStart: 'paging',
      },
    });

    expect(options.layout).toEqual({
      topStart: 'pageLength',
      topEnd: null,
      bottomStart: 'paging',
      bottomEnd: 'paging',
    });
  });
});

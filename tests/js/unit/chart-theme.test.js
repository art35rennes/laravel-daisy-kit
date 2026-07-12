/** @vitest-environment jsdom */

import { describe, expect, it } from 'vitest';
import { applyAlpha, buildChartTheme, isDarkColor, resolveSingleColor } from '../../../resources/js/chart/theme.js';

describe('chart theme colors', () => {
    it('detects dark OKLCH colors and applies alpha without losing the color', () => {
        expect(isDarkColor('oklch(0.2326 0.014 253.1)')).toBe(true);
        expect(isDarkColor('oklch(0.97807 0.029 256.847)')).toBe(false);
        expect(applyAlpha('oklch(0.2326 0.014 253.1)', 0.5)).toMatch(/^rgba\(\d+, \d+, \d+, 0.5\)$/);
    });

    it('resolves DaisyUI semantic custom properties from the chart context', () => {
        const root = document.createElement('div');
        root.style.setProperty('--color-primary', 'oklch(0.62 0.2 250)');
        root.style.setProperty('--color-base-100', 'oklch(0.23 0.01 250)');
        root.style.setProperty('--color-base-200', 'oklch(0.28 0.01 250)');
        root.style.setProperty('--color-base-300', 'oklch(0.36 0.01 250)');
        root.style.setProperty('--color-base-content', 'oklch(0.95 0.02 250)');
        document.body.appendChild(root);

        const theme = buildChartTheme({ palette: ['primary'] }, root);

        expect(resolveSingleColor('primary', root)).toBe('oklch(0.62 0.2 250)');
        expect(theme.dark).toBe(true);
        expect(theme.palette).toEqual(['oklch(0.62 0.2 250)']);
        expect(theme.textMutedColor).toContain('rgba(');
        expect(theme.gridColor).toContain('rgba(');

        root.remove();
    });

    it('resolves explicit CSS variable references before returning chart colors', () => {
        const root = document.createElement('div');
        root.style.setProperty('--dashboard-chart', '#123456');
        document.body.appendChild(root);

        expect(resolveSingleColor('var(--dashboard-chart)', root)).toBe('#123456');

        root.remove();
    });
});

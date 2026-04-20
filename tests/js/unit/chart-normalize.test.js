import { describe, expect, it } from 'vitest';
import { normalizeChartConfig } from '../../../resources/js/chart/normalize.js';
import { buildChartOption } from '../../../resources/js/chart/presets.js';

const theme = {
    dark: false,
    palette: ['#2563eb', '#db2777', '#14b8a6'],
    textColor: '#111827',
    textMutedColor: '#6b7280',
    axisColor: '#d1d5db',
    gridColor: '#e5e7eb',
    tooltipBackground: 'rgba(255,255,255,0.95)',
};

describe('chart config normalization', () => {
    it('maps categories and cartesian series into a standard config', () => {
        const config = normalizeChartConfig({
            preset: 'line',
            categories: ['Jan', 'Feb'],
            series: [{ name: 'Revenue', data: [10, 12] }],
        });

        expect(config.isCartesian).toBe(true);
        expect(config.categories).toEqual(['Jan', 'Feb']);
        expect(config.series[0].name).toBe('Revenue');
        expect(config.series[0].data).toEqual([10, 12]);
    });

    it('uses sparkline defaults without exposing legend', () => {
        const config = normalizeChartConfig({
            preset: 'sparkline',
            series: [{ data: [1, 2, 3] }],
        });

        expect(config.isSparkline).toBe(true);
        expect(config.legend).toBe(false);
    });

    it('marks stacked presets as stacked', () => {
        expect(normalizeChartConfig({ preset: 'stacked-bar', series: [{ data: [1] }] }).isStacked).toBe(true);
        expect(normalizeChartConfig({ preset: 'stacked-area', series: [{ data: [1] }] }).isStacked).toBe(true);
    });

    it('normalizes pie and donut data with categories', () => {
        const pie = normalizeChartConfig({
            preset: 'pie',
            categories: ['A', 'B'],
            series: [{ name: 'Share', data: [40, 60] }],
        });
        const donut = normalizeChartConfig({
            preset: 'donut',
            categories: ['A', 'B'],
            series: [{ name: 'Share', data: [40, 60] }],
        });

        expect(pie.series[0].data).toEqual([
            { name: 'A', value: 40 },
            { name: 'B', value: 60 },
        ]);
        expect(donut.series[0].data[1].name).toBe('B');
    });
});

describe('chart presets', () => {
    it('builds a donut radius distinct from pie', () => {
        const pieOption = buildChartOption(normalizeChartConfig({
            preset: 'pie',
            categories: ['A', 'B'],
            series: [{ data: [30, 70] }],
        }), theme);

        const donutOption = buildChartOption(normalizeChartConfig({
            preset: 'donut',
            categories: ['A', 'B'],
            series: [{ data: [30, 70] }],
        }), theme);

        expect(pieOption.series[0].radius).toBe('72%');
        expect(donutOption.series[0].radius).toEqual(['48%', '72%']);
    });
});

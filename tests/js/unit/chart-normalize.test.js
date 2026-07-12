import { describe, expect, it } from 'vitest';
import { buildActivationDetail, buildCircularFocusState, buildDrilldownUrl } from '../../../resources/js/chart/core.js';
import { normalizeChartConfig } from '../../../resources/js/chart/normalize.js';
import { buildChartOption, mergeOptions } from '../../../resources/js/chart/presets.js';

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

    it('keeps sparkline line color stable during hover emphasis', () => {
        const option = buildChartOption(normalizeChartConfig({
            preset: 'sparkline',
            series: [{ name: 'Trend', data: [1, 2, 3] }],
        }), theme);

        expect(option.series[0].symbol).toBe('circle');
        expect(option.series[0].showSymbol).toBe(false);
        expect(option.series[0].symbolSize).toBe(10);
        expect(option.series[0].lineStyle.color).toBe(theme.palette[0]);
        expect(option.series[0].emphasis.lineStyle.color).toBe(theme.palette[0]);
        expect(option.series[0].emphasis.disabled).toBe(true);
        expect(option.series[0].emphasis.itemStyle.borderWidth).toBe(0);
        expect(option.series[0].emphasis.symbolSize).toBe(option.series[0].symbolSize);
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

    it('preserves enriched points and drilldown metadata', () => {
        const config = normalizeChartConfig({
            preset: 'bar',
            categories: ['Open'],
            drilldown: {
                url: '/interventions',
                params: { section: 'terrain' },
            },
            series: [{
                name: 'Status',
                data: [{
                    value: 38,
                    color: '#16a34a',
                    drilldown: { status: 'a-realiser' },
                }],
            }],
        });

        expect(config.drilldown.url).toBe('/interventions');
        expect(config.drilldown.params).toEqual({ section: 'terrain' });
        expect(config.series[0].data[0]).toMatchObject({
            name: 'Open',
            value: 38,
            drilldown: { status: 'a-realiser' },
            itemStyle: { color: '#16a34a' },
        });
    });

    it('normalizes chart and point actions without dropping integration metadata', () => {
        const config = normalizeChartConfig({
            preset: 'bar',
            action: {
                type: 'url',
                url: '/interventions',
                params: { section: 'terrain' },
                target: '#ignored-for-url',
            },
            series: [{
                name: 'Status',
                data: [{
                    name: 'Open',
                    value: 38,
                    meta: { statusId: 7 },
                    action: { type: 'event', intent: 'detail' },
                }],
            }],
        });

        expect(config.action).toEqual({
            type: 'url',
            url: '/interventions',
            params: { section: 'terrain' },
            intent: 'navigate',
            target: '#ignored-for-url',
        });
        expect(config.series[0].data[0]).toMatchObject({
            meta: { statusId: 7 },
            action: { type: 'event', intent: 'detail', params: {} },
        });
    });
});

describe('chart presets', () => {
    it('builds a deterministic focus state for circular segments', () => {
        expect(buildCircularFocusState(4, 2)).toEqual(['muted', 'muted', 'focus', 'muted']);
        expect(buildCircularFocusState(2, null)).toEqual(['normal', 'normal']);
        expect(buildCircularFocusState(2, 8)).toEqual(['normal', 'normal']);
    });

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

    it('highlights circular segments without changing selection state', () => {
        const option = buildChartOption(normalizeChartConfig({
            preset: 'donut',
            categories: ['A', 'B'],
            series: [{ name: 'Share', data: [30, 70] }],
        }), theme);

        expect(option.tooltip.trigger).toBe('item');
        expect(option.tooltip.enterable).toBe(false);
        expect(option.tooltip.hideDelay).toBe(220);
        expect(option.tooltip.renderMode).toBe('html');
        expect(option.tooltip.appendTo).toBe('body');
        expect(option.tooltip.confine).toBe(false);
        expect(option.tooltip.extraCssText).toContain('z-index:var(--daisy-z-tooltip-content,81)');
        expect(option.tooltip.extraCssText).toContain('pointer-events:none');
        expect(option.tooltip.extraCssText).toContain('box-shadow:none');
        expect(option.tooltip.displayTransition).toBe(false);
        expect(option.series[0].animation).toBe(false);
        expect(option.series[0].selectedMode).toBe(false);
        expect(option.series[0].emphasis.disabled).toBe(true);
        expect(option.series[0].emphasis.focus).toBe('none');
        expect(option.series[0].emphasis.scale).toBe(false);
        expect(option.series[0].emphasis.scaleSize).toBe(0);
        expect(option.stateAnimation.duration).toBe(0);
        expect(option.series[0].emphasis.itemStyle.shadowBlur).toBe(0);
        expect(option.series[0].emphasis.itemStyle.opacity).toBe(1);
        expect(option.series[0].blur.itemStyle.opacity).toBe(1);
        expect(option.series[0].select.disabled).toBe(true);
    });

    it('renders donut centre labels and visible bar values when requested', () => {
        const donut = buildChartOption(normalizeChartConfig({
            preset: 'donut',
            centerValue: '87%',
            centerLabel: 'Taux global',
            series: [{ data: [87, 13] }],
        }), theme);
        const bar = buildChartOption(normalizeChartConfig({
            preset: 'bar',
            showValues: true,
            series: [{ data: [{ name: 'Rennes', value: 12, meta: { cityId: 1 } }] }],
        }), theme);

        expect(donut.graphic).toBeUndefined();
        expect(donut.series[0].data[0].label).toMatchObject({
            show: true,
            position: 'center',
            align: 'center',
            verticalAlign: 'middle',
            formatter: '{value|87%}\n{label|Taux global}',
        });
        expect(donut.series[0].data[0].label.rich.value.color).toBe(theme.textColor);
        expect(donut.series[0].data[0].label.rich.label.color).toBe(theme.textMutedColor);
        expect(bar.series[0].label.show).toBe(true);
        expect(bar.series[0].label.formatter({ value: { value: 12 } })).toBe('12');
    });

    it('does not attach cartesian markers to circular series', () => {
        const option = buildChartOption(normalizeChartConfig({
            preset: 'donut',
            categories: ['A', 'B'],
            markers: [{ type: 'line', value: 85, name: 'Target' }],
            series: [{ name: 'Compliance', data: [92, 88] }],
        }), theme);

        expect(option.series[0].markLine).toBeUndefined();
        expect(option.series[0].markPoint).toBeUndefined();
    });

    it('uses stable hover defaults for cartesian charts', () => {
        const option = buildChartOption(normalizeChartConfig({
            preset: 'bar',
            categories: ['A', 'B'],
            series: [{ name: 'Load', data: [10, 12] }],
        }), theme);

        expect(option.tooltip.transitionDuration).toBe(0);
        expect(option.tooltip.renderMode).toBe('html');
        expect(option.tooltip.appendTo).toBe('body');
        expect(option.tooltip.confine).toBe(false);
        expect(option.tooltip.enterable).toBe(false);
        expect(option.tooltip.shadowBlur).toBe(0);
        expect(option.tooltip.shadowColor).toBe('transparent');
        expect(option.tooltip.borderColor).toBe('rgba(15,23,42,0.12)');
        expect(option.tooltip.axisPointer.animation).toBe(false);
        expect(option.tooltip.axisPointer.lineStyle.color).toBe('rgba(15,23,42,0.28)');
        expect(option.tooltip.axisPointer.lineStyle.type).toBe('dashed');
        expect(option.series[0].emphasis.focus).toBe('self');
        expect(option.series[0].select.disabled).toBe(true);
        expect(option.series[0].emphasis.itemStyle.opacity).toBe(1);

        const tooltip = option.tooltip.formatter([{ axisValueLabel: 'A', seriesName: 'Load', value: 10 }]);
        expect(tooltip).toContain('A');
        expect(tooltip).toContain('Load: 10');
        expect(tooltip).not.toContain('undefined');
    });

    it('uses a stable tooltip border without compositor-heavy shadows in dark themes', () => {
        const option = buildChartOption(normalizeChartConfig({
            preset: 'line',
            categories: ['A', 'B'],
            series: [{ name: 'Load', data: [10, 12] }],
        }), { ...theme, dark: true });

        expect(option.tooltip.shadowBlur).toBe(0);
        expect(option.tooltip.shadowColor).toBe('transparent');
        expect(option.tooltip.borderColor).toBe('rgba(255,255,255,0.18)');
        expect(option.tooltip.axisPointer.lineStyle.color).toBe('rgba(255,255,255,0.62)');
        expect(option.tooltip.axisPointer.lineStyle.width).toBe(1.5);
        expect(option.series[0].emphasis.disabled).toBe(true);
        expect(option.series[0].emphasis.focus).toBe('none');
        expect(option.series[0].emphasis.symbolSize).toBe(option.series[0].symbolSize);
    });

    it('escapes tooltip labels and values before rendering portaled HTML', () => {
        const option = buildChartOption(normalizeChartConfig({
            preset: 'bar',
            categories: ['<script>alert(1)</script>'],
            series: [{ name: '<img src=x onerror=alert(1)>', data: ['<b>12</b>'] }],
        }), theme);

        const tooltip = option.tooltip.formatter([{
            axisValueLabel: '<script>alert(1)</script>',
            seriesName: '<img src=x onerror=alert(1)>',
            value: '<b>12</b>',
        }]);

        expect(tooltip).toContain('&lt;script&gt;alert(1)&lt;/script&gt;');
        expect(tooltip).toContain('&lt;img src=x onerror=alert(1)&gt;');
        expect(tooltip).toContain('&lt;b&gt;12&lt;/b&gt;');
        expect(tooltip).not.toContain('<script>');
        expect(tooltip).not.toContain('<img');
        expect(option.tooltip.renderMode).toBe('html');
    });

    it('builds horizontal bar axes', () => {
        const option = buildChartOption(normalizeChartConfig({
            preset: 'bar',
            orientation: 'horizontal',
            categories: ['Rennes', 'Vannes'],
            series: [{ name: 'Interventions', data: [12, 8] }],
        }), theme);

        expect(option.xAxis.type).toBe('value');
        expect(option.yAxis.type).toBe('category');
        expect(option.yAxis.data).toEqual(['Rennes', 'Vannes']);
    });

    it('adds aria, zoom and markers when requested', () => {
        const option = buildChartOption(normalizeChartConfig({
            preset: 'line',
            categories: ['J-1', 'Today'],
            zoom: true,
            zoomMode: 'slider',
            markers: [
                { type: 'line', value: 24, name: 'Limit' },
                { type: 'point', coord: ['Today', 28], name: 'Peak' },
            ],
            series: [{ name: 'Queue', data: [20, 28] }],
        }), theme);

        expect(option.aria.enabled).toBe(true);
        expect(option.dataZoom[0].type).toBe('slider');
        expect(option.series[0].markLine.data[0]).toMatchObject({ name: 'Limit', yAxis: 24 });
        expect(option.series[0].markPoint.data[0]).toMatchObject({ name: 'Peak', coord: ['Today', 28] });
        expect(option.series[0].markPoint.emphasis.disabled).toBe(true);
        expect(option.series[0].markPoint.select.disabled).toBe(true);
    });

    it('merges array options by object index without dropping base entries', () => {
        const option = mergeOptions({
            series: [
                { name: 'A', data: [1], label: { show: true } },
                { name: 'B', data: [2], label: { show: true } },
            ],
        }, {
            series: [
                { label: { show: false } },
            ],
        });

        expect(option.series[0]).toMatchObject({ name: 'A', data: [1], label: { show: false } });
        expect(option.series[1]).toMatchObject({ name: 'B', data: [2], label: { show: true } });
    });

    it('replaces non-object option arrays instead of preserving stale values', () => {
        const option = mergeOptions({
            color: ['red', 'blue'],
        }, {
            color: ['green'],
        });

        expect(option.color).toEqual(['green']);
    });
});

describe('chart drilldown urls', () => {
    it('merges global and point params into a query url', () => {
        const url = buildDrilldownUrl({
            drilldown: {
                url: '/interventions?period=current',
                params: { section: 'terrain', chart: 'status' },
            },
        }, {
            data: {
                value: 38,
                drilldown: { status: 'a-realiser' },
            },
        });

        expect(url).toBe('http://localhost/interventions?period=current&section=terrain&chart=status&status=a-realiser');
    });

    it('lets point actions override chart urls and parameters', () => {
        const url = buildDrilldownUrl({
            action: {
                type: 'url',
                url: '/interventions?period=current',
                params: { section: 'terrain', status: 'all' },
            },
        }, {
            data: {
                value: 38,
                action: {
                    type: 'url',
                    url: '/interventions/detail',
                    params: { status: 'open' },
                },
            },
        });

        expect(url).toBe('http://localhost/interventions/detail?section=terrain&status=open');
    });

    it('builds a stable activation payload for host integrations', () => {
        const detail = buildActivationDetail({
            preset: 'donut',
            action: { type: 'event', intent: 'detail', params: {} },
        }, {
            seriesName: 'Status',
            seriesIndex: 0,
            dataIndex: 2,
            name: 'À corriger',
            value: 7,
            color: '#ef4444',
            data: {
                value: 7,
                meta: { statusId: 3 },
                action: { type: 'event', intent: 'modal', target: '#detail-modal', params: {} },
            },
        }, null, 'chart-status');

        expect(detail).toEqual({
            chartId: 'chart-status',
            preset: 'donut',
            intent: 'modal',
            target: '#detail-modal',
            seriesName: 'Status',
            name: 'À corriger',
            value: 7,
            seriesIndex: 0,
            dataIndex: 2,
            color: '#ef4444',
            meta: { statusId: 3 },
            resolvedUrl: null,
        });
    });

    it('does not build a url without point drilldown', () => {
        const url = buildDrilldownUrl({
            drilldown: {
                url: '/interventions',
                params: { section: 'terrain' },
            },
        }, {
            data: 38,
        });

        expect(url).toBeNull();
    });

    it('does not build fake hash or cross-origin urls', () => {
        expect(buildDrilldownUrl({
            drilldown: { url: '#', params: {} },
        }, {
            data: { drilldown: { status: 'open' } },
        })).toBeNull();

        globalThis.window = {
            location: {
                href: 'https://example.test/dashboard',
                origin: 'https://example.test',
            },
        };

        expect(buildDrilldownUrl({
            drilldown: { url: 'https://evil.test/path', params: {} },
        }, {
            data: { drilldown: { status: 'open' } },
        })).toBeNull();

        delete globalThis.window;
    });
});

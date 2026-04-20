import { createAxisLabelFormatter, createPieLabelFormatter, createTooltipFormatter } from './formatters';

function isPlainObject(value) {
    return value && typeof value === 'object' && !Array.isArray(value);
}

export function mergeOptions(base, extra) {
    if (!isPlainObject(base)) {
        return isPlainObject(extra) ? { ...extra } : extra;
    }

    if (!isPlainObject(extra)) {
        return { ...base };
    }

    const output = { ...base };

    for (const [key, value] of Object.entries(extra)) {
        if (Array.isArray(value)) {
            output[key] = value.slice();
            continue;
        }

        if (isPlainObject(value) && isPlainObject(output[key])) {
            output[key] = mergeOptions(output[key], value);
            continue;
        }

        output[key] = value;
    }

    return output;
}

function baseTitle(config) {
    if (!config.title && !config.subtitle) {
        return undefined;
    }

    return {
        text: config.title || '',
        subtext: config.subtitle || '',
        left: 0,
        top: 0,
        textStyle: {
            fontSize: 14,
            fontWeight: 600,
        },
        subtextStyle: {
            fontSize: 12,
        },
    };
}

function createLegend(config, theme) {
    if (!config.legend) {
        return undefined;
    }

    return {
        top: config.title || config.subtitle ? 28 : 0,
        left: 'center',
        textStyle: {
            color: theme.textColor,
        },
    };
}

function createToolbox(config) {
    if (!config.toolbar) {
        return undefined;
    }

    return {
        right: 0,
        top: 0,
        feature: {
            saveAsImage: {},
            restore: {},
        },
    };
}

function createCartesianSeries(config, theme) {
    return config.series.map((entry, index) => {
        const color = entry.color || theme.palette[index % theme.palette.length];
        const lineSeries = {
            name: entry.name,
            type: config.preset === 'bar' || config.preset === 'stacked-bar' ? 'bar' : 'line',
            data: entry.data,
            yAxisIndex: entry.axis === 'right' ? 1 : 0,
            itemStyle: { color },
            emphasis: { focus: 'series' },
            smooth: config.preset === 'sparkline',
        };

        if (config.isStacked) {
            lineSeries.stack = entry.stack || 'total';
        }

        if (lineSeries.type === 'line') {
            lineSeries.symbol = config.isSparkline ? 'none' : 'circle';
            lineSeries.showSymbol = !config.isSparkline;
            lineSeries.lineStyle = {
                width: config.isSparkline ? 2 : 3,
            };
        }

        if (config.isArea) {
            lineSeries.areaStyle = {
                opacity: theme.dark ? 0.24 : 0.16,
            };
        }

        return lineSeries;
    });
}

function createCircularSeries(config) {
    const [first] = config.series;
    if (!first) {
        return [];
    }

    return [{
        name: first.name,
        type: 'pie',
        radius: config.preset === 'donut' ? ['48%', '72%'] : '72%',
        center: ['50%', '56%'],
        avoidLabelOverlap: true,
        label: {
            color: 'inherit',
            formatter: createPieLabelFormatter(config.valueFormat),
        },
        data: first.data,
        emphasis: {
            itemStyle: {
                shadowBlur: 18,
                shadowOffsetX: 0,
            },
        },
    }];
}

export function buildChartOption(config, theme) {
    const legend = createLegend(config, theme);
    const toolbox = createToolbox(config);
    const title = baseTitle(config);
    const tooltip = {
        trigger: config.isCircular ? 'item' : 'axis',
        backgroundColor: theme.tooltipBackground,
        borderColor: theme.axisColor,
        borderWidth: 1,
        textStyle: {
            color: theme.textColor,
        },
        formatter: createTooltipFormatter(config.tooltipFormat),
    };

    const baseOption = {
        animationDuration: 240,
        color: theme.palette,
        textStyle: {
            color: theme.textColor,
        },
        title,
        tooltip,
        legend,
        toolbox,
    };

    if (config.isCircular) {
        return mergeOptions(baseOption, {
            series: createCircularSeries(config),
        });
    }

    if (config.isSparkline) {
        return mergeOptions(baseOption, {
            grid: {
                top: title ? 28 : 6,
                right: 4,
                bottom: 4,
                left: 4,
            },
            xAxis: {
                type: 'category',
                boundaryGap: false,
                data: config.categories,
                show: false,
            },
            yAxis: {
                type: 'value',
                show: false,
            },
            tooltip: {
                ...tooltip,
                trigger: 'axis',
            },
            series: createCartesianSeries(config, theme),
        });
    }

    const usesRightAxis = config.series.some((entry) => entry.axis === 'right');

    return mergeOptions(baseOption, {
        grid: {
            top: legend ? 62 : (title ? 34 : 12),
            right: usesRightAxis ? 56 : 16,
            bottom: 16,
            left: 12,
            containLabel: true,
        },
        xAxis: {
            type: 'category',
            data: config.categories,
            boundaryGap: config.preset === 'bar' || config.preset === 'stacked-bar',
            axisLabel: {
                color: theme.textMutedColor,
            },
            axisLine: {
                lineStyle: {
                    color: theme.axisColor,
                },
            },
            axisTick: {
                lineStyle: {
                    color: theme.axisColor,
                },
            },
        },
        yAxis: [
            {
                type: 'value',
                axisLabel: {
                    color: theme.textMutedColor,
                    formatter: createAxisLabelFormatter(config.valueFormat),
                },
                splitLine: {
                    lineStyle: {
                        color: theme.gridColor,
                    },
                },
                axisLine: {
                    show: false,
                },
            },
            ...(usesRightAxis ? [{
                type: 'value',
                position: 'right',
                axisLabel: {
                    color: theme.textMutedColor,
                    formatter: createAxisLabelFormatter(config.valueFormat),
                },
                splitLine: {
                    show: false,
                },
                axisLine: {
                    lineStyle: {
                        color: theme.axisColor,
                    },
                },
            }] : []),
        ],
        series: createCartesianSeries(config, theme),
    });
}

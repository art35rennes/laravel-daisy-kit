import { createAxisLabelFormatter, createPieLabelFormatter, createTooltipFormatter } from './formatters';

function isPlainObject(value) {
    return value && typeof value === 'object' && !Array.isArray(value);
}

function shouldMergeArrayByIndex(baseValue, nextValue) {
    return Array.isArray(baseValue)
        && Array.isArray(nextValue)
        && baseValue.every((item) => item === undefined || isPlainObject(item))
        && nextValue.every((item) => item === undefined || isPlainObject(item));
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
            if (shouldMergeArrayByIndex(output[key], value)) {
                const length = Math.max(output[key].length, value.length);
                output[key] = Array.from({ length }, (_, index) => {
                    const item = value[index];
                    const baseItem = output[key][index];

                    if (item === undefined) {
                        return baseItem;
                    }

                    return isPlainObject(item) && isPlainObject(baseItem)
                        ? mergeOptions(baseItem, item)
                        : item;
                });
            } else {
                output[key] = value.slice();
            }
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

function createTooltipExtraCss(theme) {
    const shadow = theme.dark
        ? '0 18px 45px rgba(0,0,0,0.48), 0 0 0 1px rgba(255,255,255,0.16)'
        : '0 18px 45px rgba(15,23,42,0.22), 0 0 0 1px rgba(15,23,42,0.10)';

    return `pointer-events:none;z-index:9999;box-shadow:${shadow};`;
}

function createDataZoom(config) {
    if (!config.zoom || !config.isCartesian || config.isSparkline) {
        return undefined;
    }

    const axisIndex = config.isHorizontal ? { yAxisIndex: 0 } : { xAxisIndex: 0 };
    const base = {
        filterMode: 'filter',
        throttle: 80,
        ...axisIndex,
    };

    if (config.zoomMode === 'slider') {
        return [{
            ...base,
            type: 'slider',
            height: config.isHorizontal ? undefined : 20,
            width: config.isHorizontal ? 18 : undefined,
            right: config.isHorizontal ? 0 : undefined,
            bottom: config.isHorizontal ? undefined : 0,
        }];
    }

    return [{
        ...base,
        type: 'inside',
        zoomOnMouseWheel: true,
        moveOnMouseMove: true,
        moveOnMouseWheel: false,
    }];
}

function createSeriesMarkers(config, seriesName) {
    if (!Array.isArray(config.markers) || config.markers.length === 0) {
        return {};
    }

    const relevantMarkers = config.markers.filter((marker) => !marker.series || marker.series === seriesName);
    const markLineData = [];
    const markPointData = [];

    relevantMarkers.forEach((marker) => {
        if (!marker || typeof marker !== 'object') {
            return;
        }

        if (marker.type === 'point') {
            markPointData.push({
                name: marker.name,
                value: marker.value,
                coord: marker.coord,
                xAxis: marker.xAxis,
                yAxis: marker.yAxis,
            });
            return;
        }

        const valueAxis = config.isHorizontal ? 'xAxis' : 'yAxis';
        const categoryAxis = config.isHorizontal ? 'yAxis' : 'xAxis';
        const line = {
            name: marker.name,
            label: marker.label ? { formatter: marker.label } : undefined,
        };

        if (marker.axis === 'x') {
            line.xAxis = marker.value;
        } else if (marker.axis === 'category') {
            line[categoryAxis] = marker.value;
        } else {
            line[valueAxis] = marker.value;
        }

        markLineData.push(line);
    });

    return {
        ...(markLineData.length > 0 ? {
            markLine: {
                symbol: 'none',
                label: {
                    color: 'inherit',
                },
                lineStyle: {
                    type: 'dashed',
                    width: 2,
                },
                data: markLineData,
            },
        } : {}),
        ...(markPointData.length > 0 ? {
            markPoint: {
                symbolSize: 42,
                data: markPointData,
            },
        } : {}),
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
            emphasis: {
                focus: 'none',
                scale: false,
            },
            blur: {
                itemStyle: { opacity: 1 },
                lineStyle: { opacity: 1 },
                areaStyle: { opacity: 1 },
            },
            select: {
                disabled: true,
            },
            smooth: config.preset === 'sparkline',
            ...createSeriesMarkers(config, entry.name),
        };

        if (config.isStacked) {
            lineSeries.stack = entry.stack || 'total';
        }

        if (lineSeries.type === 'line') {
            lineSeries.symbol = 'circle';
            lineSeries.showSymbol = !config.isSparkline;
            lineSeries.symbolSize = config.isSparkline ? 10 : 6;
            lineSeries.lineStyle = {
                color,
                width: config.isSparkline ? 2 : 3,
            };
            lineSeries.emphasis.lineStyle = {
                color,
                width: config.isSparkline ? 2 : 3,
            };
            lineSeries.emphasis.itemStyle = {
                color,
                borderColor: theme.tooltipBackground,
                borderWidth: config.isSparkline ? 3 : 0,
            };
        }

        if (config.isArea) {
            lineSeries.areaStyle = {
                opacity: theme.dark ? 0.24 : 0.16,
            };
            lineSeries.emphasis.areaStyle = {
                opacity: theme.dark ? 0.24 : 0.16,
            };
        }

        if (lineSeries.type === 'bar') {
            lineSeries.barMaxWidth = 40;
            lineSeries.emphasis.itemStyle = {
                color,
                opacity: 1,
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
        animation: false,
        selectedMode: false,
        label: {
            color: 'inherit',
            formatter: createPieLabelFormatter(config.valueFormat),
        },
        data: first.data,
        emphasis: {
            disabled: true,
            focus: 'none',
            scale: false,
            scaleSize: 0,
            itemStyle: {
                shadowBlur: 0,
                shadowOffsetX: 0,
            },
        },
        blur: {
            itemStyle: {
                opacity: 1,
            },
        },
        select: {
            disabled: true,
        },
    }];
}

export function buildChartOption(config, theme) {
    const legend = createLegend(config, theme);
    const toolbox = createToolbox(config);
    const dataZoom = createDataZoom(config);
    const title = baseTitle(config);
    const tooltip = {
        trigger: config.isCircular ? 'item' : 'axis',
        triggerOn: 'mousemove|click',
        renderMode: 'html',
        appendTo: typeof document === 'undefined' ? undefined : document.body,
        confine: false,
        enterable: false,
        extraCssText: createTooltipExtraCss(theme),
        transitionDuration: 0,
        hideDelay: config.isCircular ? 220 : 100,
        showDelay: 0,
        backgroundColor: theme.tooltipBackground,
        borderColor: theme.dark ? 'rgba(255,255,255,0.18)' : 'rgba(15,23,42,0.12)',
        borderWidth: 1,
        textStyle: {
            color: theme.textColor,
        },
        axisPointer: config.isCircular ? undefined : {
            type: config.preset === 'bar' || config.preset === 'stacked-bar' ? 'shadow' : 'line',
            animation: false,
            snap: true,
            lineStyle: {
                color: theme.dark ? 'rgba(255,255,255,0.62)' : 'rgba(15,23,42,0.28)',
                type: 'dashed',
                width: theme.dark ? 1.5 : 1,
            },
            shadowStyle: {
                color: theme.dark ? 'rgba(255,255,255,0.04)' : 'rgba(15,23,42,0.04)',
            },
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
        dataZoom,
        aria: config.aria ? {
            enabled: true,
            decal: {
                show: false,
            },
        } : undefined,
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

    const categoryAxis = {
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
    };
    const valueAxis = {
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
    };
    const secondaryValueAxis = {
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
    };

    return mergeOptions(baseOption, {
        grid: {
            top: legend ? 62 : (title ? 34 : 12),
            right: usesRightAxis || config.isHorizontal ? 56 : 16,
            bottom: dataZoom && config.zoomMode === 'slider' && !config.isHorizontal ? 36 : 16,
            left: config.isHorizontal ? 16 : 12,
            containLabel: true,
        },
        xAxis: config.isHorizontal ? valueAxis : categoryAxis,
        yAxis: config.isHorizontal ? categoryAxis : [
            valueAxis,
            ...(usesRightAxis ? [secondaryValueAxis] : []),
        ],
        series: createCartesianSeries(config, theme),
    });
}

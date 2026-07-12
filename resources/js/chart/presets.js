import { createAxisLabelFormatter, createPieLabelFormatter, createTooltipFormatter, formatValue } from './formatters';

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
                animation: false,
                emphasis: {
                    disabled: true,
                },
                select: {
                    disabled: true,
                },
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
                focus: 'self',
                scale: false,
            },
            blur: {
                itemStyle: { opacity: 0.42 },
                lineStyle: { opacity: 0.42 },
                areaStyle: { opacity: 0.12 },
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
                borderWidth: 0,
            };
            lineSeries.emphasis.disabled = true;
            lineSeries.emphasis.focus = 'none';
            lineSeries.emphasis.symbolSize = lineSeries.symbolSize;
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
                shadowBlur: 10,
                shadowColor: theme.gridColor,
            };
            lineSeries.label = {
                show: config.showValues,
                position: config.isHorizontal ? 'right' : 'top',
                color: theme.textColor,
                formatter(params) {
                    const rawValue = params?.value?.value ?? params?.value;

                    return formatValue(Array.isArray(rawValue) ? rawValue[1] : rawValue, config.valueFormat);
                },
            };
        }

        return lineSeries;
    });
}

function escapeRichText(value) {
    return String(value)
        .replaceAll('{', '﹛')
        .replaceAll('}', '﹜');
}

function createCenterLabel(config, theme) {
    if (config.preset !== 'donut' || (!config.centerValue && !config.centerLabel)) {
        return null;
    }

    const lines = [
        config.centerValue ? `{value|${escapeRichText(config.centerValue)}}` : null,
        config.centerLabel ? `{label|${escapeRichText(config.centerLabel)}}` : null,
    ].filter(Boolean);

    return {
        show: true,
        position: 'center',
        align: 'center',
        verticalAlign: 'middle',
        formatter: lines.join('\n'),
        rich: {
            value: {
                color: theme.textColor,
                fontSize: 22,
                fontWeight: 700,
                lineHeight: 26,
                align: 'center',
            },
            label: {
                color: theme.textMutedColor,
                fontSize: 11,
                lineHeight: 16,
                align: 'center',
            },
        },
    };
}

function createCircularSeries(config, theme) {
    const [first] = config.series;
    if (!first) {
        return [];
    }

    const centerLabel = createCenterLabel(config, theme);
    const data = centerLabel
        ? first.data.map((point, index) => index === 0 ? {
            ...(point && typeof point === 'object' ? point : { value: point }),
            label: centerLabel,
            labelLine: { show: false },
        } : point)
        : first.data;

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
        data,
        emphasis: {
            disabled: true,
            focus: 'none',
            // Keep the sector geometry stable. Scaling changes the SVG hit area
            // under the pointer and can cause alternating mouseover/mouseout events.
            scale: false,
            scaleSize: 0,
            itemStyle: {
                opacity: 1,
                shadowBlur: 0,
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
    const title = undefined;
    const tooltip = {
        trigger: config.isCircular ? 'item' : 'axis',
        triggerOn: 'mousemove|click',
        renderMode: 'html',
        appendTo: 'body',
        confine: false,
        enterable: false,
        displayTransition: false,
        extraCssText: 'pointer-events:none;z-index:var(--daisy-z-tooltip-content,81);box-shadow:none;',
        transitionDuration: 0,
        hideDelay: config.isCircular ? 220 : 100,
        showDelay: 0,
        backgroundColor: theme.tooltipBackground,
        borderColor: theme.dark ? 'rgba(255,255,255,0.18)' : 'rgba(15,23,42,0.12)',
        borderWidth: 1,
        shadowBlur: 0,
        shadowColor: 'transparent',
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
        animation: config.animation,
        animationDuration: config.animation ? 240 : 0,
        animationDurationUpdate: config.animation ? 180 : 0,
        stateAnimation: {
            duration: 0,
        },
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
            series: createCircularSeries(config, theme),
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

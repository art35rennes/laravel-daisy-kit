/**
 * Daisy Kit - Signature Pad
 *
 * CSP-safe signature component based on Pointer Events and Canvas 2D.
 *
 * Events:
 * - sign:change
 * - sign:clear
 * - sign:end
 */

function parseNumber(value, fallback) {
    const parsed = Number(value);

    return Number.isFinite(parsed) ? parsed : fallback;
}

function getCanvasContext(canvas) {
    return canvas.getContext('2d');
}

function createImage(dataUrl) {
    return new Promise((resolve, reject) => {
        const image = new Image();
        image.addEventListener('load', () => resolve(image), { once: true });
        image.addEventListener('error', reject, { once: true });
        image.src = dataUrl;
    });
}

export default function initSign(root, options = {}) {
    if (!root || root.__signInit) {
        return root?.__daisySign ?? null;
    }

    root.__signInit = true;

    const canvas = root.querySelector('[data-sign-canvas]');
    const canvasWrapper = root.querySelector('[data-sign-canvas-wrapper]');
    const input = root.querySelector('[data-sign-input]');
    const clearButton = root.querySelector('[data-sign-clear]');
    const downloadButton = root.querySelector('[data-sign-download]');

    if (!canvas || !canvasWrapper) {
        console.warn('[DaisySign] Canvas not found');

        return null;
    }

    const context = getCanvasContext(canvas);

    if (!context) {
        console.warn('[DaisySign] Canvas context not available');

        return null;
    }

    const config = {
        width: parseInt(root.dataset.width || options.width || 400),
        height: parseInt(root.dataset.height || options.height || 200),
        penColor: root.dataset.penColor || options.penColor || '#000000',
        minWidth: parseNumber(root.dataset.minWidth || options.minWidth, 0.5),
        maxWidth: parseNumber(root.dataset.maxWidth || options.maxWidth, 2.5),
        responsive: root.dataset.responsive !== 'false' && options.responsive !== false,
        disabled: root.dataset.disabled === 'true' || options.disabled === true,
        showActions: root.dataset.showActions !== 'false' && options.showActions !== false,
        downloadFormat: root.dataset.downloadFormat || options.downloadFormat || 'png',
        downloadFilename: root.dataset.downloadFilename || options.downloadFilename || 'signature',
    };

    let currentDataUrl = input?.value || '';
    let isDrawing = false;
    let lastPoint = null;
    let resizeObserver = null;

    function getRatio() {
        return Math.max(window.devicePixelRatio || 1, 1);
    }

    function getDisplaySize() {
        if (!config.responsive) {
            return {
                width: config.width,
                height: config.height,
            };
        }

        const width = Math.max(1, Math.round(canvasWrapper.clientWidth || config.width));

        return {
            width,
            height: Math.max(1, Math.round(width / (config.width / config.height))),
        };
    }

    function setCanvasSize() {
        const ratio = getRatio();
        const { width, height } = getDisplaySize();

        canvas.width = Math.round(width * ratio);
        canvas.height = Math.round(height * ratio);

        if (typeof context.setTransform === 'function') {
            context.setTransform(ratio, 0, 0, ratio, 0, 0);
        } else {
            context.scale(ratio, ratio);
        }
    }

    function clearCanvas() {
        const ratio = getRatio();
        context.clearRect(0, 0, canvas.width / ratio, canvas.height / ratio);
    }

    async function drawDataUrl(dataUrl) {
        if (!dataUrl) {
            clearCanvas();

            return;
        }

        const image = await createImage(dataUrl);
        const ratio = getRatio();

        clearCanvas();
        context.drawImage(image, 0, 0, canvas.width / ratio, canvas.height / ratio);
        currentDataUrl = dataUrl;
    }

    function syncInput() {
        if (input) {
            input.value = currentDataUrl;
        }
    }

    function dispatchChange() {
        root.dispatchEvent(new CustomEvent('sign:change', {
            detail: {
                isEmpty: currentDataUrl === '',
                dataURL: currentDataUrl,
            },
            bubbles: true,
        }));
    }

    function updateFromCanvas() {
        currentDataUrl = canvas.toDataURL();
        syncInput();
        dispatchChange();
    }

    function getPoint(event) {
        const rect = canvas.getBoundingClientRect();

        return {
            x: event.clientX - rect.left,
            y: event.clientY - rect.top,
        };
    }

    function strokeWidth() {
        return Math.max(config.minWidth, (config.minWidth + config.maxWidth) / 2);
    }

    function beginStroke(event) {
        if (config.disabled || event.button > 0) {
            return;
        }

        isDrawing = true;
        lastPoint = getPoint(event);
        canvas.setPointerCapture?.(event.pointerId);
        context.beginPath();
        context.moveTo(lastPoint.x, lastPoint.y);
        event.preventDefault();
    }

    function moveStroke(event) {
        if (!isDrawing || !lastPoint) {
            return;
        }

        const point = getPoint(event);

        context.beginPath();
        context.moveTo(lastPoint.x, lastPoint.y);
        context.lineTo(point.x, point.y);
        context.strokeStyle = config.penColor;
        context.lineWidth = strokeWidth();
        context.lineCap = 'round';
        context.lineJoin = 'round';
        context.stroke();

        lastPoint = point;
        event.preventDefault();
    }

    function endStroke(event) {
        if (!isDrawing) {
            return;
        }

        moveStroke(event);
        isDrawing = false;
        lastPoint = null;
        canvas.releasePointerCapture?.(event.pointerId);
        updateFromCanvas();
        root.dispatchEvent(new CustomEvent('sign:end', {
            detail: {
                isEmpty: currentDataUrl === '',
            },
            bubbles: true,
        }));
    }

    function clearSignature() {
        clearCanvas();
        currentDataUrl = '';
        syncInput();
        dispatchChange();
        root.dispatchEvent(new CustomEvent('sign:clear', {
            bubbles: true,
        }));
    }

    async function resizeCanvas() {
        const previousDataUrl = currentDataUrl;

        setCanvasSize();

        if (previousDataUrl) {
            await drawDataUrl(previousDataUrl);
            syncInput();
        }
    }

    function downloadSignature() {
        if (!currentDataUrl) {
            return;
        }

        const dataUrl = config.downloadFormat === 'png'
            ? currentDataUrl
            : canvas.toDataURL(`image/${config.downloadFormat}`);
        const link = document.createElement('a');

        link.href = dataUrl;
        link.download = `${config.downloadFilename}.${config.downloadFormat}`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    setCanvasSize();

    if (currentDataUrl) {
        drawDataUrl(currentDataUrl).then(syncInput).catch(() => {
            currentDataUrl = '';
            syncInput();
        });
    }

    if (!config.disabled) {
        canvas.addEventListener('pointerdown', beginStroke);
        canvas.addEventListener('pointermove', moveStroke);
        canvas.addEventListener('pointerup', endStroke);
        canvas.addEventListener('pointercancel', endStroke);
        canvas.addEventListener('pointerleave', endStroke);
    }

    if (config.responsive && typeof ResizeObserver !== 'undefined') {
        resizeObserver = new ResizeObserver(() => {
            resizeCanvas();
        });
        resizeObserver.observe(canvasWrapper);
    }

    clearButton?.addEventListener('click', clearSignature);
    downloadButton?.addEventListener('click', downloadSignature);

    if (config.disabled) {
        if (clearButton) {
            clearButton.disabled = true;
        }

        if (downloadButton) {
            downloadButton.disabled = true;
        }
    }

    const api = {
        clear: clearSignature,
        clearSignature,
        dispose() {
            resizeObserver?.disconnect();
        },
        getSignature() {
            return currentDataUrl;
        },
        isEmpty() {
            return currentDataUrl === '';
        },
        setMaxWidth(width) {
            config.maxWidth = parseNumber(width, config.maxWidth);
        },
        setMinWidth(width) {
            config.minWidth = parseNumber(width, config.minWidth);
        },
        setPenColor(color) {
            config.penColor = color;
        },
        toDataURL() {
            return currentDataUrl;
        },
    };

    root.__daisySign = api;
    root.__signaturePad = api;
    root.clearSignature = clearSignature;
    root.getSignature = api.getSignature;
    root.isEmpty = api.isEmpty;
    root.setPenColor = api.setPenColor;
    root.setMinWidth = api.setMinWidth;
    root.setMaxWidth = api.setMaxWidth;

    return api;
}

export function initAllSigns() {
    document.querySelectorAll('[data-sign="1"]').forEach((element) => {
        initSign(element);
    });
}

if (typeof window !== 'undefined') {
    window.DaisySign = {
        init: initSign,
        initAll: initAllSigns,
    };
}

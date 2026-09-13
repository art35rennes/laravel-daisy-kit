import SignaturePad from 'signature_pad';
import '../css/signature.css';
import { createMountable } from './core/mountable.js';

function emit(root, name, detail = {}) {
    root.dispatchEvent(new CustomEvent(`daisy-kit:signature:${name}`, { bubbles: true, detail }));
}

function initialize(root, configuration) {
    const canvas = root.querySelector('[data-daisy-kit-signature-canvas]');
    const input = root.querySelector('[data-daisy-kit-signature-value]');

    if (!(canvas instanceof HTMLCanvasElement) || !(input instanceof HTMLInputElement)) {
        throw new Error('Signature requires a canvas and a value input.');
    }

    const initialValue = input.value;
    const initialCanvas = {
        height: canvas.getAttribute('height'),
        style: canvas.getAttribute('style'),
        width: canvas.getAttribute('width'),
    };
    const logicalWidth = Math.max(1, Number(configuration.width) || canvas.width || 640);
    const logicalHeight = Math.max(1, Number(configuration.height) || canvas.height || 240);
    const pad = new SignaturePad(canvas, {
        backgroundColor: typeof configuration.backgroundColor === 'string' ? configuration.backgroundColor : 'rgba(0,0,0,0)',
        maxWidth: Number(configuration.maxWidth) || 2.5,
        minDistance: Number(configuration.minDistance) || 5,
        minWidth: Number(configuration.minWidth) || 0.5,
        penColor: typeof configuration.penColor === 'string' ? configuration.penColor : 'black',
        throttle: Number.isFinite(Number(configuration.throttle)) ? Number(configuration.throttle) : 16,
        velocityFilterWeight: Number(configuration.velocityFilterWeight) || 0.7,
    });
    const redoGroups = [];
    let active = true;
    let resizeFrame = null;
    let canvasRatio = 1;
    let importedImage = null;
    let importRevision = 0;
    let cancelImport = null;

    function cancelPendingImport() {
        importRevision += 1;
        cancelImport?.();
        cancelImport = null;
    }

    function isEmpty() {
        return importedImage === null && pad.isEmpty();
    }

    function drawImportedImage() {
        if (importedImage === null) return;
        canvas.getContext('2d')?.drawImage(importedImage.image, 0, 0, importedImage.width, importedImage.height);
    }

    function redraw(groups) {
        pad.clear();
        drawImportedImage();
        pad.fromData(groups, { clear: false });
    }

    function loadImage(value) {
        return new Promise((resolve, reject) => {
            const image = new Image();
            function finish(result, error = null) {
                image.onload = null;
                image.onerror = null;
                if (error) reject(error);
                else resolve(result);
            }
            cancelImport = () => finish(null);
            image.onload = () => finish(image);
            image.onerror = () => finish(null, new Error('Invalid image'));
            image.crossOrigin = 'anonymous';
            image.src = value;
        });
    }

    function reportError(error, value) {
        emit(root, 'error', {
            code: 'invalid-value',
            message: error instanceof Error && error.message !== '' ? error.message : 'The signature value is invalid.',
            value,
        });
    }

    function sync(emitChange = true) {
        input.value = isEmpty() ? '' : pad.toDataURL('image/png');
        input.setCustomValidity(configuration.required === true && input.value === '' ? 'A signature is required.' : '');
        if (emitChange) emit(root, 'change', { empty: isEmpty(), value: input.value });
    }

    function resize() {
        if (!active) return;
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        const displayedWidth = canvas.getBoundingClientRect().width || logicalWidth;
        const displayedHeight = displayedWidth * (logicalHeight / logicalWidth);
        const nextWidth = Math.round(displayedWidth * ratio);
        const nextHeight = Math.round(displayedHeight * ratio);
        if (canvas.width === nextWidth && canvas.height === nextHeight) return;
        const groups = pad.toData();
        canvas.width = nextWidth;
        canvas.height = nextHeight;
        const context = canvas.getContext('2d');
        context?.scale(ratio, ratio);
        canvasRatio = ratio;
        redraw(groups);
        sync(false);
    }

    function clear() {
        if (!active) return false;
        cancelPendingImport();
        importedImage = null;
        pad.clear();
        redoGroups.length = 0;
        sync();
        emit(root, 'clear', { empty: true, value: '' });

        return true;
    }

    function undo() {
        if (!active) return false;
        cancelPendingImport();
        const groups = pad.toData();
        const removed = groups.pop();
        if (!removed) return false;
        redoGroups.push(removed);
        redraw(groups);
        sync();
        return true;
    }

    function redo() {
        if (!active) return false;
        cancelPendingImport();
        const restored = redoGroups.pop();
        if (!restored) return false;
        redraw([...pad.toData(), restored]);
        sync();
        return true;
    }

    async function setValue(value) {
        if (!active) return false;
        cancelPendingImport();
        const revision = importRevision;
        if (typeof value !== 'string') {
            reportError(new TypeError('The signature value must be a Data URL string.'), value);

            return false;
        }

        if (value === '') {
            clear();

            return true;
        }
        try {
            const image = await loadImage(value);
            if (!active || revision !== importRevision || image === null) return false;
            cancelImport = null;
            importedImage = { image, width: canvas.width / canvasRatio, height: canvas.height / canvasRatio };
            pad.clear();
            drawImportedImage();
            redoGroups.length = 0;
            sync();

            return true;
        } catch (error) {
            if (!active || revision !== importRevision) return false;
            cancelImport = null;
            reportError(error, value);

            return false;
        }
    }

    function toSVG(options = {}) {
        const svg = pad.toSVG(options);
        if (importedImage === null || options.includeDataUrl !== true) return svg;
        const image = document.createElementNS('http://www.w3.org/2000/svg', 'image');
        image.setAttribute('href', importedImage.image.src);
        image.setAttribute('width', String(importedImage.width));
        image.setAttribute('height', String(importedImage.height));

        return svg.replace(/(<path\b|<\/svg>)/, (match) => `${image.outerHTML}${match}`);
    }

    function onStrokeEnd() {
        if (!active) return;
        cancelPendingImport();
        redoGroups.length = 0;
        sync();
        emit(root, 'stroke-ended', { value: input.value });
    }

    function onAction(event) {
        const button = event.target.closest('button');
        if (!button || !root.contains(button)) return;
        if (button.matches('[data-daisy-kit-signature-undo]')) undo();
        if (button.matches('[data-daisy-kit-signature-redo]')) redo();
        if (button.matches('[data-daisy-kit-signature-clear]')) clear();
        if (button.matches('[data-daisy-kit-signature-download]')) {
            const link = document.createElement('a');
            link.download = 'signature.png';
            link.href = pad.toDataURL('image/png');
            link.click();
        }
    }

    const resizeObserver = typeof ResizeObserver === 'function' ? new ResizeObserver(() => {
        if (resizeFrame !== null) window.cancelAnimationFrame(resizeFrame);
        resizeFrame = window.requestAnimationFrame(() => {
            resizeFrame = null;
            resize();
        });
    }) : null;
    root.addEventListener('click', onAction);
    pad.addEventListener('beginStroke', cancelPendingImport);
    pad.addEventListener('endStroke', onStrokeEnd);
    resizeObserver?.observe(canvas.parentElement ?? canvas);
    resize();

    if (configuration.disabled === true) pad.off();
    if (typeof configuration.value === 'string' && configuration.value !== '') {
        setValue(configuration.value).catch(() => {});
    } else {
        sync(false);
    }

    return {
        clear,
        destroy() {
            if (!active) return;
            active = false;
            cancelPendingImport();
            resizeObserver?.disconnect();
            if (resizeFrame !== null) window.cancelAnimationFrame(resizeFrame);
            root.removeEventListener('click', onAction);
            pad.removeEventListener('beginStroke', cancelPendingImport);
            pad.removeEventListener('endStroke', onStrokeEnd);
            pad.off();
            input.value = initialValue;
            if (initialCanvas.width === null) canvas.removeAttribute('width'); else canvas.setAttribute('width', initialCanvas.width);
            if (initialCanvas.height === null) canvas.removeAttribute('height'); else canvas.setAttribute('height', initialCanvas.height);
            if (initialCanvas.style === null) canvas.removeAttribute('style'); else canvas.setAttribute('style', initialCanvas.style);
        },
        isEmpty,
        redo,
        setValue,
        toData: () => structuredClone(pad.toData()),
        toDataURL: (type = 'image/png', encoderOptions) => type === 'image/svg+xml'
            ? `data:image/svg+xml;base64,${btoa(toSVG(encoderOptions))}`
            : pad.toDataURL(type, encoderOptions),
        toSVG,
        undo,
    };
}

const module = createMountable('signature', initialize);

export const { getInstance, mount, mountAll, unmount } = module;

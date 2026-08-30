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

    function reportError(error, value) {
        emit(root, 'error', {
            code: 'invalid-value',
            message: error instanceof Error && error.message !== '' ? error.message : 'The signature value is invalid.',
            value,
        });
    }

    function sync(emitChange = true) {
        input.value = pad.isEmpty() ? '' : pad.toDataURL('image/png');
        input.setCustomValidity(configuration.required === true && input.value === '' ? 'A signature is required.' : '');
        if (emitChange) emit(root, 'change', { empty: pad.isEmpty(), value: input.value });
    }

    function resize() {
        const groups = pad.toData();
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        const displayedWidth = canvas.getBoundingClientRect().width || logicalWidth;
        const displayedHeight = displayedWidth * (logicalHeight / logicalWidth);
        const nextWidth = Math.round(displayedWidth * ratio);
        const nextHeight = Math.round(displayedHeight * ratio);
        if (canvas.width === nextWidth && canvas.height === nextHeight) return;
        canvas.width = nextWidth;
        canvas.height = nextHeight;
        canvas.getContext('2d')?.scale(ratio, ratio);
        pad.clear();
        if (groups.length > 0) pad.fromData(groups);
        sync(false);
    }

    function clear() {
        pad.clear();
        redoGroups.length = 0;
        sync();
        emit(root, 'clear', { empty: true, value: '' });

        return true;
    }

    function undo() {
        const groups = pad.toData();
        const removed = groups.pop();
        if (!removed) return false;
        redoGroups.push(removed);
        pad.fromData(groups);
        sync();
        return true;
    }

    function redo() {
        const restored = redoGroups.pop();
        if (!restored) return false;
        pad.fromData([...pad.toData(), restored]);
        sync();
        return true;
    }

    async function setValue(value) {
        if (typeof value !== 'string') {
            reportError(new TypeError('The signature value must be a Data URL string.'), value);

            return false;
        }

        if (value === '') {
            clear();

            return true;
        }
        try {
            await pad.fromDataURL(value);
            redoGroups.length = 0;
            sync();

            return true;
        } catch (error) {
            reportError(error, value);

            return false;
        }
    }

    function onStrokeEnd() {
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
            resizeObserver?.disconnect();
            if (resizeFrame !== null) window.cancelAnimationFrame(resizeFrame);
            root.removeEventListener('click', onAction);
            pad.removeEventListener('endStroke', onStrokeEnd);
            pad.off();
            input.value = initialValue;
            if (initialCanvas.width === null) canvas.removeAttribute('width'); else canvas.setAttribute('width', initialCanvas.width);
            if (initialCanvas.height === null) canvas.removeAttribute('height'); else canvas.setAttribute('height', initialCanvas.height);
            if (initialCanvas.style === null) canvas.removeAttribute('style'); else canvas.setAttribute('style', initialCanvas.style);
        },
        isEmpty: () => pad.isEmpty(),
        redo,
        setValue,
        toData: () => structuredClone(pad.toData()),
        toDataURL: (type = 'image/png', encoderOptions) => pad.toDataURL(type, encoderOptions),
        toSVG: (options) => pad.toSVG(options),
        undo,
    };
}

const module = createMountable('signature', initialize);

export const { getInstance, mount, mountAll, unmount } = module;

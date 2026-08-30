import '../css/truncate.css';
import { createMountable } from './core/mountable.js';

function emit(root, name, detail) {
    root.dispatchEvent(new CustomEvent(`daisy-kit:truncate:${name}`, { bubbles: true, detail }));
}

function lineCount(configuration) {
    if (!Number.isSafeInteger(configuration.lines)) {
        return 1;
    }

    return Math.min(Math.max(configuration.lines, 1), 6);
}

function initialize(root, configuration) {
    const text = root.querySelector('[data-daisy-kit-truncate-text]');
    const reveal = root.querySelector('[data-daisy-kit-truncate-reveal]');
    const popover = root.querySelector('[data-daisy-kit-truncate-popover]');
    const fullText = root.querySelector('[data-daisy-kit-truncate-full-text]');
    const title = root.querySelector('[data-daisy-kit-truncate-title]');
    const closeButton = root.querySelector('[data-daisy-kit-truncate-close]');

    if (!(text instanceof HTMLElement) || !(reveal instanceof HTMLButtonElement) || !(popover instanceof HTMLElement) || !(fullText instanceof HTMLElement)) {
        throw new Error('The truncate module is missing required markup.');
    }

    const initialMarkup = root.innerHTML;
    const value = typeof configuration.text === 'string' ? configuration.text : '';
    const revealLabel = typeof configuration.revealLabel === 'string' && configuration.revealLabel !== ''
        ? configuration.revealLabel
        : 'Read full text';
    const configuredTitle = typeof configuration.title === 'string' && configuration.title !== ''
        ? configuration.title
        : null;
    let truncated = false;
    let open = false;
    let resizeObserver = null;

    text.textContent = value;
    text.dataset.daisyKitTruncateLines = String(lineCount(configuration));
    fullText.textContent = value;
    reveal.textContent = revealLabel;
    reveal.setAttribute('aria-expanded', 'false');
    reveal.hidden = true;

    if (title) {
        title.hidden = configuredTitle === null;
        title.textContent = configuredTitle ?? '';
    }

    function isTruncated() {
        return text.scrollWidth > text.clientWidth || text.scrollHeight > text.clientHeight;
    }

    function refresh() {
        truncated = isTruncated();
        reveal.hidden = !truncated;

        if (!truncated && open) {
            close();
        }

        return truncated;
    }

    function showNativePopover() {
        if (typeof popover.showPopover === 'function') {
            popover.showPopover();
        }
    }

    function hideNativePopover() {
        if (typeof popover.hidePopover === 'function') {
            popover.hidePopover();
        }
    }

    function openPopover() {
        if (!truncated || open) {
            return false;
        }

        showNativePopover();
        open = true;
        reveal.setAttribute('aria-expanded', 'true');
        emit(root, 'opened', { text: value });

        return true;
    }

    function close() {
        if (!open) {
            return false;
        }

        hideNativePopover();
        open = false;
        reveal.setAttribute('aria-expanded', 'false');
        emit(root, 'closed', { text: value });

        return true;
    }

    function toggle() {
        if (open) {
            close();

            return;
        }

        openPopover();
    }

    function handleClose() {
        close();
    }

    function handleKeydown(event) {
        if (event.key === 'Escape') {
            close();
        }
    }

    reveal.addEventListener('click', toggle);
    closeButton?.addEventListener('click', handleClose);
    popover.addEventListener('keydown', handleKeydown);

    if ('ResizeObserver' in window) {
        resizeObserver = new ResizeObserver(refresh);
        resizeObserver.observe(text);
    } else {
        window.addEventListener('resize', refresh);
    }

    refresh();

    return {
        close,
        isTruncated: () => truncated,
        open: openPopover,
        refresh,
        destroy() {
            resizeObserver?.disconnect();
            window.removeEventListener('resize', refresh);
            reveal.removeEventListener('click', toggle);
            closeButton?.removeEventListener('click', handleClose);
            popover.removeEventListener('keydown', handleKeydown);
            root.innerHTML = initialMarkup;
        },
    };
}

const module = createMountable('truncate', initialize);

export const { getInstance, mount, mountAll, unmount } = module;

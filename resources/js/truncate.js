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

function hoverDelay(configuration) {
    if (!Number.isSafeInteger(configuration.hoverDelay)) {
        return 250;
    }

    return Math.min(Math.max(configuration.hoverDelay, 0), 2000);
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
    const hoverEnabled = configuration.hover !== false;
    const configuredHoverDelay = hoverDelay(configuration);
    const backdropEnabled = configuration.backdrop === true;
    let truncated = false;
    let open = false;
    let pinned = false;
    let resizeObserver = null;
    let hoverTimer = null;

    text.textContent = value;
    text.dataset.daisyKitTruncateLines = String(lineCount(configuration));
    fullText.textContent = value;
    reveal.textContent = '…';
    reveal.setAttribute('aria-label', revealLabel);
    reveal.setAttribute('aria-expanded', 'false');
    reveal.hidden = true;
    popover.dataset.daisyKitTruncateBackdrop = 'false';
    popover.dataset.daisyKitTruncatePinned = 'false';

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
            popover.showPopover({ source: reveal });
        }
    }

    function hideNativePopover() {
        if (typeof popover.hidePopover === 'function') {
            popover.hidePopover();
        }
    }

    function clearHoverTimer() {
        if (hoverTimer !== null) {
            window.clearTimeout(hoverTimer);
            hoverTimer = null;
        }
    }

    function synchronizeOpenState(nextOpen, nextPinned = false) {
        open = nextOpen;
        pinned = nextOpen && nextPinned;
        reveal.setAttribute('aria-expanded', String(nextOpen));
        popover.dataset.daisyKitTruncatePinned = String(pinned);
        popover.dataset.daisyKitTruncateBackdrop = String(pinned && backdropEnabled);
    }

    function openPopover(nextPinned = true) {
        if (!truncated) {
            return false;
        }

        clearHoverTimer();

        if (open) {
            if (!pinned && nextPinned) {
                synchronizeOpenState(true, true);

                return true;
            }

            return false;
        }

        showNativePopover();
        synchronizeOpenState(true, nextPinned);
        emit(root, 'opened', { text: value });

        return true;
    }

    function close(emitEvent = true) {
        clearHoverTimer();

        if (!open) {
            return false;
        }

        hideNativePopover();
        synchronizeOpenState(false);

        if (emitEvent) {
            emit(root, 'closed', { text: value });
        }

        return true;
    }

    function scheduleTemporaryOpen() {
        if (!hoverEnabled || pinned || !truncated) {
            return;
        }

        clearHoverTimer();
        hoverTimer = window.setTimeout(() => {
            hoverTimer = null;
            openPopover(false);
        }, configuredHoverDelay);
    }

    function scheduleTemporaryClose() {
        if (pinned) {
            return;
        }

        clearHoverTimer();
        hoverTimer = window.setTimeout(() => {
            hoverTimer = null;
            close();
        }, configuredHoverDelay);
    }

    function toggle(event) {
        event.preventDefault();

        if (open && pinned) {
            close();

            return;
        }

        openPopover(true);
    }

    function handleClose() {
        close();
    }

    function handleKeydown(event) {
        if (event.key === 'Escape') {
            close();
        }
    }

    function handleRevealBlur(event) {
        if (event.relatedTarget instanceof Node && popover.contains(event.relatedTarget)) {
            return;
        }

        scheduleTemporaryClose();
    }

    function handlePopoverBlur(event) {
        if (event.relatedTarget instanceof Node && reveal.contains(event.relatedTarget)) {
            return;
        }

        scheduleTemporaryClose();
    }

    function handleNativeToggle(event) {
        if (event.newState !== 'closed' || !open) {
            return;
        }

        clearHoverTimer();
        synchronizeOpenState(false);
        emit(root, 'closed', { text: value });
    }

    reveal.addEventListener('click', toggle);
    reveal.addEventListener('focus', scheduleTemporaryOpen);
    reveal.addEventListener('blur', handleRevealBlur);
    reveal.addEventListener('pointerenter', scheduleTemporaryOpen);
    reveal.addEventListener('pointerleave', scheduleTemporaryClose);
    closeButton?.addEventListener('click', handleClose);
    popover.addEventListener('blur', handlePopoverBlur, true);
    popover.addEventListener('keydown', handleKeydown);
    popover.addEventListener('pointerenter', clearHoverTimer);
    popover.addEventListener('pointerleave', scheduleTemporaryClose);
    popover.addEventListener('toggle', handleNativeToggle);

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
            clearHoverTimer();
            close(false);
            resizeObserver?.disconnect();
            window.removeEventListener('resize', refresh);
            reveal.removeEventListener('click', toggle);
            reveal.removeEventListener('focus', scheduleTemporaryOpen);
            reveal.removeEventListener('blur', handleRevealBlur);
            reveal.removeEventListener('pointerenter', scheduleTemporaryOpen);
            reveal.removeEventListener('pointerleave', scheduleTemporaryClose);
            closeButton?.removeEventListener('click', handleClose);
            popover.removeEventListener('blur', handlePopoverBlur, true);
            popover.removeEventListener('keydown', handleKeydown);
            popover.removeEventListener('pointerenter', clearHoverTimer);
            popover.removeEventListener('pointerleave', scheduleTemporaryClose);
            popover.removeEventListener('toggle', handleNativeToggle);
            root.innerHTML = initialMarkup;
        },
    };
}

const module = createMountable('truncate', initialize);

export const { getInstance, mount, mountAll, unmount } = module;

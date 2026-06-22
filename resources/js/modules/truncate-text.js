function isTruncated(element) {
    return element.scrollWidth > element.clientWidth || element.scrollHeight > element.clientHeight;
}

function asBoolean(value) {
    return value === true || value === 'true';
}

function getPopover(element) {
    const wrapper = element.parentElement;

    if (!wrapper) {
        return null;
    }

    return Array.from(wrapper.children).find(child => child.classList.contains('daisy-truncate-popover')) || null;
}

function hidePopover(popover) {
    if (!popover) {
        return;
    }

    popover.classList.add('hidden');
    popover.setAttribute('aria-hidden', 'true');
}

function hideOtherPopovers(element) {
    document.querySelectorAll('.daisy-truncate-popover').forEach((popover) => {
        if (popover !== getPopover(element)) {
            hidePopover(popover);
        }
    });
}

function showPopover(element) {
    const popover = getPopover(element);

    if (!popover) {
        return;
    }

    hideOtherPopovers(element);
    popover.classList.remove('hidden');
    popover.setAttribute('aria-hidden', 'false');
}

function revealMode(element) {
    if (element.dataset.truncateTextReveal) {
        return element.dataset.truncateTextReveal;
    }

    if (asBoolean(element.dataset.truncateTextTooltip) && asBoolean(element.dataset.truncateTextPopover)) {
        return 'both';
    }

    if (asBoolean(element.dataset.truncateTextPopover)) {
        return 'popover';
    }

    return asBoolean(element.dataset.truncateTextTooltip) ? 'tooltip' : 'none';
}

function onlyWhenTruncated(element) {
    if (element.dataset.truncateTextOnlyWhenTruncated) {
        return asBoolean(element.dataset.truncateTextOnlyWhenTruncated);
    }

    return asBoolean(element.dataset.truncateTextPopoverOnlyWhenTruncated);
}

function hasTooltip(element) {
    return ['tooltip', 'both'].includes(revealMode(element));
}

function hasPopover(element) {
    return ['popover', 'both'].includes(revealMode(element));
}

function canOpenPopover(element) {
    if (!hasPopover(element)) {
        return false;
    }

    return !onlyWhenTruncated(element) || isTruncated(element);
}

function syncTooltip(element) {
    const wrapper = element.closest('.tooltip');
    const label = element.dataset.truncateTextTitle || element.textContent.trim();
    const tooltipIsEnabled = hasTooltip(element);
    const popoverIsEnabled = hasPopover(element);
    const truncated = isTruncated(element);
    const overflowOnly = onlyWhenTruncated(element);
    const popover = getPopover(element);
    const tooltipIsAvailable = tooltipIsEnabled && (!overflowOnly || truncated);
    const popoverIsAvailable = popoverIsEnabled && (!overflowOnly || truncated);

    if (label === '') {
        return;
    }

    if (tooltipIsAvailable && wrapper) {
        wrapper.dataset.tip = label;
        element.setAttribute('aria-label', label);
        element.setAttribute('tabindex', '0');
    } else if (tooltipIsEnabled && wrapper) {
        delete wrapper.dataset.tip;

        if (!popoverIsAvailable) {
            element.removeAttribute('aria-label');
            element.removeAttribute('tabindex');
        }
    }

    if (popoverIsAvailable) {
        element.setAttribute('aria-label', label);
        element.setAttribute('tabindex', '0');
        element.setAttribute('role', 'button');
        element.setAttribute('aria-haspopup', 'dialog');

        return;
    }

    hidePopover(popover);
    element.removeAttribute('role');
    element.removeAttribute('aria-haspopup');

    if (!tooltipIsEnabled || !truncated) {
        element.removeAttribute('tabindex');
    }
}

export default function initTruncateText(root) {
    if (root.dataset.truncateTextInitialized === 'true') {
        return;
    }

    root.dataset.truncateTextInitialized = 'true';
    syncTooltip(root);

    root.addEventListener('click', (event) => {
        if (!canOpenPopover(root)) {
            return;
        }

        const popover = getPopover(root);

        if (!popover || popover.classList.contains('hidden') === false) {
            return;
        }

        event.preventDefault();
        showPopover(root);
    });

    root.addEventListener('keydown', (event) => {
        if (!canOpenPopover(root)) {
            return;
        }

        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            showPopover(root);
        }

        if (event.key === 'Escape') {
            hidePopover(getPopover(root));
        }
    });

    document.addEventListener('click', (event) => {
        const popover = getPopover(root);

        if (!popover || popover.classList.contains('hidden')) {
            return;
        }

        if (root.contains(event.target) || popover.contains(event.target)) {
            return;
        }

        hidePopover(popover);
    });

    if ('ResizeObserver' in window) {
        const observer = new ResizeObserver(() => syncTooltip(root));
        observer.observe(root);

        return;
    }

    window.addEventListener('resize', () => syncTooltip(root), { passive: true });
}

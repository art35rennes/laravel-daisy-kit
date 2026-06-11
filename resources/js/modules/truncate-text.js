function isTruncated(element) {
    return element.scrollWidth > element.clientWidth || element.scrollHeight > element.clientHeight;
}

function syncTooltip(element) {
    const wrapper = element.closest('.tooltip');
    const label = element.dataset.truncateTextTitle || element.textContent.trim();

    if (!wrapper || label === '') {
        return;
    }

    if (isTruncated(element)) {
        wrapper.dataset.tip = label;
        element.setAttribute('aria-label', label);
        element.setAttribute('tabindex', '0');

        return;
    }

    delete wrapper.dataset.tip;
    element.removeAttribute('aria-label');
    element.removeAttribute('tabindex');
}

export default function initTruncateText(root) {
    if (root.dataset.truncateTextInitialized === 'true') {
        return;
    }

    root.dataset.truncateTextInitialized = 'true';
    syncTooltip(root);

    if ('ResizeObserver' in window) {
        const observer = new ResizeObserver(() => syncTooltip(root));
        observer.observe(root);

        return;
    }

    window.addEventListener('resize', () => syncTooltip(root), { passive: true });
}

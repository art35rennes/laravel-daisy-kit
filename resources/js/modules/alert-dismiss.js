const MIN_PROGRESS_INTERVAL = 50;

function parseDelay(value) {
    const delay = Number.parseInt(value, 10);

    return Number.isFinite(delay) && delay > 0 ? delay : null;
}

function dismiss(element) {
    if (! element.isConnected) {
        return;
    }

    element.dispatchEvent(new CustomEvent('daisy:alert-dismiss', {
        bubbles: true,
    }));

    element.remove();
}

function updateProgress(element, remaining, delay) {
    const progress = element.querySelector('[data-alert-progress]');
    const remainingLabel = element.querySelector('[data-alert-remaining]');
    const safeRemaining = Math.max(remaining, 0);

    if (progress) {
        progress.value = Math.ceil((safeRemaining / delay) * 100);
    }

    if (remainingLabel) {
        remainingLabel.textContent = `${Math.ceil(safeRemaining / 1000)}s`;
    }
}

function initAutoDismiss(element, delay) {
    const startedAt = Date.now();
    const interval = window.setInterval(() => {
        if (! element.isConnected) {
            window.clearInterval(interval);
            window.clearTimeout(timeout);

            return;
        }

        const remaining = delay - (Date.now() - startedAt);

        updateProgress(element, remaining, delay);

        if (remaining <= 0) {
            window.clearInterval(interval);
            dismiss(element);
        }
    }, Math.min(MIN_PROGRESS_INTERVAL, delay));

    const timeout = window.setTimeout(() => {
        window.clearInterval(interval);
        updateProgress(element, 0, delay);
        dismiss(element);
    }, delay);

    element.addEventListener('daisy:alert-dismiss', () => {
        window.clearInterval(interval);
        window.clearTimeout(timeout);
    }, { once: true });
}

/**
 * Daisy Kit - Alert dismiss
 *
 * Ferme une alerte au clic sur [data-alert-dismiss] ou après un délai
 * data-alert-auto-dismiss, sans handler inline (compatible CSP script-src
 * 'self' des applications hôtes).
 */
export default function init(element) {
    if (element.dataset.alertDismissInitialized === 'true') {
        return;
    }

    element.dataset.alertDismissInitialized = 'true';

    element.addEventListener('click', (event) => {
        const button = event.target.closest('[data-alert-dismiss]');

        if (! button || ! element.contains(button)) {
            return;
        }

        event.preventDefault();
        dismiss(element);
    });

    const delay = parseDelay(element.dataset.alertAutoDismiss);

    if (delay) {
        initAutoDismiss(element, delay);
    }
}

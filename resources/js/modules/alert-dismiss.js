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
    let remaining = delay;
    let startedAt = Date.now();
    let interval = null;
    let timeout = null;
    let dismissed = false;

    const clearTimers = () => {
        window.clearInterval(interval);
        window.clearTimeout(timeout);
        interval = null;
        timeout = null;
    };

    const finish = () => {
        clearTimers();
        updateProgress(element, 0, delay);
        dismiss(element);
    };

    const pause = () => {
        if (dismissed || ! interval || ! timeout) {
            return;
        }

        remaining -= Date.now() - startedAt;
        clearTimers();
        updateProgress(element, remaining, delay);
    };

    const schedule = () => {
        if (dismissed || remaining <= 0) {
            finish();

            return;
        }

        startedAt = Date.now();
        interval = window.setInterval(() => {
            if (! element.isConnected) {
                clearTimers();

                return;
            }

            const currentRemaining = remaining - (Date.now() - startedAt);

            updateProgress(element, currentRemaining, delay);

            if (currentRemaining <= 0) {
                finish();
            }
        }, Math.min(MIN_PROGRESS_INTERVAL, delay));

        timeout = window.setTimeout(finish, remaining);
    };

    const resume = () => {
        if (! element.isConnected) {
            return;
        }

        if (dismissed || interval || timeout) {
            return;
        }

        schedule();
    };

    element.addEventListener('daisy:alert-dismiss', () => {
        dismissed = true;
        clearTimers();
    }, { once: true });

    if (element.dataset.alertPauseOnHover === 'true') {
        element.addEventListener('pointerenter', pause);
        element.addEventListener('focusin', pause);
        element.addEventListener('pointerleave', resume);
        element.addEventListener('focusout', resume);
    }

    schedule();
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

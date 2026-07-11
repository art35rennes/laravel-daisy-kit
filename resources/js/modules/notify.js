import initAlertDismiss from './alert-dismiss.js';

const DEFAULT_AUTO_DISMISS = 5000;
const DEFAULT_LIMIT = 4;
const DEFAULT_POSITION = 'bottom-end';
const TYPES = new Set(['info', 'success', 'warning', 'error']);
const positionClasses = {
    'top-start': ['toast-top', 'toast-start'],
    'top-center': ['toast-top', 'toast-center'],
    'top-end': ['toast-top', 'toast-end'],
    'middle-start': ['toast-middle', 'toast-start'],
    'middle-center': ['toast-middle', 'toast-center'],
    'middle-end': ['toast-middle', 'toast-end'],
    'bottom-start': ['toast-bottom', 'toast-start'],
    'bottom-center': ['toast-bottom', 'toast-center'],
    'bottom-end': ['toast-bottom', 'toast-end'],
};

let notificationCounter = 0;
let globalsInstalled = false;

function normalizeType(type) {
    return TYPES.has(type) ? type : 'info';
}

function parsePositiveInteger(value, fallback = null) {
    const parsed = Number.parseInt(value, 10);

    return Number.isFinite(parsed) && parsed > 0 ? parsed : fallback;
}

function escapeSelectorValue(value) {
    if (typeof CSS !== 'undefined' && typeof CSS.escape === 'function') {
        return CSS.escape(value);
    }

    return String(value).replace(/["\\]/g, '\\$&');
}

function normalizePosition(options = {}) {
    if (positionClasses[options.position]) {
        return options.position;
    }

    const vertical = options.vertical || 'bottom';
    const horizontal = options.horizontal || options.position || 'end';
    const position = `${vertical}-${horizontal}`;

    return positionClasses[position] ? position : DEFAULT_POSITION;
}

function applyPosition(container, position) {
    Object.values(positionClasses).flat().forEach((className) => container.classList.remove(className));
    container.classList.add(...positionClasses[position]);
    container.dataset.notifyPosition = position;
}

function findContainer() {
    return document.querySelector('[data-daisy-notify-container]');
}

function createContainer(options = {}) {
    const container = document.createElement('div');
    container.className = 'toast';
    container.dataset.module = 'notify';
    container.dataset.daisyNotifyContainer = 'true';
    container.dataset.notifyLimit = String(parsePositiveInteger(options.limit, DEFAULT_LIMIT));
    applyPosition(container, normalizePosition(options));
    document.body.appendChild(container);

    return container;
}

function ensureContainer(options = {}) {
    const container = findContainer() || createContainer(options);

    if (options.limit) {
        container.dataset.notifyLimit = String(parsePositiveInteger(options.limit, DEFAULT_LIMIT));
    }

    if (options.position || options.vertical || options.horizontal) {
        applyPosition(container, normalizePosition(options));
    }

    return container;
}

function createTextElement(tagName, className, text) {
    if (! text) {
        return null;
    }

    const element = document.createElement(tagName);
    element.className = className;
    element.textContent = text;

    return element;
}

function appendContent(notification, options) {
    const content = document.createElement('div');
    content.className = 'min-w-0 flex-1';

    const title = createTextElement('h3', 'font-medium leading-5', options.title);
    if (title) {
        content.appendChild(title);
    }

    if (options.html) {
        const message = document.createElement('div');
        message.className = 'text-sm';
        message.innerHTML = options.html;
        content.appendChild(message);
    } else {
        const message = createTextElement('div', 'text-sm', options.message || options.text);
        if (message) {
            content.appendChild(message);
        }
    }

    notification.appendChild(content);
}

function appendLoadingIndicator(notification, options) {
    if (options.loading !== true) {
        return;
    }

    const spinner = document.createElement('span');
    spinner.className = 'loading loading-spinner loading-sm shrink-0';
    spinner.setAttribute('aria-hidden', 'true');
    notification.appendChild(spinner);
}

function actionButtonClass(action) {
    const variant = action.variant || 'ghost';

    return `btn btn-xs ${variant.startsWith('btn-') ? variant : `btn-${variant}`}`;
}

function appendActions(notification, options) {
    const actions = Array.isArray(options.actions) ? options.actions.slice(0, 2) : [];

    if (actions.length === 0) {
        return;
    }

    const actionsElement = document.createElement('div');
    actionsElement.className = 'flex flex-wrap items-center gap-2 justify-start sm:justify-end';

    actions.forEach((action, index) => {
        if (! action?.label) {
            return;
        }

        const button = document.createElement('button');
        button.type = 'button';
        button.className = actionButtonClass(action);
        button.textContent = action.label;
        button.addEventListener('click', () => {
            const detail = {
                id: notification.dataset.notifyId,
                action: action.name || action.value || index,
                notification,
                detail: action.detail || null,
            };

            if (typeof action.callback === 'function') {
                action.callback(detail);
            } else {
                notification.dispatchEvent(new CustomEvent(action.event || 'notify:action', {
                    bubbles: true,
                    detail,
                }));
            }

            if (action.dismiss !== false) {
                dismissNotification(notification);
            }
        });

        actionsElement.appendChild(button);
    });

    if (actionsElement.children.length > 0) {
        notification.appendChild(actionsElement);
    }
}

function appendDismissButton(notification, options) {
    if (options.dismissible === false) {
        return;
    }

    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'btn btn-ghost btn-xs btn-square ms-2';
    button.setAttribute('aria-label', options.closeLabel || 'Close notification');
    button.dataset.alertDismiss = 'true';
    button.textContent = '\u00d7';
    notification.appendChild(button);
}

function appendProgress(notification, type, delay) {
    if (! delay) {
        return;
    }

    const progress = document.createElement('progress');
    progress.className = `progress progress-${type} absolute inset-x-0 bottom-0 h-1 w-full rounded-none`;
    progress.max = 100;
    progress.value = 100;
    progress.setAttribute('aria-hidden', 'true');
    progress.dataset.alertProgress = 'true';
    notification.appendChild(progress);
}

function createNotification(options = {}) {
    const type = normalizeType(options.type);
    const delay = options.autoDismiss === false
        ? null
        : parsePositiveInteger(options.autoDismissMs ?? options.autoDismissAfter ?? options.autoDismiss, DEFAULT_AUTO_DISMISS);
    const notification = document.createElement('div');

    notification.className = `alert alert-${type} relative overflow-hidden items-start gap-3`;
    notification.role = ['error', 'warning'].includes(type) ? 'alert' : 'status';
    notification.dataset.module = 'alert-dismiss';
    notification.dataset.notifyId = options.id || `notify-${Date.now()}-${notificationCounter += 1}`;
    notification.dataset.alertPauseOnHover = options.pauseOnHover === false ? 'false' : 'true';

    if (delay) {
        notification.dataset.alertAutoDismiss = String(delay);
    }

    appendLoadingIndicator(notification, options);
    appendContent(notification, options);
    appendActions(notification, options);
    appendDismissButton(notification, options);
    appendProgress(notification, type, delay);

    notification.addEventListener('daisy:alert-dismiss', () => {
        notification.dispatchEvent(new CustomEvent('daisy:notify-dismiss', {
            bubbles: true,
            detail: { id: notification.dataset.notifyId, notification },
        }));
    }, { once: true });

    return notification;
}

function enforceLimit(container) {
    const limit = parsePositiveInteger(container.dataset.notifyLimit, DEFAULT_LIMIT);

    while (container.querySelectorAll('[data-notify-id]').length > limit) {
        dismissNotification(container.querySelector('[data-notify-id]'));
    }
}

export function notify(options = {}) {
    const container = ensureContainer(options);
    const notification = createNotification(options);

    container.appendChild(notification);
    initAlertDismiss(notification);
    enforceLimit(container);

    return notification;
}

export function dismissNotification(notificationOrId) {
    const notification = typeof notificationOrId === 'string'
        ? document.querySelector(`[data-notify-id="${escapeSelectorValue(notificationOrId)}"]`)
        : notificationOrId;

    if (! notification?.isConnected) {
        return false;
    }

    notification.dispatchEvent(new CustomEvent('daisy:alert-dismiss', {
        bubbles: true,
    }));
    notification.remove();

    return true;
}

export function clearNotifications() {
    findContainer()?.querySelectorAll('[data-notify-id]').forEach(dismissNotification);
}

export function installNotifyGlobals() {
    if (globalsInstalled || typeof window === 'undefined') {
        return;
    }

    globalsInstalled = true;
    window.DaisyKit = {
        ...(window.DaisyKit || {}),
        notify,
        dismissNotification,
        clearNotifications,
    };

    document.addEventListener('daisy:notify', (event) => {
        notify(event.detail || {});
    });
}

export default function init(element, options = {}) {
    element.dataset.daisyNotifyContainer = 'true';
    element.dataset.notifyLimit = String(parsePositiveInteger(options.notifyLimit || element.dataset.notifyLimit, DEFAULT_LIMIT));
    applyPosition(element, normalizePosition({
        position: options.notifyPosition || element.dataset.notifyPosition,
        horizontal: options.notifyHorizontal || element.dataset.notifyHorizontal,
        vertical: options.notifyVertical || element.dataset.notifyVertical,
    }));
    installNotifyGlobals();
}

installNotifyGlobals();

import { resolveToolIcon } from './config.js';

function createToolbar(root) {
    const toolbar = document.createElement('div');
    toolbar.className = [
        'daisy-leaflet-draw-toolbar',
        'absolute',
        'left-2',
        'top-20',
        'z-[1000]',
        'join',
        'join-vertical',
        'bg-base-100',
        'shadow',
    ].join(' ');
    toolbar.setAttribute('role', 'toolbar');
    toolbar.setAttribute('aria-label', 'Outils de carte');
    ['click', 'contextmenu', 'dblclick', 'pointerdown', 'pointerup'].forEach(eventName => {
        toolbar.addEventListener(eventName, event => {
            event.preventDefault();
            event.stopPropagation();
        });
    });

    root.appendChild(toolbar);

    return toolbar;
}

function createToolTooltip(label) {
    const tooltipWrapper = document.createElement('span');

    tooltipWrapper.className = [
        'pointer-events-none',
        'absolute',
        'left-full',
        'top-1/2',
        'z-[1200]',
        'ml-2',
        'hidden',
        '-translate-y-1/2',
        'whitespace-nowrap',
        'rounded-box',
        'bg-neutral',
        'px-2',
        'py-1',
        'text-xs',
        'font-medium',
        'text-neutral-content',
        'shadow',
        'group-hover:inline-flex',
        'group-focus-visible:inline-flex',
    ].join(' ');
    tooltipWrapper.setAttribute('aria-hidden', 'true');
    tooltipWrapper.textContent = label;

    return tooltipWrapper;
}

function createToolButton(toolbar, definition) {
    const { action, label, mode } = definition;
    const button = document.createElement('button');
    const iconWrapper = document.createElement('span');
    const labelWrapper = document.createElement('span');
    const tooltipWrapper = createToolTooltip(label);

    button.type = 'button';
    button.className = definition.className || 'btn btn-xs btn-square join-item group relative';
    button.dataset.action = action || mode;
    button.dataset.mode = mode;
    button.setAttribute('aria-label', label);

    iconWrapper.className = 'inline-flex size-4';
    iconWrapper.setAttribute('aria-hidden', 'true');
    iconWrapper.innerHTML = resolveToolIcon(definition);

    labelWrapper.className = 'sr-only';
    labelWrapper.textContent = label;

    button.append(iconWrapper, labelWrapper, tooltipWrapper);
    toolbar.appendChild(button);

    return button;
}

function closeSiblingToolMenus(toolbar, exceptWrapper = null) {
    toolbar.querySelectorAll('[data-leaflet-tool-menu]').forEach(wrapper => {
        if (wrapper === exceptWrapper) {
            return;
        }

        wrapper.__daisyCloseToolMenu?.();
    });
}

function createToolMenu(toolbar, group) {
    const wrapper = document.createElement('div');
    const button = document.createElement('button');
    const iconWrapper = document.createElement('span');
    const labelWrapper = document.createElement('span');
    const tooltipWrapper = createToolTooltip(group.label);
    const panel = document.createElement('div');
    const buttons = [];
    let isOpen = false;

    wrapper.className = 'join-item relative flex';
    wrapper.dataset.leafletToolMenu = group.action;
    button.type = 'button';
    button.className = 'btn btn-xs btn-square group relative';
    button.dataset.action = group.action;
    button.setAttribute('aria-label', group.label);
    button.setAttribute('aria-haspopup', 'menu');
    button.setAttribute('aria-expanded', 'false');

    iconWrapper.className = 'inline-flex size-4';
    iconWrapper.setAttribute('aria-hidden', 'true');
    iconWrapper.innerHTML = resolveToolIcon(group);

    labelWrapper.className = 'sr-only';
    labelWrapper.textContent = group.label;

    panel.className = [
        'absolute',
        'left-full',
        'top-0',
        'z-[1200]',
        'ml-1',
        'hidden',
        'max-h-80',
        'max-w-[calc(100vw-5rem)]',
        'min-w-max',
        'flex-col',
        'overflow-x-auto',
        'overflow-y-auto',
        'overscroll-contain',
        'rounded-box',
        'bg-base-100',
        'p-1',
        'shadow-lg',
        'ring-1',
        'ring-base-300',
    ].join(' ');
    panel.setAttribute('role', 'menu');

    const closeOnOutsidePointer = event => {
        if (!wrapper.contains(event.target)) {
            setOpen(false);
        }
    };
    const closeOnEscape = event => {
        if (event.key === 'Escape') {
            setOpen(false);
            button.focus();
        }
    };
    function setOpen(nextOpen) {
        if (isOpen === nextOpen) {
            return;
        }

        if (nextOpen) {
            closeSiblingToolMenus(toolbar, wrapper);
        }

        isOpen = nextOpen;
        wrapper.dataset.open = nextOpen ? 'true' : 'false';
        button.setAttribute('aria-expanded', nextOpen ? 'true' : 'false');
        button.classList.toggle('btn-active', nextOpen);
        panel.classList.toggle('hidden', !nextOpen);
        panel.classList.toggle('flex', nextOpen);

        const method = nextOpen ? 'addEventListener' : 'removeEventListener';
        document[method]('pointerdown', closeOnOutsidePointer);
        document[method]('mousedown', closeOnOutsidePointer);
        document[method]('touchstart', closeOnOutsidePointer);
        document[method]('keydown', closeOnEscape);
    }
    wrapper.__daisyCloseToolMenu = () => setOpen(false);

    button.addEventListener('click', event => {
        event.preventDefault();
        event.stopPropagation();
        setOpen(!isOpen);
    });
    button.addEventListener('keydown', event => {
        if (event.key === 'ArrowDown' || event.key === 'ArrowRight') {
            event.preventDefault();
            setOpen(true);
            buttons[0]?.focus();
        }
    });
    panel.addEventListener('click', event => {
        if (event.target.closest('button')) {
            setOpen(false);
        }
    });

    button.append(iconWrapper, labelWrapper, tooltipWrapper);
    wrapper.append(button, panel);
    toolbar.appendChild(wrapper);

    group.items.forEach(item => {
        const childButton = createToolButton(panel, {
            ...item,
            className: 'btn btn-xs btn-square group relative',
        });

        childButton.setAttribute('role', 'menuitem');
        childButton.addEventListener('click', () => setOpen(false));
        buttons.push(childButton);
    });

    return { button, panel, buttons };
}

function createActionBadge(root, config, onClear) {
    if (!config?.enabled) {
        return {
            show: () => {},
            hide: () => {},
            element: null,
        };
    }

    const element = document.createElement('div');
    const label = document.createElement('span');
    const clearButton = document.createElement('button');

    element.className = [
        'pointer-events-auto',
        'absolute',
        'bottom-3',
        'left-1/2',
        'z-[1000]',
        'hidden',
        'max-w-[calc(100%-2rem)]',
        '-translate-x-1/2',
        'items-center',
        'gap-2',
        'rounded-box',
        'bg-base-100/95',
        'px-3',
        'py-2',
        'text-xs',
        'font-semibold',
        'shadow-lg',
        'ring-1',
        'ring-base-300',
    ].join(' ');
    element.setAttribute('role', 'status');
    element.setAttribute('aria-live', 'polite');

    label.className = 'truncate';

    clearButton.type = 'button';
    clearButton.className = 'btn btn-ghost btn-xs btn-square';
    clearButton.setAttribute('aria-label', 'Revenir à la sélection');
    clearButton.textContent = '×';
    clearButton.addEventListener('click', event => {
        event.preventDefault();
        event.stopPropagation();
        onClear();
    });

    element.append(label, clearButton);
    root.appendChild(element);

    return {
        show: actionLabel => {
            label.textContent = `${config.label} : ${actionLabel}`;
            element.classList.remove('hidden');
            element.classList.add('inline-flex');
        },
        hide: () => {
            label.textContent = '';
            element.classList.add('hidden');
            element.classList.remove('inline-flex');
        },
        element,
    };
}

function setActiveButton(buttons, activeMode) {
    buttons.forEach(button => {
        button.classList.toggle('btn-active', button.dataset.action === activeMode);
    });
}

export {
    closeSiblingToolMenus,
    createActionBadge,
    createToolButton,
    createToolMenu,
    createToolTooltip,
    createToolbar,
    setActiveButton,
};

import { applyFitBounds } from './core.js';
import { addLayer, removeLayer } from './layers.js';

const CONTROL_ICONS = {
    fit: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 3H5a2 2 0 0 0-2 2v3"/><path d="M21 8V5a2 2 0 0 0-2-2h-3"/><path d="M3 16v3a2 2 0 0 0 2 2h3"/><path d="M16 21h3a2 2 0 0 0 2-2v-3"/></svg>',
    layers: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m12 3 9 5-9 5-9-5 9-5Z"/><path d="m3 12 9 5 9-5"/><path d="m3 16 9 5 9-5"/></svg>',
    settings: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z"/><path d="M19.4 15a1.8 1.8 0 0 0 .36 1.98l.04.04a2 2 0 1 1-2.83 2.83l-.04-.04a1.8 1.8 0 0 0-1.98-.36 1.8 1.8 0 0 0-1.1 1.65V21a2 2 0 1 1-4 0v-.06a1.8 1.8 0 0 0-1.1-1.65 1.8 1.8 0 0 0-1.98.36l-.04.04a2 2 0 1 1-2.83-2.83l.04-.04A1.8 1.8 0 0 0 4.6 15a1.8 1.8 0 0 0-1.65-1.1H3a2 2 0 1 1 0-4h.06A1.8 1.8 0 0 0 4.7 8.8a1.8 1.8 0 0 0-.36-1.98l-.04-.04a2 2 0 1 1 2.83-2.83l.04.04a1.8 1.8 0 0 0 1.98.36h.01A1.8 1.8 0 0 0 10.25 2.7V2.6a2 2 0 1 1 4 0v.06a1.8 1.8 0 0 0 1.1 1.65 1.8 1.8 0 0 0 1.98-.36l.04-.04a2 2 0 1 1 2.83 2.83l-.04.04a1.8 1.8 0 0 0-.36 1.98v.01a1.8 1.8 0 0 0 1.65 1.1H21a2 2 0 1 1 0 4h-.06A1.8 1.8 0 0 0 19.4 15Z"/></svg>',
};

const DEFAULT_CONTROLS_CONFIG = {
    position: 'topright',
    basemaps: true,
    overlays: true,
    draw: true,
    measurements: true,
    fitBounds: true,
    persist: false,
    storageKey: null,
};

function normalizeControlsConfig(controls) {
    if (!controls) {
        return false;
    }

    return {
        ...DEFAULT_CONTROLS_CONFIG,
        ...(controls === true ? {} : controls),
    };
}

function createDefaultControlsState() {
    return {
        basemap: null,
        drawToolbar: true,
        measurements: true,
        overlays: {},
    };
}

function getControlsStorageKey(root, cfg, controlsConfig) {
    if (!controlsConfig?.persist) {
        return null;
    }

    if (controlsConfig.storageKey) {
        return controlsConfig.storageKey;
    }

    return `daisy:leaflet:${root.id || cfg.containerId}:controls`;
}

function loadControlsState(storageKey) {
    if (!storageKey) {
        return createDefaultControlsState();
    }

    try {
        const stored = JSON.parse(window.localStorage.getItem(storageKey) || '{}');

        return {
            ...createDefaultControlsState(),
            ...stored,
            overlays: {
                ...createDefaultControlsState().overlays,
                ...(stored.overlays || {}),
            },
        };
    } catch {
        return createDefaultControlsState();
    }
}

function persistControlsState(storageKey, state) {
    if (!storageKey) {
        return;
    }

    try {
        window.localStorage.setItem(storageKey, JSON.stringify(state));
    } catch {
        // Storage can be unavailable in private or locked-down contexts.
    }
}

function dispatchControlsChange(root, map, cfg, context, controlsState) {
    context.controlsState = controlsState;
    cfg.controlsState = controlsState;

    root.dispatchEvent(new CustomEvent('daisy:leaflet:controls-change', {
        detail: {
            map,
            config: cfg,
            state: controlsState,
        },
    }));
}

function getControlsPositionClasses(position, hasLayerControl = false) {
    const positions = {
        bottomleft: ['left-2', 'bottom-12', 'items-start'],
        bottomright: ['right-2', 'bottom-12', 'items-end'],
        topleft: ['left-12', 'top-2', 'items-start'],
        topright: ['right-2', hasLayerControl ? 'top-28' : 'top-14', 'items-end'],
    };

    return positions[position] || positions.topright;
}

function createIconButton(parent, icon, label, className = 'btn btn-xs btn-square') {
    const button = document.createElement('button');
    const iconWrapper = document.createElement('span');
    const labelWrapper = document.createElement('span');

    button.type = 'button';
    button.className = className;
    button.title = label;
    button.setAttribute('aria-label', label);

    iconWrapper.className = 'inline-flex size-4';
    iconWrapper.setAttribute('aria-hidden', 'true');
    iconWrapper.innerHTML = CONTROL_ICONS[icon] || CONTROL_ICONS.settings;

    labelWrapper.className = 'sr-only';
    labelWrapper.textContent = label;

    button.append(iconWrapper, labelWrapper);
    parent.appendChild(button);

    return button;
}

function createControlsSection(parent, title) {
    const section = document.createElement('div');
    const heading = document.createElement('div');

    section.className = 'space-y-1';
    heading.className = 'text-[0.7rem] font-semibold uppercase tracking-wide text-base-content/60';
    heading.textContent = title;

    section.appendChild(heading);
    parent.appendChild(section);

    return section;
}

function createControlChoice(parent, options) {
    const label = document.createElement('label');
    const input = document.createElement('input');
    const text = document.createElement('span');

    label.className = 'flex min-h-7 cursor-pointer items-center gap-2 rounded px-1 text-xs hover:bg-base-200';
    input.type = options.type;
    input.name = options.name || '';
    input.value = options.value || '';
    input.checked = Boolean(options.checked);
    input.className = options.type === 'radio' ? 'radio radio-xs' : 'checkbox checkbox-xs';
    text.textContent = options.label;

    label.append(input, text);
    parent.appendChild(label);

    if (typeof options.onChange === 'function') {
        input.addEventListener('change', () => options.onChange(input));
    }

    return label;
}

function activateBaseLayer(map, baseLayers, selectedName) {
    Object.entries(baseLayers).forEach(([name, layer]) => {
        if (name === selectedName) {
            addLayer(map, layer);
        } else {
            removeLayer(map, layer);
        }
    });
}

function applyControlsState(root, map, cfg, context, controlsState) {
    if (controlsState.basemap && context.baseLayers?.[controlsState.basemap]) {
        activateBaseLayer(map, context.baseLayers, controlsState.basemap);
    }

    Object.entries(controlsState.overlays || {}).forEach(([name, visible]) => {
        const layer = context.overlayLayers?.[name];

        if (!layer) {
            return;
        }

        if (visible) {
            addLayer(map, layer);
        } else {
            removeLayer(map, layer);
        }
    });

    const toolbar = root.querySelector('.daisy-leaflet-draw-toolbar');
    if (toolbar) {
        toolbar.classList.toggle('hidden', controlsState.drawToolbar === false);
    }

    dispatchControlsChange(root, map, cfg, context, controlsState);
}

function addUserControls(L, map, cfg, context) {
    const controlsConfig = context.controlsConfig;

    if (!controlsConfig) {
        return null;
    }

    const root = context.root;
    const storageKey = context.controlsStorageKey;
    const state = context.controlsState || createDefaultControlsState();
    const wrapper = document.createElement('div');
    const panel = document.createElement('div');
    const positionClasses = getControlsPositionClasses(controlsConfig.position, Boolean(context.layerMenuControl));

    wrapper.className = [
        'daisy-leaflet-controls',
        'absolute',
        'z-[1100]',
        'flex',
        'flex-col',
        'gap-2',
        ...positionClasses,
    ].join(' ');

    const trigger = createIconButton(wrapper, 'settings', 'Réglages de carte');
    trigger.classList.add('bg-base-100', 'shadow');
    trigger.setAttribute('aria-expanded', 'false');

    panel.className = [
        'hidden',
        'w-64',
        'max-w-[calc(100vw-2rem)]',
        'space-y-3',
        'rounded-box',
        'bg-base-100',
        'p-3',
        'text-base-content',
        'shadow-lg',
        'ring-1',
        'ring-base-300',
    ].join(' ');

    const title = document.createElement('div');
    title.className = 'text-sm font-semibold';
    title.textContent = 'Réglages de carte';
    panel.appendChild(title);

    trigger.addEventListener('click', () => {
        const open = panel.classList.toggle('hidden') === false;
        trigger.setAttribute('aria-expanded', String(open));
    });

    const updateState = (patch) => {
        Object.assign(state, patch);
        persistControlsState(storageKey, state);
        applyControlsState(root, map, cfg, context, state);
    };

    if ((controlsConfig.draw && cfg.draw) || (controlsConfig.measurements && cfg.measure)) {
        const section = createControlsSection(panel, 'Outils');

        if (controlsConfig.draw && cfg.draw) {
            createControlChoice(section, {
                type: 'checkbox',
                label: 'Afficher les outils de dessin',
                checked: state.drawToolbar !== false,
                onChange(input) {
                    updateState({ drawToolbar: input.checked });
                },
            });
        }

        if (controlsConfig.measurements && cfg.measure) {
            createControlChoice(section, {
                type: 'checkbox',
                label: 'Afficher les mesures',
                checked: state.measurements !== false,
                onChange(input) {
                    updateState({ measurements: input.checked });
                },
            });
        }
    }

    if (controlsConfig.fitBounds) {
        const section = createControlsSection(panel, 'Vue');
        const button = createIconButton(section, 'fit', 'Voir les objets', 'btn btn-xs w-full justify-start gap-2');
        const buttonText = document.createElement('span');

        buttonText.textContent = 'Voir les objets';
        button.appendChild(buttonText);
        button.addEventListener('click', () => {
            const fitLayers = [context.geojsonLayer, ...(context.renderedOverlayLayers || [])].filter(Boolean);
            const fitCollections = [cfg.value, ...(context.editableCollections || [])].filter(Boolean);

            applyFitBounds(L, map, { fitBounds: true }, context.markers || [], fitLayers, fitCollections);
        });
    }

    wrapper.appendChild(panel);
    root.appendChild(wrapper);

    applyControlsState(root, map, cfg, context, state);

    return wrapper;
}

export {
    addUserControls,
    applyControlsState,
    createControlChoice,
    createControlsSection,
    createDefaultControlsState,
    createIconButton,
    getControlsPositionClasses,
    getControlsStorageKey,
    loadControlsState,
    normalizeControlsConfig,
    persistControlsState,
};

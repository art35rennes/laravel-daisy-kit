import { getControlType } from './nodes.js';

export function setDetailsOpen(root, open) {
  const panel = root.querySelector('[data-blueprint-details-panel]');

  panel?.classList.toggle('hidden', !open);
  panel?.classList.toggle('modal-open', open && panel.classList.contains('modal'));
  root.querySelector('[data-blueprint-details-backdrop]')?.classList.toggle('hidden', !open);
}

export function renderProperties(root, node, i18n, readonly = false, draftData = null, validation = null) {
  const target = root.querySelector('[data-blueprint-properties]');

  if (!target) {
    return;
  }

  replaceChildren(target);

  if (!node) {
    target.append(createElement('p', {
      className: 'text-sm text-base-content/70',
      text: i18n.noSelection,
    }));
    setDetailsOpen(root, false);
    return;
  }

  const controls = getNodeControls(node);
  const values = draftData || node.__blueprint?.data || {};
  const wrapper = createElement('div', { className: 'grid gap-3' });
  const header = createElement('div', { className: 'flex items-start justify-between gap-3' });
  const titleWrap = createElement('div', { className: 'min-w-0' });

  titleWrap.append(
    createElement('p', { className: 'text-sm font-semibold', text: node.label }),
    createElement('p', { className: 'text-xs text-base-content/60', text: node.__blueprint?.type || 'node' }),
  );
  header.append(
    titleWrap,
    createElement('span', {
      className: 'badge badge-outline max-w-32 truncate',
      text: node.__blueprint?.theme || node.id,
    }),
  );
  wrapper.append(header);

  if (node.__blueprint?.description) {
    wrapper.append(createElement('p', {
      className: 'text-sm text-base-content/70',
      text: node.__blueprint.description,
    }));
  }

  if (validation) {
    wrapper.append(renderBlueprintFeedback(validation, i18n));
  }

  const controlsWrap = createElement('div', { className: 'grid gap-3' });

  if (controls.length) {
    controlsWrap.append(renderControlSections(controls, node.id, values, readonly));
  } else {
    controlsWrap.append(createElement('p', {
      className: 'rounded-box border border-dashed border-base-300 bg-base-200/60 p-3 text-sm text-base-content/70',
      text: i18n.noProperties,
    }));
  }

  wrapper.append(controlsWrap);

  if (!readonly) {
    const actions = createElement('div', { className: 'flex flex-wrap items-center justify-between gap-2 pt-1' });

    actions.append(
      createElement('button', {
        className: 'btn btn-primary btn-sm',
        dataset: { blueprintApplyNode: '' },
        text: i18n.applyNode || 'Apply',
        type: 'button',
      }),
      createElement('button', {
        className: 'btn btn-error btn-outline btn-xs',
        dataset: { blueprintDeleteNode: '' },
        text: i18n.deleteNode || 'Delete',
        type: 'button',
      }),
    );
    wrapper.append(actions);
  }

  target.append(wrapper);
  setDetailsOpen(root, true);
  initLazyEditors(target);
}

export function activateDetailsTab(root, tabName) {
  root.querySelectorAll('[data-blueprint-details-tab]').forEach((tab) => {
    const active = tab.dataset.blueprintDetailsTab === tabName;

    tab.classList.toggle('tab-active', active);
    tab.setAttribute('aria-selected', active ? 'true' : 'false');
  });
  root.querySelectorAll('[data-blueprint-details-section]').forEach((section) => {
    section.classList.toggle('hidden', section.dataset.blueprintDetailsSection !== tabName);
  });
}

export function getNodeControls(node) {
  return node.__blueprint?.controls?.length
    ? node.__blueprint.controls
    : Object.entries(node.__blueprint?.data || {}).map(([key, value]) => ({
      key,
      label: key,
      type: getControlType(value),
    }));
}

export function readPropertyInputValue(input) {
  if (input.classList?.contains('code-editor')) {
    return input.querySelector('textarea[data-sync]')?.value || '';
  }

  if (input.classList?.contains('trix-wrapper')) {
    return input.querySelector('input[type="hidden"]')?.value || '';
  }

  if (input.type === 'checkbox') {
    return input.checked;
  }

  if (input.tagName === 'SELECT' && input.multiple) {
    return [...input.selectedOptions].map((option) => option.value);
  }

  if (input.type === 'number' || input.type === 'range') {
    const value = Number(input.value);

    return Number.isFinite(value) ? value : input.value;
  }

  return input.value;
}

export function collectPropertyInputData(root, draftData = {}) {
  const nextData = { ...draftData };

  root.querySelectorAll('[data-blueprint-property-input]').forEach((input) => {
    nextData[input.dataset.blueprintPropertyInput] = readPropertyInputValue(input);
  });

  return nextData;
}

export function applyNodeData(node, data) {
  node.__blueprint.data = { ...data };
}

function renderBlueprintFeedback(validation, i18n) {
  if (validation.valid) {
    const alert = createElement('div', {
      className: 'alert alert-success py-2 text-sm',
      dataset: { blueprintDetailsFeedback: '' },
    });

    alert.append(createElement('span', { text: i18n.applySuccess || 'Changes applied.' }));

    return alert;
  }

  const alert = createElement('div', {
    className: 'alert alert-error grid gap-2 py-2 text-sm',
    dataset: { blueprintDetailsFeedback: '' },
  });
  const details = createElement('details', { className: 'collapse collapse-arrow rounded-box bg-error-content/10' });
  const content = createElement('div', { className: 'collapse-content grid gap-2 px-3 pb-3' });
  const textarea = createElement('textarea', {
    className: 'textarea textarea-bordered textarea-xs min-h-24 font-mono text-xs',
    dataset: { blueprintErrorDetails: '' },
    readonly: true,
  });

  textarea.value = JSON.stringify(validation.errors, null, 2);
  content.append(
    textarea,
    createElement('button', {
      className: 'btn btn-xs justify-self-start',
      dataset: { blueprintCopyError: '' },
      text: i18n.copyError || 'Copy details',
      type: 'button',
    }),
  );
  details.append(
    createElement('summary', {
      className: 'collapse-title min-h-0 px-3 py-2 text-xs font-semibold',
      text: i18n.errorDetails || 'Details',
    }),
    content,
  );
  alert.append(
    createElement('span', { text: i18n.applyError || 'Some fields are invalid.' }),
    details,
  );

  return alert;
}

function renderControlSections(controls, nodeId, values, readonly) {
  const sections = groupControlsBySection(controls);

  if (sections.length <= 1) {
    const single = createElement('div', { className: 'grid gap-3' });

    sections[0].controls.forEach((control) => single.append(renderBlueprintControl(control, nodeId, values, readonly)));

    return single;
  }

  const wrapper = createElement('div', { className: 'grid gap-4' });
  const tabs = createElement('div', {
    className: 'tabs tabs-border overflow-x-auto',
    role: 'tablist',
  });
  const sectionsWrap = createElement('div', { className: 'grid gap-4' });

  sections.forEach((section, index) => {
    const active = index === 0;

    tabs.append(createElement('button', {
      className: `tab ${active ? 'tab-active' : ''}`,
      dataset: { blueprintDetailsTab: section.key },
      role: 'tab',
      type: 'button',
      text: section.label,
    }));

    const content = createElement('section', {
      className: active ? 'grid gap-3' : 'hidden grid gap-3',
      dataset: { blueprintDetailsSection: section.key },
      role: 'tabpanel',
    });

    section.controls.forEach((control) => content.append(renderBlueprintControl(control, nodeId, values, readonly)));
    sectionsWrap.append(content);
  });

  wrapper.append(tabs, sectionsWrap);

  return wrapper;
}

function groupControlsBySection(controls) {
  const groups = [];
  const byKey = new Map();

  controls.forEach((control) => {
    const section = normalizeControlSection(control);

    if (!byKey.has(section.key)) {
      byKey.set(section.key, { ...section, controls: [] });
      groups.push(byKey.get(section.key));
    }

    byKey.get(section.key).controls.push(control);
  });

  return groups;
}

function normalizeControlSection(control) {
  const explicit = control.section || control.group || control.tab;

  if (explicit) {
    const label = String(explicit);

    return { key: slugify(label), label };
  }

  const key = String(control.key || '');

  if (['eligibility_rules', 'eligibility_rules_description', 'recommendation'].includes(key) || key.includes('rule')) {
    return { key: 'rules', label: 'Règles' };
  }

  if (['forwardable', 'backwardable'].includes(key) || key.includes('transition')) {
    return { key: 'transitions', label: 'Transitions' };
  }

  if (key.includes('availability') || key === 'readonly') {
    return { key: 'permissions', label: 'Droits' };
  }

  if (key.includes('action') || key.includes('shortcut') || key.includes('generate')) {
    return { key: 'actions', label: 'Actions' };
  }

  return { key: 'general', label: 'Général' };
}

function slugify(value) {
  return String(value)
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-|-$/g, '') || 'section';
}

function renderBlueprintControl(control, nodeId, data, readonly) {
  const key = control.key;
  const value = data[key] ?? control.default ?? '';

  if (control.type === 'textarea') {
    const input = createElement('textarea', {
      className: 'textarea textarea-bordered textarea-sm min-h-20 w-full',
      dataset: { blueprintPropertyInput: key },
      disabled: readonly,
      placeholder: control.placeholder || '',
      required: control.required,
    });

    input.value = String(value ?? '');
    applyTextControlAttributes(input, control);

    return wrapFormControl(control, key, input);
  }

  if (control.type === 'code-editor') {
    return renderCodeEditorControl(control, key, value, readonly);
  }

  if (control.type === 'wysiwyg') {
    return renderWysiwygControl(control, key, value, readonly);
  }

  if (control.type === 'select') {
    const input = createElement('select', {
      className: 'select select-bordered select-sm w-full',
      dataset: { blueprintPropertyInput: key },
      disabled: readonly,
      required: control.required,
    });

    control.options.forEach((option) => {
      input.append(createElement('option', {
        text: option.label,
        value: option.value,
        selected: String(value) === String(option.value),
      }));
    });

    return wrapFormControl(control, key, input);
  }

  if (control.type === 'multiselect') {
    const selectedValues = Array.isArray(value)
      ? value.map((item) => String(item))
      : String(value ?? '').split(',').map((item) => item.trim()).filter(Boolean);
    const input = createElement('select', {
      className: 'select select-bordered select-sm min-h-28 w-full',
      dataset: { blueprintPropertyInput: key },
      disabled: readonly,
      multiple: true,
      required: control.required,
      size: Math.min(Math.max(control.options.length, 3), Number(control.size || 8)),
    });

    control.options.forEach((option) => {
      input.append(createElement('option', {
        text: option.label,
        value: option.value,
        selected: selectedValues.includes(String(option.value)),
      }));
    });

    return wrapFormControl(control, key, input);
  }

  if (control.type === 'radio') {
    const fieldset = createElement('fieldset', {
      className: 'form-control grid w-full gap-2',
      dataset: { blueprintProperty: key },
    });

    fieldset.append(createElement('legend', {
      className: 'label-text text-xs font-medium text-base-content/70',
      text: control.label || key,
    }));

    const options = createElement('div', { className: 'grid gap-1' });

    control.options.forEach((option) => {
      const input = createElement('input', {
        checked: String(value) === String(option.value),
        className: 'radio radio-sm',
        dataset: { blueprintPropertyInput: key },
        disabled: readonly,
        name: `${nodeId}-${key}`,
        required: control.required,
        type: 'radio',
        value: option.value,
      });

      options.append(createElement('label', { className: 'flex items-center gap-2 text-sm' }, [
        input,
        createElement('span', { text: option.label }),
      ]));
    });
    fieldset.append(options);
    appendHelp(fieldset, control);

    return fieldset;
  }

  if (control.type === 'checkbox') {
    const input = createElement('input', {
      checked: Boolean(value),
      className: 'checkbox checkbox-sm mt-0.5',
      dataset: { blueprintPropertyInput: key },
      disabled: readonly,
      required: control.required,
      type: 'checkbox',
    });
    const text = createElement('span', { className: 'grid gap-0.5' });

    text.append(createLabelText(control, key));
    appendHelp(text, control);

    return createElement('label', {
      className: 'flex items-start gap-2',
      dataset: { blueprintProperty: key },
    }, [input, text]);
  }

  const nativeType = control.type === 'range'
    ? 'range'
    : (['color', 'date', 'datetime-local', 'email', 'hidden', 'month', 'number', 'password', 'tel', 'text', 'time', 'url', 'week'].includes(control.type)
      ? control.type
      : 'text');
  const input = createElement('input', {
    className: nativeType === 'range' ? 'range range-sm' : 'input input-sm input-bordered w-full',
    dataset: { blueprintPropertyInput: key },
    disabled: readonly,
    placeholder: control.placeholder || '',
    required: control.required,
    type: nativeType,
    value: String(value ?? ''),
  });

  applyNumberControlAttributes(input, control);
  applyTextControlAttributes(input, control);

  return wrapFormControl(control, key, input);
}

function renderCodeEditorControl(control, key, value, readonly) {
  const editorId = `blueprint-code-${key}-${Math.random().toString(36).slice(2)}`;
  const wrapper = createElement('div', {
    className: 'code-editor bg-base-100 card-border rounded-box overflow-hidden',
    dataset: {
      blueprintPropertyInput: key,
      module: 'code-editor',
      language: control.language || 'javascript',
      readonly: readonly ? 'true' : 'false',
      tabSize: 2,
    },
  });
  const toolbar = createElement('div', { className: 'flex items-center justify-between gap-2 border-b bg-base-200 px-2 py-1' });
  const actions = createElement('div', { className: 'flex items-center gap-1' });

  toolbar.append(
    createElement('div', { className: 'text-xs opacity-70', text: String(control.language || 'javascript').toUpperCase() }),
    actions,
  );
  actions.append(
    createElement('button', { className: 'btn btn-xs', dataset: { action: 'fold-all' }, text: 'Tout plier', type: 'button' }),
    createElement('button', { className: 'btn btn-xs', dataset: { action: 'unfold-all' }, text: 'Tout déplier', type: 'button' }),
    createElement('button', { className: 'btn btn-xs', dataset: { action: 'format' }, text: 'Formater', type: 'button' }),
    createElement('button', { className: 'btn btn-xs', dataset: { action: 'copy' }, text: 'Copier', type: 'button' }),
  );
  const host = createElement('div', { className: 'cm-host daisy-code-editor-height-px-180' });

  if (control.height) {
    host.style.height = control.height;
  }

  wrapper.append(
    toolbar,
    host,
    createElement('textarea', { className: 'hidden', dataset: { sync: '' } }),
    createJsonScript('options', {}),
    createJsonScript('initial', { value: String(value ?? '') }),
    createJsonScript('i18n', {}),
  );
  wrapper.id = editorId;
  wrapper.querySelector('textarea[data-sync]').value = String(value ?? '');

  return wrapFormControl(control, key, wrapper);
}

function renderWysiwygControl(control, key, value, readonly) {
  const inputId = `blueprint-trix-${key}-${Math.random().toString(36).slice(2)}`;
  const wrapper = createElement('div', {
    className: 'trix-wrapper daisy-blueprint-wysiwyg',
    dataset: {
      blueprintPropertyInput: key,
      module: 'lazy-editors',
      trixAttachments: '0',
    },
  });
  const container = createElement('div', { dataset: { trixContainer: '' } });
  const toolbar = createElement('trix-toolbar');
  const input = createElement('input', { type: 'hidden', value: String(value ?? '') });
  const editor = createElement('trix-editor', {
    className: 'trix-content daisy-wysiwyg-min-height-rem-24',
    disabled: readonly,
    placeholder: control.placeholder || '',
  });

  if (control.height) {
    editor.style.minHeight = control.height;
  }

  toolbar.id = `${inputId}-toolbar`;
  input.id = `${inputId}-input`;
  editor.setAttribute('input', input.id);
  editor.setAttribute('toolbar', toolbar.id);
  container.append(toolbar, input, editor);
  wrapper.append(container);

  return wrapFormControl(control, key, wrapper);
}

function createJsonScript(name, value) {
  const script = document.createElement('script');

  script.type = 'application/json';
  script.dataset[name] = '';
  script.textContent = JSON.stringify(value);

  return script;
}

function wrapFormControl(control, key, input) {
  const label = createElement('label', {
    className: 'form-control grid w-full gap-1',
    dataset: { blueprintProperty: key },
  });

  label.append(createLabelText(control, key), input);
  appendHelp(label, control);

  return label;
}

function createLabelText(control, key) {
  return createElement('span', {
    className: 'label-text text-xs font-medium text-base-content/70',
    text: control.label || key,
  });
}

function appendHelp(parent, control) {
  if (!control.help) {
    return;
  }

  parent.append(createElement('span', {
    className: 'text-xs text-base-content/50',
    text: control.help,
  }));
}

function applyTextControlAttributes(input, control) {
  setOptionalAttribute(input, 'pattern', control.pattern);
  setOptionalAttribute(input, 'minlength', control.minLength);
  setOptionalAttribute(input, 'maxlength', control.maxLength);
}

function applyNumberControlAttributes(input, control) {
  setOptionalAttribute(input, 'min', control.min);
  setOptionalAttribute(input, 'max', control.max);
  setOptionalAttribute(input, 'step', control.step);
}

function setOptionalAttribute(element, name, value) {
  if (value === null || value === undefined || value === '') {
    return;
  }

  element.setAttribute(name, String(value));
}

function initLazyEditors(root) {
  if (!root.querySelector('.code-editor, .trix-wrapper')) {
    return;
  }

  const schedule = window.requestAnimationFrame || ((callback) => window.setTimeout(callback, 0));

  schedule(() => {
    import('../lazy-editors.js')
      .then((module) => module.initEditorsIn?.(root))
      .catch(() => {});
  });
}

function createElement(tag, options = {}, children = []) {
  const element = document.createElement(tag);

  Object.entries(options.dataset || {}).forEach(([key, value]) => {
    element.dataset[key] = value;
  });

  if (options.className) {
    element.className = options.className;
  }

  if (options.text !== undefined) {
    element.textContent = options.text;
  }

  ['checked', 'disabled', 'required', 'selected'].forEach((property) => {
    if (options[property]) {
      element[property] = true;
    }
  });

  if (options.readonly) {
    element.readOnly = true;
    element.setAttribute('readonly', '');
  }

  ['id', 'name', 'placeholder', 'type', 'value'].forEach((attribute) => {
    if (options[attribute] !== undefined) {
      element.setAttribute(attribute, String(options[attribute]));
    }
  });

  children.forEach((child) => element.append(child));

  return element;
}

function replaceChildren(element) {
  if (typeof element.replaceChildren === 'function') {
    element.replaceChildren();
    return;
  }

  while (element.firstChild) {
    element.removeChild(element.firstChild);
  }
}

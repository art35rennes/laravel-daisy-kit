import { getControlType } from './nodes.js';

export function setDetailsOpen(root, open) {
  root.querySelector('[data-blueprint-details-panel]')?.classList.toggle('hidden', !open);
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

  const controlsWrap = createElement('div', { className: 'grid gap-2' });

  if (controls.length) {
    controls.forEach((control) => controlsWrap.append(renderBlueprintControl(control, node.id, values, readonly)));
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
  if (input.type === 'checkbox') {
    return input.checked;
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

  ['name', 'placeholder', 'type', 'value'].forEach((attribute) => {
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

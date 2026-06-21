const DefaultGraphVersion = 1;

const emptyGraph = () => ({
  version: DefaultGraphVersion,
  nodes: [],
  edges: [],
  viewport: { x: 0, y: 0, zoom: 1 },
});

const asArray = (value) => (Array.isArray(value) ? value : []);

const asPlainObject = (value) => (
  value && typeof value === 'object' && !Array.isArray(value) ? value : {}
);

const stringOrFallback = (value, fallback = '') => {
  if (value === null || value === undefined) {
    return fallback;
  }

  const normalized = String(value).trim();

  return normalized === '' ? fallback : normalized;
};

const numberOrFallback = (value, fallback = 0) => {
  const normalized = Number(value);

  return Number.isFinite(normalized) ? normalized : fallback;
};

export function normalizeNodeTypes(nodeTypes = []) {
  return asArray(nodeTypes)
    .map((type) => {
      const source = asPlainObject(type);
      const normalizedType = stringOrFallback(source.type);
      const controls = normalizeControls(source.controls ?? source.fields);

      if (!normalizedType) {
        return null;
      }

      return {
        type: normalizedType,
        label: stringOrFallback(source.label, normalizedType),
        category: stringOrFallback(source.category, 'General'),
        description: stringOrFallback(source.description),
        theme: stringOrFallback(source.theme, 'default'),
        display: normalizeNodeDisplay(source.display ?? source.variant),
        icon: stringOrFallback(source.icon ?? source.brandIcon),
        nameStrategy: normalizeNameStrategy(source.nameStrategy ?? source.naming),
        inputs: normalizePorts(source.inputs, false),
        outputs: normalizePorts(source.outputs, true),
        controls,
        defaults: {
          ...controlDefaults(controls),
          ...asPlainObject(source.defaults),
        },
      };
    })
    .filter(Boolean);
}

export function normalizeNodeDisplay(value) {
  const display = stringOrFallback(value, 'detailed');

  return ['minimal', 'detailed'].includes(display) ? display : 'detailed';
}

export function normalizeNameStrategy(value = {}) {
  if (typeof value === 'string') {
    return { mode: stringOrFallback(value, 'free') };
  }

  const source = asPlainObject(value);
  const mode = stringOrFallback(source.mode, 'free');

  return {
    mode: ['free', 'preset', 'auto'].includes(mode) ? mode : 'free',
    prefix: stringOrFallback(source.prefix),
    value: stringOrFallback(source.value),
  };
}

export function normalizeControls(controls = []) {
  return asArray(controls)
    .map((control) => {
      const source = asPlainObject(control);
      const key = stringOrFallback(source.key ?? source.name ?? source.id);

      if (!key) {
        return null;
      }

      const type = stringOrFallback(source.type, 'text');

      return {
        key,
        name: stringOrFallback(source.name, key),
        label: stringOrFallback(source.label, key),
        type: normalizeControlType(type),
        placeholder: stringOrFallback(source.placeholder),
        help: stringOrFallback(source.help ?? source.description),
        required: Boolean(source.required),
        pattern: stringOrFallback(source.pattern),
        minLength: source.minLength ?? source.minlength ?? null,
        maxLength: source.maxLength ?? source.maxlength ?? null,
        min: source.min ?? null,
        max: source.max ?? null,
        step: source.step ?? null,
        options: normalizeControlOptions(source.options),
        default: source.default ?? source.value ?? null,
      };
    })
    .filter(Boolean);
}

export function controlDefaults(controls = []) {
  return asArray(controls).reduce((defaults, control) => {
    if (control.default !== null && control.default !== undefined) {
      defaults[control.key] = control.default;
    }

    return defaults;
  }, {});
}

export function normalizeControlOptions(options = []) {
  return asArray(options)
    .map((option) => {
      if (typeof option === 'string' || typeof option === 'number' || typeof option === 'boolean') {
        return { value: String(option), label: String(option) };
      }

      const source = asPlainObject(option);
      const value = source.value ?? source.id ?? source.key;

      if (value === null || value === undefined) {
        return null;
      }

      return {
        value: String(value),
        label: stringOrFallback(source.label, String(value)),
      };
    })
    .filter(Boolean);
}

export function normalizeControlType(type) {
  const normalized = stringOrFallback(type, 'text');
  const aliases = {
    boolean: 'checkbox',
    bool: 'checkbox',
    dropdown: 'select',
    email: 'email',
    integer: 'number',
    string: 'text',
    toggle: 'checkbox',
  };
  const resolved = aliases[normalized] || normalized;

  return [
    'checkbox',
    'color',
    'date',
    'datetime-local',
    'email',
    'hidden',
    'month',
    'number',
    'password',
    'radio',
    'range',
    'select',
    'tel',
    'text',
    'textarea',
    'time',
    'url',
    'week',
  ].includes(resolved) ? resolved : 'text';
}

export function normalizePorts(ports = [], outputDefaultMultiple = false) {
  return asArray(ports)
    .map((port) => {
      const source = asPlainObject(port);
      const key = stringOrFallback(source.key);

      if (!key) {
        return null;
      }

      return {
        key,
        label: stringOrFallback(source.label, key),
        kind: stringOrFallback(source.kind, 'default'),
        type: stringOrFallback(source.type ?? source.dataType, 'any'),
        multiple: source.multiple === undefined ? outputDefaultMultiple : Boolean(source.multiple),
      };
    })
    .filter(Boolean);
}

export function normalizeGraph(value = {}, nodeTypes = []) {
  const source = typeof value === 'string' ? parseJson(value, emptyGraph()) : asPlainObject(value);
  const typeMap = new Map(normalizeNodeTypes(nodeTypes).map((type) => [type.type, type]));
  const nodes = [];
  const nodeIds = new Set();

  asArray(source.nodes).forEach((node, index) => {
    const sourceNode = asPlainObject(node);
    const type = stringOrFallback(sourceNode.type, typeMap.keys().next().value || 'node');
    const typeDefinition = typeMap.get(type);
    const id = stringOrFallback(sourceNode.id, `${type}-${index + 1}`);

    if (nodeIds.has(id)) {
      return;
    }

    nodeIds.add(id);
    nodes.push({
      id,
      type,
      label: stringOrFallback(sourceNode.label, typeDefinition?.label || type),
      position: {
        x: numberOrFallback(sourceNode.position?.x, index * 260),
        y: numberOrFallback(sourceNode.position?.y, 0),
      },
      data: {
        ...(typeDefinition?.defaults || {}),
        ...asPlainObject(sourceNode.data),
      },
    });
  });

  const nodeById = new Map(nodes.map((node) => [node.id, node]));
  const edges = [];
  const edgeIds = new Set();

  asArray(source.edges).forEach((edge, index) => {
    const sourceEdge = asPlainObject(edge);
    const normalizedEdge = {
      id: stringOrFallback(sourceEdge.id, `edge-${index + 1}`),
      source: stringOrFallback(sourceEdge.source),
      sourcePort: stringOrFallback(sourceEdge.sourcePort),
      target: stringOrFallback(sourceEdge.target),
      targetPort: stringOrFallback(sourceEdge.targetPort),
      data: asPlainObject(sourceEdge.data),
    };

    if (
      !normalizedEdge.source
      || !normalizedEdge.target
      || !nodeById.has(normalizedEdge.source)
      || !nodeById.has(normalizedEdge.target)
      || edgeIds.has(normalizedEdge.id)
      || !canConnect(normalizedEdge, nodeById, typeMap)
    ) {
      return;
    }

    edgeIds.add(normalizedEdge.id);
    edges.push(normalizedEdge);
  });

  const viewport = asPlainObject(source.viewport);

  return {
    version: numberOrFallback(source.version, DefaultGraphVersion),
    nodes,
    edges,
    viewport: {
      x: numberOrFallback(viewport.x, 0),
      y: numberOrFallback(viewport.y, 0),
      zoom: numberOrFallback(viewport.zoom ?? viewport.k, 1),
    },
  };
}

export function canConnect(edge, nodesOrMap, nodeTypesOrMap) {
  const nodeById = nodesOrMap instanceof Map
    ? nodesOrMap
    : new Map(asArray(nodesOrMap).map((node) => [node.id, node]));
  const typeMap = nodeTypesOrMap instanceof Map
    ? nodeTypesOrMap
    : new Map(normalizeNodeTypes(nodeTypesOrMap).map((type) => [type.type, type]));
  const sourceNode = nodeById.get(edge.source);
  const targetNode = nodeById.get(edge.target);

  if (!sourceNode || !targetNode) {
    return false;
  }

  if (sourceNode.id === targetNode.id) {
    return false;
  }

  const sourceType = typeMap.get(sourceNode.type);
  const targetType = typeMap.get(targetNode.type);

  if (!sourceType || !targetType) {
    return true;
  }

  const output = sourceType.outputs.find((port) => port.key === edge.sourcePort);
  const input = targetType.inputs.find((port) => port.key === edge.targetPort);

  if (!output || !input) {
    return false;
  }

  return portsCompatible(output, input);
}

export function portsCompatible(output, input) {
  return tokensCompatible(output?.kind, input?.kind) && tokensCompatible(output?.type, input?.type);
}

export function tokensCompatible(outputToken, inputToken) {
  const source = stringOrFallback(outputToken, 'any');
  const target = stringOrFallback(inputToken, 'any');

  if (source === 'any' || target === 'any' || source === target) {
    return true;
  }

  return source === 'int' && target === 'float';
}

export function validateControlsData(controls = [], data = {}) {
  const errors = {};

  asArray(controls).forEach((control) => {
    const value = data[control.key];
    const controlErrors = validateControlValue(control, value);

    if (controlErrors.length) {
      errors[control.key] = controlErrors;
    }
  });

  return {
    valid: Object.keys(errors).length === 0,
    errors,
  };
}

export function validateControlValue(control, value) {
  const errors = [];
  const empty = value === null || value === undefined || value === '';

  if (control.required && (empty || (control.type === 'checkbox' && value !== true))) {
    errors.push('required');
  }

  if (empty) {
    return errors;
  }

  if (['number', 'range'].includes(control.type)) {
    const number = Number(value);

    if (!Number.isFinite(number)) {
      errors.push('number');
      return errors;
    }

    if (control.min !== null && control.min !== undefined && number < Number(control.min)) {
      errors.push('min');
    }

    if (control.max !== null && control.max !== undefined && number > Number(control.max)) {
      errors.push('max');
    }

    if (control.step !== null && control.step !== undefined && !matchesStep(number, control)) {
      errors.push('step');
    }
  }

  if (['select', 'radio'].includes(control.type) && control.options?.length) {
    const values = control.options.map((option) => String(option.value));

    if (!values.includes(String(value))) {
      errors.push('option');
    }
  }

  if (control.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(value))) {
    errors.push('email');
  }

  if (control.type === 'url' && !isValidUrl(value)) {
    errors.push('url');
  }

  if (control.minLength !== null && control.minLength !== undefined && String(value).length < Number(control.minLength)) {
    errors.push('minLength');
  }

  if (control.maxLength !== null && control.maxLength !== undefined && String(value).length > Number(control.maxLength)) {
    errors.push('maxLength');
  }

  if (control.pattern) {
    try {
      if (!new RegExp(control.pattern).test(String(value))) {
        errors.push('pattern');
      }
    } catch {
      errors.push('pattern');
    }
  }

  return errors;
}

function isValidUrl(value) {
  try {
    new URL(String(value));
    return true;
  } catch {
    return false;
  }
}

function matchesStep(value, control) {
  const step = Number(control.step);

  if (!Number.isFinite(step) || step <= 0) {
    return true;
  }

  const base = Number(control.min ?? 0);
  const quotient = (value - (Number.isFinite(base) ? base : 0)) / step;

  return Math.abs(quotient - Math.round(quotient)) < Number.EPSILON * 100;
}

export function syncTextarea(root, graph) {
  const textarea = root?.querySelector?.('[data-blueprint-sync]');

  if (textarea) {
    textarea.value = JSON.stringify(graph);
  }
}

export function emitBlueprintEvent(root, name, detail = {}) {
  root?.dispatchEvent?.(new CustomEvent(`daisy:blueprint:${name}`, {
    bubbles: true,
    detail,
  }));
}

export function isPointerClick(start, event, threshold = 6) {
  if (!start || start.pointerId !== event.pointerId) {
    return false;
  }

  return Math.hypot(event.clientX - start.x, event.clientY - start.y) <= threshold;
}

export function parseJson(value, fallback = {}) {
  try {
    return JSON.parse(value);
  } catch {
    return fallback;
  }
}

export function readJsonPayload(root, selector, fallback = {}) {
  const element = root?.querySelector?.(selector);

  if (!element) {
    return fallback;
  }

  return parseJson(element.content?.textContent || element.textContent || '', fallback);
}

export const readJsonScript = readJsonPayload;

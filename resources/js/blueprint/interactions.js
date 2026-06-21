import { isPointerClick } from './core.js';

export function bindNodeClickToggle(element, node, onToggle) {
  if (!element || element.dataset.blueprintClickBound === 'true') {
    return false;
  }

  let pointerStart = null;

  element.dataset.blueprintClickBound = 'true';
  element.addEventListener('pointerdown', (event) => {
    if (event.button !== 0 || isInteractiveNodeTarget(event)) {
      pointerStart = null;
      return;
    }

    pointerStart = {
      pointerId: event.pointerId,
      x: event.clientX,
      y: event.clientY,
    };
  });
  element.addEventListener('pointerup', (event) => {
    if (isInteractiveNodeTarget(event)) {
      pointerStart = null;
      return;
    }

    if (isPointerClick(pointerStart, event)) {
      onToggle(node);
    }

    pointerStart = null;
  });
  element.addEventListener('pointercancel', () => {
    pointerStart = null;
  });

  return true;
}

export function isInteractiveNodeTarget(event) {
  const path = event.composedPath?.() || [];

  return path.some((target) => {
    if (!(target instanceof Element)) {
      return false;
    }

    return Boolean(target.closest?.([
      'button',
      'input',
      'select',
      'textarea',
      '[contenteditable="true"]',
      'rete-socket',
      'rete-ref',
      '.input-socket',
      '.output-socket',
    ].join(',')));
  });
}

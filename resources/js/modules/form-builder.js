/**
 * Minimal pointer bridge for the Livewire Form Builder outline.
 *
 * It owns only transient drag gesture state. Schema mutations still go through Livewire.
 *
 * @module modules/form-builder
 */

export default function initFormBuilder(root) {
  if (root.__daisyFormBuilderDragReady) return;
  root.__daisyFormBuilderDragReady = true;
  root.dataset.builderDragReady = '1';
  let ghost = null;
  let autoScrollFrame = null;
  let autoScrollDelta = 0;
  const dragStartTolerance = 4;

  const findLivewireComponent = (element) => {
    let node = element;

    while (node && !node.hasAttribute('wire:id')) {
      node = node.parentElement;
    }

    return node;
  };

  const preserveScrollAnchor = (anchor, callback) => {
    const beforeTop = anchor?.isConnected ? anchor.getBoundingClientRect().top : null;

    callback();

    if (typeof beforeTop !== 'number') return;

    window.requestAnimationFrame(() => {
      if (!anchor.isConnected) return;

      const delta = anchor.getBoundingClientRect().top - beforeTop;

      if (Math.abs(delta) > 1) {
        window.scrollBy(0, delta);
      }
    });
  };

  const clearDragState = (scrollAnchor = null) => {
    preserveScrollAnchor(scrollAnchor, () => {
      delete root.dataset.dragging;
      root.__daisyFormBuilderActiveDropZone = null;
      root.querySelectorAll('[data-builder-dragging-row], [data-builder-drop-active], [data-builder-drop-disabled], [data-builder-drop-disabled-row]').forEach((node) => {
        node.removeAttribute('data-builder-dragging-row');
        node.removeAttribute('data-builder-drop-active');
        node.removeAttribute('data-builder-drop-disabled');
        node.removeAttribute('data-builder-drop-disabled-row');
      });
    });

    document.body.style.removeProperty('cursor');
  };

  const parseJsonAttribute = (value, fallback = []) => {
    try {
      const parsed = JSON.parse(value || '');

      return Array.isArray(parsed) ? parsed : fallback;
    } catch (_) {
      return fallback;
    }
  };

  const findDropZone = (event, eligibleZones = null) => {
    if (typeof document.elementFromPoint !== 'function') {
      return null;
    }

    const element = document.elementFromPoint(event.clientX, event.clientY);
    const zone = element?.closest?.('[data-builder-drop-zone]');

    if (!zone || !root.contains(zone) || zone.hasAttribute('data-builder-drop-disabled')) {
      return null;
    }

    if (eligibleZones && !eligibleZones.includes(zone)) {
      return null;
    }

    return zone;
  };

  const collectEligibleDropZones = (draggedId, draggedDescendants) => {
    return Array.from(root.querySelectorAll('[data-builder-drop-zone]')).filter((zone) => {
      const targetId = zone.dataset.builderDropTarget;
      const disabled = !targetId || targetId === draggedId || draggedDescendants.includes(targetId);
      const dropRow = zone.closest('[data-builder-drop-row]');

      zone.toggleAttribute('data-builder-drop-disabled', disabled);
      dropRow?.toggleAttribute('data-builder-drop-disabled-row', disabled);
      zone.removeAttribute('data-builder-drop-active');

      return !disabled;
    });
  };

  const activateDropZone = (zone) => {
    if (root.__daisyFormBuilderActiveDropZone === zone) return;

    root.querySelectorAll('[data-builder-drop-active]').forEach((node) => {
      if (node !== zone) node.removeAttribute('data-builder-drop-active');
    });

    zone?.setAttribute('data-builder-drop-active', 'true');
    root.__daisyFormBuilderActiveDropZone = zone || null;
  };

  const startDrag = (state, currentEvent) => {
    if (state.dragStarted) return;

    state.dragStarted = true;
    document.body.style.cursor = 'grabbing';
    state.draggedRow?.setAttribute('data-builder-dragging-row', 'true');

    const activeGhost = prepareDragGhost(state.handle, state.draggedId);
    state.activeGhost = activeGhost;
    moveDragGhost(activeGhost, currentEvent);

    window.requestAnimationFrame(() => {
      if (!state.dragStarted || state.finished) return;

      preserveScrollAnchor(state.draggedRow, () => {
        state.eligibleZones = collectEligibleDropZones(state.draggedId, state.draggedDescendants);
        root.dataset.dragging = state.draggedId;
        root.__daisyFormBuilderActiveDropZone = null;
      });
      activateDropZone(findDropZone(currentEvent, state.eligibleZones));
    });
  };

  const computeAutoScrollDelta = (event) => {
    const edge = 140;

    if (event.clientY < edge) {
      return -Math.ceil((edge - event.clientY) / 2.8);
    }

    if (event.clientY > window.innerHeight - edge) {
      return Math.ceil((event.clientY - (window.innerHeight - edge)) / 2.8);
    }

    return 0;
  };

  const stopAutoScroll = () => {
    autoScrollDelta = 0;

    if (autoScrollFrame) {
      window.cancelAnimationFrame(autoScrollFrame);
      autoScrollFrame = null;
    }
  };

  const runAutoScroll = () => {
    if (autoScrollDelta === 0) {
      autoScrollFrame = null;

      return;
    }

    window.scrollBy(0, autoScrollDelta);
    autoScrollFrame = window.requestAnimationFrame(runAutoScroll);
  };

  const updateAutoScroll = (event) => {
    autoScrollDelta = computeAutoScrollDelta(event);

    if (autoScrollDelta === 0) {
      stopAutoScroll();

      return;
    }

    if (!autoScrollFrame) {
      autoScrollFrame = window.requestAnimationFrame(runAutoScroll);
    }
  };

  const getDragGhost = () => {
    if (ghost) return ghost;

    ghost = document.createElement('div');
    ghost.className = 'daisy-form-builder-drag-ghost';
    ghost.setAttribute('aria-hidden', 'true');
    ghost.innerHTML = '<span data-builder-drag-ghost-label></span><span data-builder-drag-ghost-type></span>';
    document.body.appendChild(ghost);

    return ghost;
  };

  const setHandleCaptured = (handle, pointerId, captured) => {
    try {
      if (captured) {
        handle.setPointerCapture(pointerId);
      } else if (handle.hasPointerCapture(pointerId)) {
        handle.releasePointerCapture(pointerId);
      }
    } catch (_) {}
  };

  const prepareDragGhost = (handle, draggedId) => {
    const row = handle.closest('[data-builder-field]');
    const label = row?.querySelector('[data-builder-select] span')?.textContent?.trim() || draggedId;
    const type = row?.querySelector('[data-builder-type-badge]')?.textContent?.trim() || '';
    const currentGhost = getDragGhost();

    currentGhost.querySelector('[data-builder-drag-ghost-label]').textContent = label;
    currentGhost.querySelector('[data-builder-drag-ghost-type]').textContent = type;
    currentGhost.hidden = false;

    return currentGhost;
  };

  const moveDragGhost = (ghost, event) => {
    if (!ghost) return;

    ghost.style.transform = `translate3d(${event.clientX + 14}px, ${event.clientY + 14}px, 0)`;
  };

  const hideDragGhost = (ghost) => {
    if (!ghost) return;

    ghost.hidden = true;
    ghost.style.transform = 'translate3d(-9999px, -9999px, 0)';
  };

  getDragGhost().hidden = true;

  root.addEventListener('pointerdown', (event) => {
    const handle = event.target.closest?.('[data-builder-drag-handle]');
    if (!handle || event.button !== 0) return;

    const draggedId = handle.dataset.builderDragField;
    if (!draggedId) return;

    event.preventDefault();
    event.stopPropagation();

    const draggedDescendants = parseJsonAttribute(handle.dataset.builderDragDescendants);
    const startX = event.clientX;
    const startY = event.clientY;
    const draggedRow = handle.closest('[data-builder-field]');
    const state = {
      activeGhost: null,
      draggedDescendants,
      draggedId,
      draggedRow,
      dragStarted: false,
      eligibleZones: null,
      finished: false,
      handle,
    };

    setHandleCaptured(handle, event.pointerId, true);

    let pendingMoveEvent = null;
    let animationFrame = null;

    const flushPointerMove = () => {
      animationFrame = null;

      if (!pendingMoveEvent) return;

      const currentEvent = pendingMoveEvent;
      pendingMoveEvent = null;

      moveDragGhost(state.activeGhost, currentEvent);
      updateAutoScroll(currentEvent);

      if (!state.dragStarted) {
        const movement = Math.hypot(currentEvent.clientX - startX, currentEvent.clientY - startY);

        if (movement < dragStartTolerance) {
          return;
        }

        startDrag(state, currentEvent);

        return;
      }

      activateDropZone(findDropZone(currentEvent, state.eligibleZones));
    };

    const detach = () => {
      document.removeEventListener('pointermove', onPointerMove, true);
      document.removeEventListener('pointerup', onPointerUp, true);
      document.removeEventListener('pointercancel', onPointerCancel, true);
    };

    const finish = () => {
      state.finished = true;
      clearDragState(state.draggedRow);
      hideDragGhost(state.activeGhost);
      stopAutoScroll();
      setHandleCaptured(handle, event.pointerId, false);

      if (animationFrame) {
        window.cancelAnimationFrame(animationFrame);
      }

      detach();
    };

    const onPointerMove = (moveEvent) => {
      moveEvent.preventDefault();
      pendingMoveEvent = moveEvent;

      if (!animationFrame) {
        animationFrame = window.requestAnimationFrame(flushPointerMove);
      }
    };

    const onPointerUp = (upEvent) => {
      if (state.dragStarted && !state.eligibleZones) {
        state.eligibleZones = collectEligibleDropZones(state.draggedId, state.draggedDescendants);
      }

      const zone = findDropZone(upEvent, state.eligibleZones);
      const targetId = zone?.dataset.builderDropTarget;
      const action = zone?.dataset.builderDropAction;
      const componentRoot = findLivewireComponent(root);

      finish();

      if (state.dragStarted && targetId && action && componentRoot && window.Livewire) {
        window.Livewire.find(componentRoot.getAttribute('wire:id'))?.call('dropField', targetId, action, state.draggedId);
      }
    };

    const onPointerCancel = () => {
      finish();
    };

    document.addEventListener('pointermove', onPointerMove, true);
    document.addEventListener('pointerup', onPointerUp, true);
    document.addEventListener('pointercancel', onPointerCancel, true);
  }, true);
}

import Trix from 'trix';
import '../css/wysiwyg.css';
import { createMountable } from './core/mountable.js';
import { createInstanceIdentifier } from './core/identifiers.js';

function registerTrixElements() {
    if (!customElements.get('trix-toolbar')) {
        customElements.define('trix-toolbar', Trix.elements.TrixToolbarElement);
    }
    if (!customElements.get('trix-editor')) {
        customElements.define('trix-editor', Trix.elements.TrixEditorElement);
    }
}

registerTrixElements();
Reflect.deleteProperty(globalThis, 'Trix');

function emit(root, name, detail = {}) {
    root.dispatchEvent(new CustomEvent(`daisy-kit:wysiwyg:${name}`, { bubbles: true, detail }));
}

function attachmentAttributes(attachment) {
    const attributes = attachment?.getAttributes?.();

    return attributes && typeof attributes === 'object' ? structuredClone(attributes) : {};
}

function attachmentIdentifier(attachment, identifiers) {
    if (identifiers.has(attachment)) return identifiers.get(attachment);
    const id = attachment?.id === undefined || attachment?.id === null
        ? createInstanceIdentifier('attachment')
        : String(attachment.id);
    identifiers.set(attachment, id);

    return id;
}

function isSafeWebUrl(value) {
    if (typeof value !== 'string' || value.trim() === '') return false;
    try {
        const url = new URL(value, document.baseURI);

        return ['http:', 'https:'].includes(url.protocol);
    } catch {
        return false;
    }
}

function initialize(root, configuration) {
    const host = root.querySelector('[data-daisy-kit-wysiwyg-editor]');
    const input = root.querySelector('[data-daisy-kit-wysiwyg-value]');
    const validation = root.querySelector('[data-daisy-kit-wysiwyg-validation]');
    const toolbarTemplate = root.querySelector('[data-daisy-kit-wysiwyg-toolbar-template]');

    if (!(host instanceof HTMLElement) || !(input instanceof HTMLInputElement) || !(validation instanceof HTMLInputElement)) {
        throw new Error(configuration.labels?.initializationFailed || 'The rich text editor could not be initialized.');
    }

    const initialValue = input.value;
    const initialDisabled = input.disabled;
    const form = input.form;
    const identifiers = new WeakMap();
    const attachments = new Map();
    const configuredEditorId = typeof configuration.editorId === 'string' && configuration.editorId !== ''
        ? configuration.editorId
        : createInstanceIdentifier('daisy-kit-wysiwyg');
    const editorId = document.getElementById(configuredEditorId)
        ? createInstanceIdentifier('daisy-kit-wysiwyg')
        : configuredEditorId;
    let active = true;
    const toolbar = document.createElement('trix-toolbar');
    toolbar.id = `${editorId}-toolbar`;
    toolbar.classList.add('daisy-kit-wysiwyg__toolbar');
    toolbar.hidden = configuration.showToolbar !== true;

    if (configuration.showToolbar === true && toolbarTemplate instanceof HTMLTemplateElement) {
        toolbar.append(toolbarTemplate.content.cloneNode(true));
    }
    host.append(toolbar);

    const editorElement = document.createElement('trix-editor');
    editorElement.id = editorId;
    editorElement.classList.add('trix-content', 'daisy-kit-wysiwyg__editor');
    editorElement.setAttribute('input', input.id);
    editorElement.setAttribute('toolbar', toolbar.id);
    if (typeof configuration.labelId === 'string' && configuration.labelId !== '') {
        editorElement.setAttribute('aria-labelledby', configuration.labelId);
    }
    if (typeof configuration.placeholder === 'string') editorElement.setAttribute('placeholder', configuration.placeholder);
    editorElement.toggleAttribute('autofocus', configuration.autofocus === true);
    editorElement.toggleAttribute('required', configuration.required === true);
    editorElement.toggleAttribute('disabled', configuration.disabled === true);
    if (configuration.readonly === true) {
        editorElement.setAttribute('contenteditable', 'false');
        editorElement.setAttribute('aria-readonly', 'true');
    }
    host.append(editorElement);

    const trixEditor = editorElement.editor;
    if (!trixEditor) {
        editorElement.remove();
        toolbar.remove();
        throw new Error(configuration.labels?.initializationFailed || 'The rich text editor could not be initialized.');
    }

    function snapshot(attachment) {
        const attributes = attachmentAttributes(attachment);
        const id = attachmentIdentifier(attachment, identifiers);

        return {
            attributes,
            id,
            pending: attachment?.file instanceof File && !attachment?.getURL?.() && !attachment?.getHref?.(),
        };
    }

    function collectDocumentAttachments() {
        const documentAttachments = trixEditor.getDocument?.().getAttachments?.() ?? [];
        for (const attachment of documentAttachments) {
            attachments.set(attachmentIdentifier(attachment, identifiers), attachment);
        }
    }

    function isEmpty() {
        return trixEditor.getDocument?.().isEmpty?.() ?? input.value === '';
    }

    function synchronizeValidation() {
        const empty = isEmpty();
        validation.value = empty ? '' : 'valid';
        const hasPendingAttachment = [...attachments.values()].some((attachment) => snapshot(attachment).pending);
        validation.setCustomValidity(hasPendingAttachment
            ? configuration.labels?.attachmentPending || 'Wait for pending attachments to finish uploading or remove them.'
            : '');
        editorElement.setAttribute('aria-invalid', String(hasPendingAttachment || (configuration.required === true && empty)));
    }

    function synchronizeAttachmentAccessibility() {
        for (const figure of editorElement.querySelectorAll('figure.attachment')) {
            const caption = figure.querySelector('figcaption')?.textContent?.trim();
            const accessibleName = caption || configuration.labels?.attachment || 'Attachment';
            const image = figure.querySelector('img');
            const link = figure.querySelector('a');

            if (image && image.getAttribute('alt') !== accessibleName) image.setAttribute('alt', accessibleName);
            if (link && link.getAttribute('aria-label') !== accessibleName) link.setAttribute('aria-label', accessibleName);
        }
    }

    function onChange() {
        if (!active) return;
        collectDocumentAttachments();
        synchronizeValidation();
        synchronizeAttachmentAccessibility();
        emit(root, 'change', { value: input.value });
    }

    function onFileAccept(event) {
        if (configuration.attachments !== true) event.preventDefault();
    }

    function onAttachmentAdd(event) {
        if (!active || !event.attachment) return;
        const item = snapshot(event.attachment);
        attachments.set(item.id, event.attachment);
        synchronizeValidation();
        queueMicrotask(synchronizeAttachmentAccessibility);
        emit(root, 'attachment-add', {
            file: event.attachment.file ?? null,
            id: item.id,
            name: item.attributes.filename ?? event.attachment.file?.name ?? '',
            size: item.attributes.filesize ?? event.attachment.file?.size ?? 0,
            type: item.attributes.contentType ?? event.attachment.file?.type ?? '',
        });
    }

    function onAttachmentEdit(event) {
        if (!active || !event.attachment) return;
        const item = snapshot(event.attachment);
        attachments.set(item.id, event.attachment);
        queueMicrotask(synchronizeAttachmentAccessibility);
        emit(root, 'attachment-edit', item);
    }

    function onAttachmentRemove(event) {
        if (!active || !event.attachment) return;
        const item = snapshot(event.attachment);
        event.attachment.releaseFile?.();
        attachments.delete(item.id);
        synchronizeValidation();
        emit(root, 'attachment-remove', { attributes: item.attributes, id: item.id });
    }

    function onAction(event) {
        if (active && typeof event.actionName === 'string') emit(root, 'action', { name: event.actionName });
    }

    function onToolbarKeydown(event) {
        const button = event.target.closest('button');
        if (!button || !toolbar.contains(button)) return;
        const buttons = [...toolbar.querySelectorAll('button:not([disabled])')];
        const index = buttons.indexOf(button);
        if (index === -1) return;
        let nextIndex = null;
        if (event.key === 'ArrowRight' || event.key === 'ArrowDown') nextIndex = (index + 1) % buttons.length;
        if (event.key === 'ArrowLeft' || event.key === 'ArrowUp') nextIndex = (index - 1 + buttons.length) % buttons.length;
        if (event.key === 'Home') nextIndex = 0;
        if (event.key === 'End') nextIndex = buttons.length - 1;
        if (nextIndex === null) return;
        event.preventDefault();
        buttons.forEach((item, itemIndex) => { item.tabIndex = itemIndex === nextIndex ? 0 : -1; });
        buttons[nextIndex].focus();
    }

    function getValue() {
        return input.value;
    }

    function setValue(value) {
        if (!active || typeof value !== 'string') return false;
        editorElement.value = value;
        synchronizeValidation();

        return true;
    }

    function clear() {
        return setValue('');
    }

    function focus() {
        if (!active || configuration.disabled === true) return false;
        editorElement.focus();

        return true;
    }

    function undo() {
        if (!active || trixEditor.canUndo?.() !== true) return false;
        trixEditor.undo();

        return true;
    }

    function redo() {
        if (!active || trixEditor.canRedo?.() !== true) return false;
        trixEditor.redo();

        return true;
    }

    function getAttachments() {
        return [...attachments.values()].map(snapshot);
    }

    function setAttachmentProgress(id, progress) {
        if (!active || !attachments.has(String(id)) || !Number.isFinite(Number(progress))) return false;
        const normalizedProgress = Math.min(100, Math.max(0, Number(progress)));
        attachments.get(String(id)).setUploadProgress(normalizedProgress);
        emit(root, 'attachment-progress', { id: String(id), progress: normalizedProgress });

        return true;
    }

    function resolveAttachment(id, attributes) {
        const attachment = attachments.get(String(id));
        if (!active || !attachment || !attributes || !isSafeWebUrl(attributes.url)) return false;
        if (attributes.href !== undefined && !isSafeWebUrl(attributes.href)) return false;
        const resolvedAttributes = {
            href: attributes.href ?? attributes.url,
            url: attributes.url,
        };
        attachment.setAttributes(resolvedAttributes);
        attachment.setUploadProgress?.(100);
        synchronizeValidation();
        queueMicrotask(synchronizeAttachmentAccessibility);
        emit(root, 'attachment-resolved', { attributes: structuredClone(resolvedAttributes), id: String(id) });

        return true;
    }

    function removeAttachment(id) {
        const attachment = attachments.get(String(id));
        if (!active || !attachment) return false;
        trixEditor.removeAttachment(attachment);

        return true;
    }

    function onFormReset(event) {
        if (!active || event.target !== form) return;
        queueMicrotask(() => {
            if (!active) return;
            for (const attachment of attachments.values()) attachment.releaseFile?.();
            editorElement.value = initialValue;
            attachments.clear();
            collectDocumentAttachments();
            synchronizeValidation();
            emit(root, 'change', { value: input.value });
        });
    }

    const toolbarButtons = [...toolbar.querySelectorAll('button')];
    const attachmentObserver = new MutationObserver(synchronizeAttachmentAccessibility);
    toolbarButtons.forEach((button, index) => {
        button.tabIndex = index === 0 ? 0 : -1;
        if (configuration.disabled === true || configuration.readonly === true) button.disabled = true;
    });

    editorElement.addEventListener('trix-change', onChange);
    editorElement.addEventListener('trix-file-accept', onFileAccept);
    editorElement.addEventListener('trix-attachment-add', onAttachmentAdd);
    editorElement.addEventListener('trix-attachment-edit', onAttachmentEdit);
    editorElement.addEventListener('trix-attachment-remove', onAttachmentRemove);
    editorElement.addEventListener('trix-action-invoke', onAction);
    toolbar.addEventListener('keydown', onToolbarKeydown);
    form?.addEventListener('reset', onFormReset);
    attachmentObserver.observe(editorElement, { childList: true, subtree: true });
    collectDocumentAttachments();
    synchronizeValidation();
    synchronizeAttachmentAccessibility();

    return {
        clear,
        destroy() {
            if (!active) return;
            active = false;
            editorElement.removeEventListener('trix-change', onChange);
            editorElement.removeEventListener('trix-file-accept', onFileAccept);
            editorElement.removeEventListener('trix-attachment-add', onAttachmentAdd);
            editorElement.removeEventListener('trix-attachment-edit', onAttachmentEdit);
            editorElement.removeEventListener('trix-attachment-remove', onAttachmentRemove);
            editorElement.removeEventListener('trix-action-invoke', onAction);
            toolbar.removeEventListener('keydown', onToolbarKeydown);
            form?.removeEventListener('reset', onFormReset);
            attachmentObserver.disconnect();
            for (const attachment of attachments.values()) attachment.releaseFile?.();
            attachments.clear();
            editorElement.remove();
            toolbar.remove();
            input.value = initialValue;
            input.disabled = initialDisabled;
            validation.value = '';
            validation.setCustomValidity('');
        },
        focus,
        getAttachments,
        getTrixEditor: () => active ? trixEditor : null,
        getValue,
        redo,
        removeAttachment,
        resolveAttachment,
        setAttachmentProgress,
        setValue,
        undo,
    };
}

const module = createMountable('wysiwyg', initialize);

export const { getInstance, mount, mountAll, unmount } = module;

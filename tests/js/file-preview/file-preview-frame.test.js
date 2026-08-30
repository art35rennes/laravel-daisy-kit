import { describe, expect, it, vi } from 'vitest';

const renderAsync = vi.fn((_data, output) => {
    const wrapper = document.createElement('div');
    const page = document.createElement('section');

    wrapper.className = 'docx-wrapper';
    page.className = 'docx';
    wrapper.append(page);
    output.append(wrapper);

    return Promise.resolve();
});
const pdfRender = vi.fn(() => ({ promise: Promise.resolve() }));
const pdfPage = {
    getViewport: ({ scale }) => ({ height: 1_000_000 * scale, width: 100 * scale }),
    render: pdfRender,
};
let pdfPageCount = 3;
const getDocument = vi.fn(() => ({
    destroy: vi.fn(() => Promise.resolve()),
    promise: Promise.resolve({
        getPage: vi.fn(() => Promise.resolve(pdfPage)),
        get numPages() {
            return pdfPageCount;
        },
    }),
}));
class PdfWorker {
    terminate = vi.fn();
}

vi.mock('docx-preview', () => ({ renderAsync }));
vi.mock('pdfjs-dist', () => ({ getDocument, GlobalWorkerOptions: { workerPort: null } }));
vi.mock('pdfjs-dist/build/pdf.worker.min.mjs?worker&inline', () => ({ default: PdfWorker }));

describe('file preview sandbox renderer', () => {
    it('renders every supported family with typed blobs and authenticated messages', async () => {
        document.body.innerHTML = '<main data-daisy-kit-file-preview-output data-daisy-kit-file-preview-token="token"></main>';
        vi.spyOn(HTMLCanvasElement.prototype, 'getContext').mockReturnValue({});
        const createObjectURL = vi.spyOn(URL, 'createObjectURL').mockReturnValue('blob:preview');
        const postMessage = vi.spyOn(window.parent, 'postMessage');

        await import('../../../resources/js/file-preview-frame.js');
        await vi.waitFor(() => expect(postMessage).toHaveBeenCalledWith(expect.objectContaining({ type: 'ready' }), '*'));
        postMessage.mockClear();

        function render(payload, renderId) {
            window.dispatchEvent(new MessageEvent('message', {
                data: {
                    channel: 'daisy-kit:file-preview:frame',
                    payload,
                    renderId,
                    token: 'token',
                    type: 'render',
                },
                source: window.parent,
            }));
        }

        render({
            data: 'Hello',
            mimeType: 'text/plain',
            name: 'notes.txt',
            truncated: true,
            truncatedLabel: 'Only the beginning is shown.',
            type: 'text',
        }, 1);
        await vi.waitFor(() => expect(document.querySelector('pre')?.textContent).toBe('Hello\n\n…'));
        expect(document.querySelector('.daisy-kit-file-preview-frame__notice')?.textContent)
            .toBe('Only the beginning is shown.');

        render({ data: new ArrayBuffer(2), mimeType: 'image/svg+xml', name: 'plan.svg', type: 'image' }, 2);
        await vi.waitFor(() => expect(document.querySelector('img')?.src).toBe('blob:preview'));
        expect(createObjectURL.mock.calls.at(-1)[0].type).toBe('image/svg+xml');

        render({ data: new ArrayBuffer(2), mimeType: 'audio/mpeg', name: 'brief.mp3', type: 'audio' }, 3);
        await vi.waitFor(() => expect(document.querySelector('audio')?.controls).toBe(true));
        expect(createObjectURL.mock.calls.at(-1)[0].type).toBe('audio/mpeg');

        render({ data: new ArrayBuffer(2), mimeType: 'video/mp4', name: 'clip.mp4', type: 'video' }, 4);
        await vi.waitFor(() => expect(document.querySelector('video')?.controls).toBe(true));

        render({ data: new ArrayBuffer(2), mimeType: 'application/pdf', name: 'report.pdf', type: 'pdf' }, 5);
        await vi.waitFor(() => expect(document.querySelectorAll('[data-daisy-kit-pdf-page]')).toHaveLength(3));
        expect(document.querySelector('iframe')).toBeNull();
        expect(pdfRender).toHaveBeenCalledTimes(3);
        expect([...document.querySelectorAll('[data-daisy-kit-pdf-page]')].every((canvas) => (
            canvas.width <= 8_192
            && canvas.height <= 8_192
            && canvas.width * canvas.height <= 16_777_216
        ))).toBe(true);

        render({
            data: new ArrayBuffer(2),
            docxView: 'page',
            mimeType: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            name: 'report.docx',
            type: 'docx',
            zoom: 125,
        }, 6);
        await vi.waitFor(() => expect(renderAsync).toHaveBeenCalledOnce());
        expect(document.querySelector('main').dataset.daisyKitDocxView).toBe('page');
        expect(document.querySelector('[data-daisy-kit-file-preview-docx-styles]')).not.toBeNull();
        expect(document.querySelector('.docx-wrapper').classList).toContain('daisy-kit-file-preview-zoom-125');

        window.dispatchEvent(new MessageEvent('message', {
            data: {
                channel: 'daisy-kit:file-preview:frame',
                renderId: 6,
                token: 'token',
                type: 'zoom',
                zoom: 150,
            },
            source: window.parent,
        }));
        expect(document.querySelector('.docx-wrapper').classList).toContain('daisy-kit-file-preview-zoom-150');

        const callsBeforeRejectedMessage = renderAsync.mock.calls.length;
        window.dispatchEvent(new MessageEvent('message', {
            data: {
                channel: 'daisy-kit:file-preview:frame',
                payload: { type: 'docx' },
                renderId: 7,
                token: 'wrong-token',
                type: 'render',
            },
            source: window.parent,
        }));
        expect(renderAsync).toHaveBeenCalledTimes(callsBeforeRejectedMessage);

        let rejectStalePdfPage;
        const stalePdfPage = new Promise((_resolve, reject) => {
            rejectStalePdfPage = reject;
        });

        pdfRender.mockReturnValueOnce({ promise: stalePdfPage });
        postMessage.mockClear();
        render({ data: new ArrayBuffer(2), mimeType: 'application/pdf', name: 'stale.pdf', type: 'pdf' }, 8);
        await vi.waitFor(() => expect(postMessage).toHaveBeenCalledWith(expect.objectContaining({
            renderId: 8,
            type: 'rendered',
        }), '*'));
        render({ data: 'Current render', mimeType: 'text/plain', name: 'current.txt', type: 'text' }, 9);
        rejectStalePdfPage(new Error('Stale PDF paint failed.'));

        await vi.waitFor(() => expect(postMessage).toHaveBeenCalledWith(expect.objectContaining({
            renderId: 9,
            type: 'rendered',
        }), '*'));
        expect(postMessage).not.toHaveBeenCalledWith(expect.objectContaining({
            renderId: 8,
            type: 'error',
        }), '*');

        pdfPageCount = 251;
        render({ data: new ArrayBuffer(2), mimeType: 'application/pdf', name: 'oversized.pdf', type: 'pdf' }, 10);
        await vi.waitFor(() => expect(postMessage).toHaveBeenCalledWith(expect.objectContaining({
            renderId: 10,
            type: 'error',
        }), '*'));
        pdfPageCount = 3;
    });
});

import { describe, expect, it, vi } from 'vitest';

const renderAsync = vi.fn(() => Promise.resolve());

vi.mock('docx-preview', () => ({ renderAsync }));

describe('file preview sandbox renderer', () => {
    it('renders every supported family with typed blobs and authenticated messages', async () => {
        document.body.innerHTML = '<main data-daisy-kit-file-preview-output data-daisy-kit-file-preview-token="token"></main>';
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
        await vi.waitFor(() => expect(document.querySelector('iframe')?.getAttribute('sandbox')).toBe(''));

        render({
            data: new ArrayBuffer(2),
            docxView: 'width',
            mimeType: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            name: 'report.docx',
            type: 'docx',
            zoom: 125,
        }, 6);
        await vi.waitFor(() => expect(renderAsync).toHaveBeenCalledOnce());
        expect(document.querySelector('main').dataset.daisyKitDocxView).toBe('width');
        expect(document.querySelector('main').classList).toContain('daisy-kit-file-preview-zoom-125');

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
        expect(document.querySelector('main').classList).toContain('daisy-kit-file-preview-zoom-150');

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
    });
});

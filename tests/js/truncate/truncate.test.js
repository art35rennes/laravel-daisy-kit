import { afterEach, describe, expect, it, vi } from 'vitest';
import { getInstance, mount, unmount } from '../../../resources/js/truncate.js';

function root(configuration) {
    document.body.innerHTML = `
        <section data-daisy-kit-module="truncate">
            <p data-daisy-kit-status hidden role="status"></p>
            <span data-daisy-kit-truncate-preview>
                <span data-daisy-kit-truncate-text></span>
                <button aria-expanded="false" aria-label="" data-daisy-kit-truncate-reveal hidden popovertarget="truncate-popover" popovertargetaction="show" type="button">&hellip;</button>
            </span>
            <div data-daisy-kit-truncate-popover id="truncate-popover" popover="auto"><p data-daisy-kit-truncate-full-text></p><button data-daisy-kit-truncate-close type="button">Close</button></div>
            <script data-daisy-kit-config type="application/json">${JSON.stringify(configuration)}</script>
        </section>
    `;

    return document.querySelector('[data-daisy-kit-module="truncate"]');
}

afterEach(() => {
    vi.restoreAllMocks();
    vi.useRealTimers();
});

describe('truncate entry', () => {
    it('only shows its reveal control when the rendered text overflows and restores it on destroy', () => {
        const element = root({ text: 'Long text', lines: 2, revealLabel: 'Read more', title: 'Summary' });
        const text = element.querySelector('[data-daisy-kit-truncate-text]');
        Object.defineProperties(text, { clientHeight: { value: 20 }, scrollHeight: { value: 40 }, clientWidth: { value: 100 }, scrollWidth: { value: 100 } });
        const button = element.querySelector('[data-daisy-kit-truncate-reveal]');

        mount(element);

        expect(button.hidden).toBe(false);
        expect(button.textContent).toBe('…');
        expect(button.getAttribute('aria-label')).toBe('Read more');
        expect(text.textContent).toBe('Long text');
        expect(getInstance(element).isTruncated()).toBe(true);
        unmount(element);
        expect(element.querySelector('[data-daisy-kit-truncate-reveal]').hidden).toBe(true);
        expect(element.querySelector('[data-daisy-kit-truncate-text]').textContent).toBe('');
    });

    it('shows a temporary anchored preview on hover and keeps it open while the pointer enters the popover', () => {
        vi.useFakeTimers();
        const element = root({ text: '18 rue de la Paix, 75002 Paris', lines: 1, revealLabel: 'Show full address', hover: true, hoverDelay: 200 });
        const text = element.querySelector('[data-daisy-kit-truncate-text]');
        Object.defineProperties(text, { clientHeight: { value: 20 }, scrollHeight: { value: 20 }, clientWidth: { value: 100 }, scrollWidth: { value: 180 } });
        const reveal = element.querySelector('[data-daisy-kit-truncate-reveal]');
        const popover = element.querySelector('[data-daisy-kit-truncate-popover]');
        popover.showPopover = vi.fn();
        popover.hidePopover = vi.fn();

        mount(element);
        reveal.dispatchEvent(new PointerEvent('pointerenter'));
        vi.advanceTimersByTime(199);
        expect(popover.showPopover).not.toHaveBeenCalled();
        vi.advanceTimersByTime(1);
        expect(popover.showPopover).toHaveBeenCalledOnce();
        expect(popover.showPopover).toHaveBeenCalledWith({ source: reveal });
        expect(popover.dataset.daisyKitTruncatePinned).toBe('false');

        reveal.dispatchEvent(new PointerEvent('pointerleave'));
        popover.dispatchEvent(new PointerEvent('pointerenter'));
        vi.runAllTimers();
        expect(popover.hidePopover).not.toHaveBeenCalled();

        popover.dispatchEvent(new PointerEvent('pointerleave'));
        vi.runAllTimers();
        expect(popover.hidePopover).toHaveBeenCalledOnce();
        vi.useRealTimers();
    });

    it('pins the preview on click and lets native light dismiss synchronize the component', () => {
        const element = root({ text: 'Long address', lines: 1, revealLabel: 'Show full address', hover: true, backdrop: true });
        const text = element.querySelector('[data-daisy-kit-truncate-text]');
        Object.defineProperties(text, { clientHeight: { value: 20 }, scrollHeight: { value: 20 }, clientWidth: { value: 100 }, scrollWidth: { value: 180 } });
        const reveal = element.querySelector('[data-daisy-kit-truncate-reveal]');
        const popover = element.querySelector('[data-daisy-kit-truncate-popover]');
        popover.showPopover = vi.fn();
        popover.hidePopover = vi.fn();
        const closed = [];
        element.addEventListener('daisy-kit:truncate:closed', event => closed.push(event.detail));

        mount(element);
        reveal.click();
        expect(popover.showPopover).toHaveBeenCalledOnce();
        expect(popover.dataset.daisyKitTruncatePinned).toBe('true');
        expect(popover.dataset.daisyKitTruncateBackdrop).toBe('true');
        expect(reveal.getAttribute('aria-expanded')).toBe('true');

        const lightDismiss = new Event('toggle');
        Object.defineProperties(lightDismiss, { newState: { value: 'closed' }, oldState: { value: 'open' } });
        popover.dispatchEvent(lightDismiss);
        expect(popover.dataset.daisyKitTruncatePinned).toBe('false');
        expect(reveal.getAttribute('aria-expanded')).toBe('false');
        expect(closed).toEqual([{ text: 'Long address' }]);
    });

    it('opens on keyboard focus without pinning and pins through the public facade', () => {
        vi.useFakeTimers();
        const element = root({ text: 'Long address', lines: 1, revealLabel: 'Show full address', hover: true, hoverDelay: 0 });
        const text = element.querySelector('[data-daisy-kit-truncate-text]');
        Object.defineProperties(text, { clientHeight: { value: 20 }, scrollHeight: { value: 20 }, clientWidth: { value: 100 }, scrollWidth: { value: 180 } });
        const reveal = element.querySelector('[data-daisy-kit-truncate-reveal]');
        const popover = element.querySelector('[data-daisy-kit-truncate-popover]');
        popover.showPopover = vi.fn();
        popover.hidePopover = vi.fn();

        const instance = mount(element);
        reveal.dispatchEvent(new FocusEvent('focus'));
        vi.runAllTimers();
        expect(popover.showPopover).toHaveBeenCalledOnce();
        expect(popover.dataset.daisyKitTruncatePinned).toBe('false');
        expect(instance.open()).toBe(true);
        expect(popover.showPopover).toHaveBeenCalledOnce();
        expect(popover.dataset.daisyKitTruncatePinned).toBe('true');
        expect(instance.close()).toBe(true);
        vi.useRealTimers();
    });

    it('opens and closes a native popover through its facade and events', () => {
        const element = root({ text: 'Long text', lines: 1, revealLabel: 'Read more', title: null });
        const text = element.querySelector('[data-daisy-kit-truncate-text]');
        Object.defineProperties(text, { clientHeight: { value: 20 }, scrollHeight: { value: 40 }, clientWidth: { value: 100 }, scrollWidth: { value: 100 } });
        const popover = element.querySelector('[data-daisy-kit-truncate-popover]');
        popover.showPopover = vi.fn();
        popover.hidePopover = vi.fn();
        const opened = [];
        element.addEventListener('daisy-kit:truncate:opened', (event) => opened.push(event.detail));

        const instance = mount(element);
        expect(Object.keys(instance).sort()).toEqual(['close', 'isTruncated', 'open', 'refresh']);
        expect(instance.refresh()).toBe(true);
        expect(instance.open()).toBe(true);
        expect(instance.open()).toBe(false);
        expect(instance.close()).toBe(true);
        expect(instance.close()).toBe(false);

        expect(popover.showPopover).toHaveBeenCalledOnce();
        expect(popover.hidePopover).toHaveBeenCalledOnce();
        expect(opened).toEqual([{ text: 'Long text' }]);
    });
});

import { afterEach, describe, expect, it, vi } from 'vitest';
import { getInstance, mount, unmount } from '../../../resources/js/scrollspy.js';

class IntersectionObserverStub {
    static instances = [];

    constructor(callback, options) {
        this.callback = callback;
        this.options = options;
        this.targets = [];
        IntersectionObserverStub.instances.push(this);
    }

    observe(target) { this.targets.push(target); }
    disconnect() { this.targets = []; }
}

function root(configuration) {
    document.body.innerHTML = `
        <main id="guide"><h2 id="install">Install</h2><h3 id="configure">Configure</h3></main>
        <nav data-daisy-kit-module="scrollspy"><p data-daisy-kit-status hidden role="status"></p><ul data-daisy-kit-scrollspy-list></ul><script data-daisy-kit-config type="application/json">${JSON.stringify(configuration)}</script></nav>
    `;

    return document.querySelector('[data-daisy-kit-module="scrollspy"]');
}

afterEach(() => vi.unstubAllGlobals());

describe('scrollspy entry', () => {
    it('discovers headings, tracks the active link and exposes a facade', () => {
        vi.stubGlobal('IntersectionObserver', IntersectionObserverStub);
        const element = root({ target: '#guide', items: [], selector: 'h2[id],h3[id]', smooth: true, offset: 0, rootMargin: '0px' });
        const changed = [];
        element.addEventListener('daisy-kit:scrollspy:change', (event) => changed.push(event.detail));

        const instance = mount(element);
        const configure = document.getElementById('configure');
        IntersectionObserverStub.instances[0].callback([{ target: configure, isIntersecting: true }]);

        expect(getInstance(element)).toBe(instance);
        expect(Object.keys(instance).sort()).toEqual(['getActive', 'refresh', 'scrollTo']);
        expect(instance.getActive()).toBe('configure');
        expect(element.querySelector('a[href="#configure"]').getAttribute('aria-current')).toBe('location');
        expect(changed).toEqual([{ id: 'configure' }]);
    });

    it('scrolls to a configured target and removes generated navigation during destroy', () => {
        vi.stubGlobal('IntersectionObserver', IntersectionObserverStub);
        const scrollIntoView = vi.fn();
        document.documentElement.scrollIntoView = scrollIntoView;
        const element = root({ target: '#guide', items: [{ id: 'install', label: 'Start' }], selector: 'h2[id],h3[id]', smooth: true, offset: 0, rootMargin: '0px' });
        const install = document.getElementById('install');
        install.scrollIntoView = scrollIntoView;

        const instance = mount(element);
        expect(instance.refresh()).toBe(true);
        expect(instance.scrollTo('install')).toBe(true);
        expect(instance.scrollTo('missing')).toBe(false);
        unmount(element);

        expect(scrollIntoView).toHaveBeenCalledWith({ behavior: 'smooth', block: 'start' });
        expect(element.querySelector('[data-daisy-kit-scrollspy-list]').children).toHaveLength(0);
        expect(getInstance(element)).toBeNull();
    });

    it('rediscovers dynamic headings when refreshed', () => {
        vi.stubGlobal('IntersectionObserver', IntersectionObserverStub);
        const element = root({ target: '#guide', items: [], selector: 'h2[id],h3[id]', smooth: false, offset: 0, rootMargin: '0px' });
        const instance = mount(element);
        const heading = document.createElement('h2');
        heading.id = 'deploy';
        heading.textContent = 'Deploy';
        heading.scrollIntoView = vi.fn();
        document.getElementById('guide').append(heading);

        expect(instance.refresh()).toBe(true);

        expect(element.querySelector('a[href="#deploy"]').textContent).toBe('Deploy');
        expect(element.querySelector('a[href="#install"]').getAttribute('aria-current')).toBe('location');
        expect(IntersectionObserverStub.instances.at(-1).targets).toContain(heading);
        expect(instance.scrollTo('deploy')).toBe(true);
    });

    it('applies the configured offset to page and scroll-container navigation', () => {
        vi.stubGlobal('IntersectionObserver', IntersectionObserverStub);
        const pageScrollTo = vi.fn();
        vi.stubGlobal('scrollTo', pageScrollTo);
        Object.defineProperty(window, 'scrollY', { configurable: true, value: 200 });
        const pageElement = root({ target: '#guide', items: [], selector: 'h2[id]', smooth: false, offset: 24, rootMargin: '0px' });
        vi.spyOn(document.getElementById('install'), 'getBoundingClientRect').mockReturnValue({ top: 100 });

        expect(mount(pageElement).scrollTo('install')).toBe(true);
        expect(pageScrollTo).toHaveBeenCalledWith({ behavior: 'auto', top: 276 });

        unmount(pageElement);
        const containerElement = root({ target: '#guide', items: [], selector: 'h2[id]', smooth: false, offset: 24, rootMargin: '0px' });
        const guide = document.getElementById('guide');
        const container = document.createElement('section');
        guide.before(container);
        container.append(guide);
        Object.defineProperties(container, {
            clientHeight: { configurable: true, value: 100 },
            scrollHeight: { configurable: true, value: 500 },
            scrollTop: { configurable: true, value: 40, writable: true },
        });
        container.style.overflowY = 'auto';
        container.scrollTo = vi.fn();
        vi.spyOn(container, 'getBoundingClientRect').mockReturnValue({ top: 20 });
        vi.spyOn(document.getElementById('install'), 'getBoundingClientRect').mockReturnValue({ top: 100 });

        const remounted = mount(containerElement);
        expect(remounted.scrollTo('install')).toBe(true);
        expect(container.scrollTo).toHaveBeenCalledWith({ behavior: 'auto', top: 96 });
    });

    it('uses the target itself as the observer root and scroll container', () => {
        vi.stubGlobal('IntersectionObserver', IntersectionObserverStub);
        const element = root({ target: '#guide', items: [], selector: 'h2[id]', smooth: false, offset: 24, rootMargin: '0px' });
        const guide = document.getElementById('guide');
        Object.defineProperties(guide, {
            clientHeight: { configurable: true, value: 100 },
            scrollHeight: { configurable: true, value: 500 },
            scrollTop: { configurable: true, value: 40, writable: true },
        });
        guide.style.overflowY = 'auto';
        guide.scrollTo = vi.fn();
        vi.spyOn(guide, 'getBoundingClientRect').mockReturnValue({ top: 20 });
        vi.spyOn(document.getElementById('install'), 'getBoundingClientRect').mockReturnValue({ top: 100 });

        const instance = mount(element);

        expect(IntersectionObserverStub.instances.at(-1).options.root).toBe(guide);
        expect(instance.scrollTo('install')).toBe(true);
        expect(guide.scrollTo).toHaveBeenCalledWith({ behavior: 'auto', top: 96 });
    });

    it('recalculates the scroll container when dynamic content changes overflow', () => {
        vi.stubGlobal('IntersectionObserver', IntersectionObserverStub);
        const element = root({ target: '#guide', items: [], selector: 'h2[id]', smooth: false, offset: 0, rootMargin: '0px' });
        const instance = mount(element);
        const guide = document.getElementById('guide');

        expect(IntersectionObserverStub.instances.at(-1).options.root).toBeNull();

        Object.defineProperties(guide, {
            clientHeight: { configurable: true, value: 100 },
            scrollHeight: { configurable: true, value: 500 },
        });
        guide.style.overflowY = 'auto';

        expect(instance.refresh()).toBe(true);
        expect(IntersectionObserverStub.instances.at(-1).options.root).toBe(guide);
    });

    it('activates the first heading immediately and tracks all intersecting headings', () => {
        vi.stubGlobal('IntersectionObserver', IntersectionObserverStub);
        const element = root({ target: '#guide', items: [], selector: 'h2[id],h3[id]', smooth: false, offset: 0, rootMargin: '0px' });
        const instance = mount(element);
        const observer = IntersectionObserverStub.instances.at(-1);
        const install = document.getElementById('install');
        const configure = document.getElementById('configure');

        expect(instance.getActive()).toBe('install');
        expect(element.querySelector('[data-daisy-kit-scrollspy-id="install"]').getAttribute('aria-current')).toBe('location');

        observer.callback([{ target: install, isIntersecting: true, boundingClientRect: { top: 10 } }]);
        observer.callback([{ target: configure, isIntersecting: true, boundingClientRect: { top: 100 } }]);
        expect(instance.getActive()).toBe('install');

        observer.callback([{ target: install, isIntersecting: false, boundingClientRect: { top: -20 } }]);
        expect(instance.getActive()).toBe('configure');
    });

    it('marks every ancestor link in deeply nested navigation and keeps aria-current exclusive', () => {
        vi.stubGlobal('IntersectionObserver', IntersectionObserverStub);
        const deploy = document.createElement('h4');
        const element = root({ target: '#guide', items: [], selector: 'h2[id],h3[id],h4[id]', smooth: false, offset: 0, rootMargin: '0px' });
        deploy.id = 'deploy';
        deploy.textContent = 'Deploy';
        document.getElementById('guide').append(deploy);
        mount(element);
        const observer = IntersectionObserverStub.instances.at(-1);

        observer.callback([{ target: deploy, isIntersecting: true, boundingClientRect: { top: 0 } }]);

        const installLink = element.querySelector('[data-daisy-kit-scrollspy-id="install"]');
        const configureLink = element.querySelector('[data-daisy-kit-scrollspy-id="configure"]');
        const deployLink = element.querySelector('[data-daisy-kit-scrollspy-id="deploy"]');
        expect(installLink.hasAttribute('data-daisy-kit-scrollspy-parent-active')).toBe(true);
        expect(configureLink.hasAttribute('data-daisy-kit-scrollspy-parent-active')).toBe(true);
        expect(deployLink.getAttribute('aria-current')).toBe('location');
        expect([...element.querySelectorAll('[aria-current="location"]')]).toEqual([deployLink]);

        observer.callback([{ target: document.getElementById('install'), isIntersecting: true, boundingClientRect: { top: 0 } }]);

        expect(element.querySelectorAll('[data-daisy-kit-scrollspy-parent-active]')).toHaveLength(0);
        expect(installLink.getAttribute('aria-current')).toBe('location');
        expect(configureLink.hasAttribute('aria-current')).toBe(false);
        expect(deployLink.hasAttribute('aria-current')).toBe(false);
        expect([...element.querySelectorAll('[aria-current="location"]')]).toEqual([installLink]);
    });
});

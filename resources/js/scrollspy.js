import '../css/scrollspy.css';
import { createMountable } from './core/mountable.js';

function emit(root, name, detail) {
    root.dispatchEvent(new CustomEvent(`daisy-kit:scrollspy:${name}`, { bubbles: true, detail }));
}

function validItems(items) {
    if (!Array.isArray(items)) {
        return [];
    }

    return items.flatMap((item) => {
        if (!item || Array.isArray(item) || typeof item !== 'object' || typeof item.id !== 'string' || item.id === '') {
            return [];
        }

        return [{ id: item.id, label: typeof item.label === 'string' && item.label !== '' ? item.label : item.id, level: 2 }];
    });
}

function scrollableContainer(target) {
    let element = target;

    while (element) {
        const style = window.getComputedStyle(element);
        const scrollable = ['auto', 'scroll'].includes(style.overflowY) && element.scrollHeight > element.clientHeight;

        if (scrollable) {
            return element;
        }

        element = element.parentElement;
    }

    return null;
}

function normalizedOffset(value) {
    return Number.isFinite(value) ? Math.max(0, value) : 0;
}

function initialize(root, configuration) {
    const list = root.querySelector('[data-daisy-kit-scrollspy-list]');

    if (!(list instanceof HTMLUListElement)) {
        throw new Error('The scrollspy module is missing its navigation list.');
    }

    const initialMarkup = list.innerHTML;
    const target = typeof configuration.target === 'string' && configuration.target !== ''
        ? document.querySelector(configuration.target)
        : null;

    if (!(target instanceof HTMLElement)) {
        throw new Error('The scrollspy target cannot be found.');
    }

    const selector = typeof configuration.selector === 'string' && configuration.selector !== ''
        ? configuration.selector
        : 'h2[id],h3[id]';
    const smooth = configuration.smooth !== false;
    const offset = normalizedOffset(configuration.offset);
    const rootMargin = typeof configuration.rootMargin === 'string' && configuration.rootMargin !== ''
        ? configuration.rootMargin
        : '0px 0px -60% 0px';
    let container = null;
    const configuredItems = validItems(configuration.items);

    function discoverItems() {
        if (configuredItems.length > 0) {
            return configuredItems;
        }

        return [...target.querySelectorAll(selector)].flatMap((heading) => {
            if (!(heading instanceof HTMLElement) || heading.id === '') {
                return [];
            }

            return [{
                id: heading.id,
                label: heading.textContent?.trim() || heading.id,
                level: Number.parseInt(heading.tagName.slice(1), 10) || 2,
            }];
        });
    }

    let items = [];
    let targets = new Map();
    let visibleTargets = new Map();
    let observer = null;
    let activeId = null;

    function render() {
        list.replaceChildren();
        const parentItems = new Map();

        items.forEach((item) => {
            const listItem = document.createElement('li');
            const link = document.createElement('a');
            link.href = `#${encodeURIComponent(item.id)}`;
            link.textContent = item.label;
            link.dataset.daisyKitScrollspyId = item.id;
            listItem.appendChild(link);

            const parent = [...parentItems.entries()].reverse().find(([level]) => level < item.level)?.[1];
            if (parent) {
                let childList = parent.querySelector(':scope > ul');

                if (!(childList instanceof HTMLUListElement)) {
                    childList = document.createElement('ul');
                    parent.appendChild(childList);
                }

                childList.appendChild(listItem);
            } else {
                list.appendChild(listItem);
            }

            parentItems.set(item.level, listItem);
            [...parentItems.keys()].filter((level) => level > item.level).forEach((level) => parentItems.delete(level));
        });
    }

    function setActive(id, notify = true) {
        if (!targets.has(id) || activeId === id) {
            return;
        }

        activeId = id;
        root.querySelectorAll('[data-daisy-kit-scrollspy-id]').forEach((link) => {
            const current = link.dataset.daisyKitScrollspyId === id;
            link.classList.toggle('menu-active', current);

            if (current) {
                link.setAttribute('aria-current', 'location');
            } else {
                link.removeAttribute('aria-current');
            }
        });
        root.querySelectorAll('[data-daisy-kit-scrollspy-parent-active]').forEach((link) => link.removeAttribute('data-daisy-kit-scrollspy-parent-active'));
        const activeLink = [...root.querySelectorAll('[data-daisy-kit-scrollspy-id]')]
            .find((link) => link.dataset.daisyKitScrollspyId === id);
        let parentItem = activeLink?.closest('li')?.parentElement?.closest('li');

        while (parentItem) {
            parentItem.querySelector(':scope > a')?.setAttribute('data-daisy-kit-scrollspy-parent-active', '');
            parentItem = parentItem.parentElement?.closest('li');
        }

        if (notify) {
            emit(root, 'change', { id });
        }
    }

    function refresh() {
        observer?.disconnect();
        container = scrollableContainer(target);
        visibleTargets = new Map();
        items = discoverItems();
        targets = new Map(items.flatMap((item) => {
            const heading = [...target.querySelectorAll('[id]')].find((candidate) => candidate.id === item.id);

            return heading instanceof HTMLElement ? [[item.id, heading]] : [];
        }));
        const preferredActiveId = activeId !== null && targets.has(activeId)
            ? activeId
            : targets.keys().next().value ?? null;
        activeId = null;
        render();

        if (preferredActiveId !== null) {
            setActive(preferredActiveId, false);
        }

        if (!('IntersectionObserver' in window)) {
            return false;
        }

        observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    visibleTargets.set(entry.target.id, entry);
                } else {
                    visibleTargets.delete(entry.target.id);
                }
            });

            const visible = [...visibleTargets.values()];

            if (visible.length === 0) {
                return;
            }

            visible.sort((first, second) => {
                const topDifference = first.boundingClientRect.top - second.boundingClientRect.top;

                if (topDifference !== 0) {
                    return topDifference;
                }

                return items.findIndex((item) => item.id === first.target.id)
                    - items.findIndex((item) => item.id === second.target.id);
            });
            setActive(visible[0].target.id);
        }, { root: container, rootMargin, threshold: [0, 0.1, 1] });
        targets.forEach((heading) => observer.observe(heading));

        return true;
    }

    function scrollTo(id) {
        const heading = targets.get(id);

        if (!heading) {
            return false;
        }

        const behavior = smooth ? 'smooth' : 'auto';
        if (container && offset > 0) {
            const top = heading.getBoundingClientRect().top - container.getBoundingClientRect().top + container.scrollTop - offset;
            container.scrollTo({ top, behavior });
        } else if (!container && offset > 0) {
            const top = window.scrollY + heading.getBoundingClientRect().top - offset;
            window.scrollTo({ top, behavior });
        } else {
            heading.scrollIntoView({ behavior, block: 'start' });
        }

        setActive(id);

        return true;
    }

    function handleClick(event) {
        const link = event.target.closest('[data-daisy-kit-scrollspy-id]');

        if (!(link instanceof HTMLAnchorElement) || !root.contains(link)) {
            return;
        }

        event.preventDefault();
        scrollTo(link.dataset.daisyKitScrollspyId);
    }

    root.addEventListener('click', handleClick);
    refresh();

    return {
        getActive: () => activeId,
        refresh,
        scrollTo,
        destroy() {
            observer?.disconnect();
            root.removeEventListener('click', handleClick);
            list.innerHTML = initialMarkup;
        },
    };
}

const module = createMountable('scrollspy', initialize);

export const { getInstance, mount, mountAll, unmount } = module;

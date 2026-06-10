const normalizeText = (value) => (value || '')
  .toLowerCase()
  .normalize('NFD')
  .replace(/\p{Diacritic}/gu, '');

const slugify = (value) => (value || '')
  .toLowerCase()
  .trim()
  .replace(/[^\w\s-]/g, '')
  .replace(/\s+/g, '-');

export default function init(root) {
  const panel = root.querySelector('[data-section-nav-panel]');
  const box = root.querySelector('[data-section-nav-box]');
  const list = root.querySelector('[data-section-nav-list]');
  const empty = root.querySelector('[data-section-nav-empty]');
  const search = root.querySelector('[data-section-nav-search]');
  const button = root.querySelector('[data-section-nav-button]');
  const iconOpen = root.querySelector('[data-section-nav-icon-open]');
  const iconClose = root.querySelector('[data-section-nav-icon-close]');

  if (!panel || !box || !list || !button) {
    return;
  }

  const targetSelector = root.dataset.targetSelector || 'div.space-y-10 > section';
  const headingSelector = root.dataset.headingSelector || 'h2';
  let cachedData = [];

  function collectSections() {
    const sections = Array.from(document.querySelectorAll(targetSelector));
    const seen = new Set();

    return sections.reduce((acc, section) => {
      const heading = section.querySelector(headingSelector);

      if (!heading) {
        return acc;
      }

      const label = heading.textContent.trim();
      let id = section.id || slugify(label);
      let suffix = 2;

      while (seen.has(id)) {
        id = `${section.id || slugify(label)}-${suffix++}`;
      }

      seen.add(id);

      if (!section.id) {
        section.id = id;
      }

      acc.push({
        id,
        label,
        key: normalizeText(label),
      });

      return acc;
    }, []);
  }

  function adjustOverflow() {
    const viewportW = window.innerWidth;

    const rect = panel.getBoundingClientRect();
    const shift = Math.max(0, rect.right - viewportW + 16);
    const previousAnimation = panel.__daisySectionNavFrameAnimation;
    const animation = panel.animate(
      [{ transform: shift ? `translateX(-${shift}px)` : 'translateX(0)' }],
      { duration: 1, fill: 'forwards' },
    );

    previousAnimation?.cancel?.();
    panel.__daisySectionNavFrameAnimation = animation;
  }

  function render(filter = '') {
    if (!cachedData.length) {
      cachedData = collectSections();
    }

    const key = normalizeText(filter);
    const items = cachedData.filter((item) => !key || item.key.includes(key));

    list.innerHTML = '';

    if (!items.length) {
      empty?.classList.remove('hidden');
      return;
    }

    empty?.classList.add('hidden');

    items.forEach((item) => {
      const li = document.createElement('li');
      const link = document.createElement('a');

      link.href = `#${item.id}`;
      link.textContent = item.label;
      li.appendChild(link);
      list.appendChild(li);
    });
  }

  function toggle(forceOpen) {
    const open = forceOpen ?? panel.classList.contains('hidden');

    panel.classList.toggle('hidden', !open);
    iconOpen?.classList.toggle('hidden', open);
    iconClose?.classList.toggle('hidden', !open);

    if (!open) {
      return;
    }

    cachedData = [];

    if (search) {
      search.value = '';
    }

    render();
    adjustOverflow();
    setTimeout(() => search?.focus(), 0);
  }

  button.addEventListener('click', () => toggle());
  panel.addEventListener('click', (event) => {
    if (event.target instanceof HTMLAnchorElement) {
      toggle(false);
    }
  });

  document.addEventListener('click', (event) => {
    if (!root.contains(event.target)) {
      toggle(false);
    }
  });

  window.addEventListener('resize', adjustOverflow);
  search?.addEventListener('input', () => render(search.value));

  document.addEventListener('keydown', (event) => {
    if (event.key === '/' && !event.ctrlKey && !event.metaKey && !event.altKey) {
      if (panel.classList.contains('hidden')) {
        toggle(true);
      }

      if (search) {
        event.preventDefault();
        search.focus();
      }
    }
  });

  const targetRoot = document.querySelector(targetSelector)?.parentElement;

  if (targetRoot) {
    const observer = new MutationObserver(() => {
      cachedData = [];

      if (!panel.classList.contains('hidden')) {
        render(search?.value || '');
      }
    });

    observer.observe(targetRoot, { childList: true, subtree: true });
  }
}

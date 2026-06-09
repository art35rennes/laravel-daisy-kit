export default function init(root) {
  const links = Array.from(root.querySelectorAll('a[href^="#"]'));

  if (!('IntersectionObserver' in window) || links.length === 0) {
    return;
  }

  const targets = new Map();

  links.forEach((link) => {
    const id = link.getAttribute('href')?.slice(1);
    const target = id ? document.getElementById(id) : null;

    if (target) {
      targets.set(target, link);
    }
  });

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      const link = targets.get(entry.target);

      if (!link || !entry.isIntersecting) {
        return;
      }

      links.forEach((item) => item.classList.remove('menu-active', 'font-semibold'));
      link.classList.add('menu-active', 'font-semibold');
    });
  }, { rootMargin: '0px 0px -70% 0px', threshold: 0.1 });

  targets.forEach((_, target) => observer.observe(target));
}

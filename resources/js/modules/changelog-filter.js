export default function init(container, options = {}) {
  const searchInput = container.querySelector('[data-changelog-search]');
  const filterInputs = Array.from(container.querySelectorAll('input[name="changelog-filter"]'));
  const allTypesLabel = String(options.allTypesLabel || '').toLowerCase();

  function filterChangelog() {
    const searchTerm = searchInput?.value.toLowerCase() || '';
    const selectedFilter = filterInputs.find((input) => input.checked)?.ariaLabel?.toLowerCase() || '';
    let visibleCount = 0;

    container.querySelectorAll('.changelog-version-item').forEach((versionItem) => {
      let versionVisible = false;

      versionItem.querySelectorAll('.changelog-change-item').forEach((changeItem) => {
        const description = changeItem.textContent.toLowerCase();
        const typeBadge = changeItem.querySelector('.badge')?.textContent.toLowerCase() || '';
        const matchesSearch = !searchTerm || description.includes(searchTerm);
        const matchesFilter = !selectedFilter || selectedFilter === allTypesLabel || typeBadge.includes(selectedFilter);
        const visible = matchesSearch && matchesFilter;

        changeItem.hidden = !visible;
        versionVisible = versionVisible || visible;
      });

      versionItem.hidden = !versionVisible;

      if (versionVisible) {
        visibleCount += 1;
      }
    });

    const emptyState = container.querySelector('[data-changelog-empty]');

    if (emptyState) {
      emptyState.hidden = visibleCount > 0;
    }
  }

  searchInput?.addEventListener('input', filterChangelog);
  filterInputs.forEach((input) => input.addEventListener('change', filterChangelog));
  filterChangelog();
}

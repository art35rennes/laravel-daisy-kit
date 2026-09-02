# Tree

`x-daisy-kit::tree` is a hierarchical selector, not a file editor. Import `@daisy-kit/tree.js`
and `@daisy-kit/tree.css` explicitly from the Composer package's Vite alias. No other module or
global is required. The host compiles DaisyUI/Tailwind; the component follows the active theme.

```blade
<x-daisy-kit::tree
    :items="$areas"
    label="Project areas"
    :multiple="true"
    :value="['projects-read']"
    name="areas"
    value-mode="leaves"
    :searchable="true"
    :highlight-matches="true"
    search-mode="manual"
    persistence-key="project-42-areas"
/>
```

## Data and selection

Items have a unique string/integer `id`, a text `label`, optional `description`, `badge`, `disabled`,
`expanded`, nested `children`, and a lazy `source` URL. Integers normalize to strings. Duplicate ids,
unsafe URLs, invalid settings and malformed payloads are rejected. Remote results cannot reparent
existing ids. Disabled subtrees cannot be selected. With `disabled=true`, the initial value remains
visible, but the hidden form field is disabled and commands cannot change the value.

Single selection is the default. In multiple mode, `valueMode="leaves"` submits enabled loaded
leaves; selecting a parent propagates to its known descendants. A partially loaded parent is never
reported as fully selected. An unloaded branch must first be expanded in leaf mode.
`valueMode="selected-roots"` compresses fully selected branches and permits selecting an unloaded
branch: that id represents the subtree, including children loaded later. Deselecting a loaded child
splits the selection into remaining sibling subtrees. Applications must validate authorization
server-side; disabled presentation is not an access-control boundary.

The exact `name` carries one ordered JSON array in both modes (`[]`, `["id"]`, `["id1","id2"]`).
The facade returns a scalar/null in single mode and an array in multiple mode. Selection summaries
count submitted ids, not an invented count of unknown remote descendants. Hidden includes selected
ids outside the visible search or beneath collapsed branches. "Select loaded results" selects
visible complete results. In `selected-roots` mode, a visible complete branch with no visible
descendants represents its known subtree. It never selects a partially loaded branch or fetches the
full tree.

## Search, loading and persistence

`searchMode="auto"` is the default; `searchDebounce=200`, `searchMin=0`, `searchMatch="includes"`.
Choose `searchMatch="fuzzy"` to use Match Sorter without changing sibling order. Manual mode stages
the query until Search, Enter or `applySearch()`. `highlightMatches=true` marks matched characters
in standard-rendered labels and descriptions. Clear search restores pre-search expansion.

`searchSource` receives GET requests with `searchParam="query"`. Search and lazy endpoints return
`{ "items": [...] }`. Lazy responses may add `nextCursor`; Tree then renders an accessible Load more
action and sends that cursor only when `loadMore(id)` is called. The page is appended to that branch.
A response without `nextCursor` completes the branch. Search results include ancestor paths, with
`source` retained for partial
branches. Search results merge by id into the canonical tree, so hidden selections are retained.
Lazy responses complete the immediate children of the requested branch; successful empty responses
are cached. Reload replaces those immediate children and removes selected ids that disappeared.
Branch errors display a retry action; search errors retain the previous result and allow retry.
Superseded searches and unmounted instances ignore late responses.

Endpoints belong to the application. Spatie Query Builder can filter their underlying queries,
but the application must still provide the ancestor paths and `{ items: [...] }` envelope.

For application-specific client filtering, call `setFilter(predicate)`. The predicate receives a
frozen detached snapshot `{ id, parentId, label, description, badge, disabled, loaded, hasMore }` for
each loaded node. Returning true retains that node and its ancestors. It composes with applied search
and never serializes executable code into Blade configuration. `clearFilter()` removes it. Use server
endpoints instead for unloaded or very large datasets.

`initialExpandPaths` is an array of root-to-node id arrays. It hydrates ancestors as needed.
`persistenceKey` opts into instance-scoped local storage for values, expanded ids and known paths.
Use a key scoped to user/record. Explicit `value`, including null/empty, overrides stored selection.
Search-driven expansion is temporary. Storage unavailability does not prevent interaction.

## Custom presentation and translations

`nodeView="your-view-name"` renders a Blade view with `$node` into an inert template per initial
node. Use text, descriptions, badges, icons and layout markup; the package owns controls and focus.
Scripts, styles, interactive controls and executable attributes are rejected. Escape application
data with ordinary Blade `{{ }}`. Remote JSON never becomes HTML; remotely loaded nodes use the
standard description/badge presentation. No runtime template function is serialized into JSON.

All labels come from `daisy-kit::tree` (English/French). `labels` overrides individual keys per
instance, including `summary`, `results`, `expand`, `collapse`, `retry`, `search`, and `applySearch`.

## Facade

```js
import { getInstance } from '@daisy-kit/tree.js';
const tree = getInstance(document.querySelector('#project-areas'));
tree.setSearch('Operations');
await tree.applySearch();
await tree.expandPath(['operations', 'reports']);
tree.setValue(['report-read']);
```

Getters return detached values: `getValue()` and `getState()` (`value`, `values`, `expandedIds`,
`visibleIds`, `query`, `searchDraft`, `loadingIds`, `searching`, `selection { total, visible, hidden }`).
Synchronous commands return boolean: `setValue`, `clear`, `collapse`, `focus`, `setSearch`,
`clearSearch`, `setFilter`, `clearFilter`, `expandAll`, `collapseAll`, `selectVisible`. Asynchronous
commands return `Promise<boolean>`: `expand`, `expandPath`, `applySearch`, `loadMore`, `reloadBranch`.
`expandAll` opens known branches without fetching; `expandPath` only loads ancestors on its path.
Invalid targets and no-ops return false. Repeated `expand` of an open branch returns true.

Events bubble from the instance under `daisy-kit:tree:`:

- `change { value, values }`, followed by native input/change on the hidden field;
- `expanded` / `collapsed { id, label }`;
- `search { query }` for applied/cleared searches;
- `filtered { active, visibleIds }` when a custom predicate is installed or cleared;
- `loading { loading, id?, query? }`;
- `error { code, message, id?, query? }` for operational failures.

Focus and selection are separate. Arrow keys navigate and expand/collapse; Home/End move within
visible nodes; Enter/Space select; character typing jumps by label. Pointer disclosure never
changes selection. Retry is also reachable through ArrowRight on a failed branch.

The internal Workbench `/tree?lang=fr&theme=light` demonstrates four normal application scenarios
under strict CSP. Use `theme=dark` for dark mode. No editor, drag-and-drop or virtualization is
included; lazy branch pagination bounds large result sets.

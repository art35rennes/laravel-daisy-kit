<!-- TreeView -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">TreeView</h2>
    <div class="grid md:grid-cols-2 gap-6 items-start">
        <div class="space-y-3">
            <div class="text-sm opacity-70">Sélection simple</div>
            <x-daisy::ui.tree-view id="demoTreeSingle" selection="single" :persist="true" controlSize="xs" :data="[
                ['id' => 'root', 'label' => 'Racine', 'expanded' => true, 'children' => [
                    ['id' => 'a', 'label' => 'Dossier A', 'children' => [
                        ['id' => 'a1', 'label' => 'Fichier A1'],
                        ['id' => 'a2', 'label' => 'Fichier A2'],
                    ]],
                    ['id' => 'b', 'label' => 'Dossier B', 'lazy' => true],
                    ['id' => 'c', 'label' => 'Fichier C'],
                ]],
            ]" />
            <div class="text-xs opacity-70">Événements: <code>tree:select</code>, <code>tree:lazy</code></div>
        </div>

        <div class="space-y-3">
            <div class="text-sm opacity-70">Sélection multiple</div>
            <x-daisy::ui.tree-view id="demoTreeMulti" selection="multiple" :persist="true" controlSize="xs" :data="[
                ['id' => '1', 'label' => 'Projets', 'expanded' => true, 'children' => [
                    ['id' => '1-1', 'label' => 'Kit UI', 'children' => [
                        ['id' => '1-1-1', 'label' => 'Roadmap.md'],
                        ['id' => '1-1-2', 'label' => 'Changelog.md (disable)', 'disabled' => true],
                    ]],
                    ['id' => '1-2', 'label' => 'Site (disable)', 'disabled' => true, 'children' => [
                        ['id' => '1-2-1', 'label' => 'Home.vue'],
                        ['id' => '1-2-2', 'label' => 'About.vue'],
                    ]],
                    ['id' => '1-3', 'label' => 'Sandbox (mixed au chargement)', 'children' => [
                        ['id' => '1-3-1', 'label' => 'Draft.md', 'selected' => true],
                        ['id' => '1-3-2', 'label' => 'Notes.md', 'selected' => false],
                    ]],
                ]],
            ]" />
        </div>
    </div>

    <div class="divider"></div>
    <div class="space-y-2">
        <div class="flex gap-2">
            <button id="btnReadSelected" class="btn btn-primary btn-sm">Lire la sélection (multi)</button>
            <button id="btnExpandB" class="btn btn-ghost btn-sm">Développer B (lazy)</button>
        </div>
        <pre id="selectedOutput" class="mockup-code w-full"><code></code></pre>
    </div>

    <script>
    (function(){
        document.addEventListener('DOMContentLoaded', () => {
            const single = document.getElementById('demoTreeSingle');
            const multi = document.getElementById('demoTreeMulti');
            const out = document.getElementById('selectedOutput')?.querySelector('code');

            if (single) {
                single.addEventListener('tree:select', () => {});
                single.addEventListener('tree:lazy', (e) => {
                    const { li } = e.detail;
                    const group = li.querySelector(':scope > ul[role="group"]');
                    if (!group) return;
                    group.innerHTML = '';
                    const mk = (id, label) => `<li role="treeitem" aria-level="2" aria-expanded="false" aria-selected="false" data-id="${id}"><div class="flex items-center gap-2 px-2 py-1 rounded hover:bg-base-200"><span class="inline-block w-6"></span><span class="flex-1 cursor-default select-none" data-label="1">${label}</span></div></li>`;
                    group.insertAdjacentHTML('beforeend', mk('b1','Fichier B1'));
                    group.insertAdjacentHTML('beforeend', mk('b2','Fichier B2'));
                    li.setAttribute('aria-expanded', 'true');
                    const btn = li.querySelector('[data-toggle]');
                    if (btn) {
                        const c = btn.querySelector('[data-icon-collapsed]');
                        const e2 = btn.querySelector('[data-icon-expanded]');
                        if (c) c.classList.add('hidden');
                        if (e2) e2.classList.remove('hidden');
                    }
                });
            }

            if (multi) {
                multi.addEventListener('tree:select', () => {
                    const ids = window.DaisyTreeView.getSelected(multi);
                    if (out) out.textContent = JSON.stringify(ids);
                });
            }

            const btnRead = document.getElementById('btnReadSelected');
            if (btnRead && multi) btnRead.addEventListener('click', () => {
                const ids = window.DaisyTreeView.getSelected(multi);
                if (out) out.textContent = JSON.stringify(ids);
            });

            const btnExpandB = document.getElementById('btnExpandB');
            if (btnExpandB && single) btnExpandB.addEventListener('click', () => {
                if (single.__treeApi) single.__treeApi.expand('b');
            });
        });
    })();
    </script>
</section>



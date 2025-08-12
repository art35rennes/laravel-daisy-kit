<!-- Code Editor -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Code Editor</h2>
    <div class="grid gap-6 items-start">
        <div class="space-y-3">
            <div class="text-sm opacity-70">JSON</div>
            <x-daisy::ui.code-editor id="demoCodeJson" language="json" :showToolbar="true" value='{"title":"Sample","users":[{"id":1,"name":"Alice"},{"id":2,"name":"Bob"}]}' height="320px" />
        </div>
    </div>
    <div class="divider"></div>
    <div class="flex flex-wrap gap-2">
        <button id="codeFoldAll" class="btn btn-sm">Plier tout</button>
        <button id="codeUnfoldAll" class="btn btn-sm">Déplier tout</button>
        <button id="codeFormat" class="btn btn-sm">Formatter</button>
        <button id="codeCopy" class="btn btn-sm">Copier</button>
    </div>
    <pre class="mockup-code"><code id="codeChangeOut"></code></pre>
    <script>
    (function(){
        document.addEventListener('DOMContentLoaded', () => {
            const js = document.getElementById('demoCodeJson');
            const out = document.getElementById('codeChangeOut');
            if (js) {
                js.addEventListener('code:change', (e) => {
                    if (out) out.textContent = e.detail.value;
                });
            }
            const bind = (id, cb) => {
                const el = document.getElementById(id);
                if (el && js) el.addEventListener('click', () => cb(js));
            };
            bind('codeFoldAll', (el) => window.DaisyCodeEditor.foldAll(el));
            bind('codeUnfoldAll', (el) => window.DaisyCodeEditor.unfoldAll(el));
            bind('codeFormat', (el) => window.DaisyCodeEditor.format(el));
            bind('codeCopy', (el) => window.DaisyCodeEditor.copy(el));
        });
    })();
    </script>
</section>



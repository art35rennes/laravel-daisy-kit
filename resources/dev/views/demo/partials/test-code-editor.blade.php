<!-- Code Editor -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Code Editor</h2>
    <div class="space-y-3">
        <div class="text-sm opacity-70">Disposition à hauteur fixe (2 colonnes)</div>
        <div class="grid md:grid-cols-2 gap-6">
            <!-- Colonne gauche: JSON (60% haut) + Résultat (40% bas) -->
            <div class="grid gap-6" style="grid-template-rows: 60vh 40vh;">
                <x-daisy::ui.code-editor id="jsonInput" language="json" :showToolbar="true" height="100%" value='{
  "products": [
    {"id": 1, "name": "Keyboard", "price": 49.9},
    {"id": 2, "name": "Mouse", "price": 29.9}
  ]
}' />
                <x-daisy::ui.code-editor id="jsonResult" language="json" :showToolbar="true" height="100%" readonly value='{}' />
            </div>
            <!-- Colonne droite: Expression / Script -->
            <x-daisy::ui.code-editor id="jsonExpr" language="javascript" :showToolbar="true" height="100vh" value='// Expression / script
// Filtrer les produits > 40
const data = JSON.parse(json);
const expensive = data.products.filter(p => p.price > 40);
result = { expensive };' />
        </div>
        <div class="text-right">
            <button id="evaluateJSON" class="btn btn-primary">Évaluer</button>
        </div>
    </div>
    <script>
    (function(){
        document.addEventListener('DOMContentLoaded', () => {
            const elJson = document.getElementById('jsonInput');
            const elExpr = document.getElementById('jsonExpr');
            const elRes  = document.getElementById('jsonResult');
            const run = () => {
                try {
                    const json = window.DaisyCodeEditor.getValue(elJson);
                    const expr = window.DaisyCodeEditor.getValue(elExpr);
                    const fn = new Function('json', 'result', expr + '\nreturn typeof result!=="undefined" ? result : null;');
                    const out = fn(json, null);
                    const pretty = JSON.stringify(out, null, 2);
                    window.DaisyCodeEditor.setValue(elRes, pretty);
                } catch (e) {
                    window.DaisyCodeEditor.setValue(elRes, JSON.stringify({ error: String(e.message || e) }, null, 2));
                }
            };
            const btn = document.getElementById('evaluateJSON');
            if (btn) btn.addEventListener('click', run);
        });
    })();
    </script>
</section>



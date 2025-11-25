<!-- Sign -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Sign (Signature)</h2>
    <p class="text-sm opacity-70">Composant de signature responsive avec support tactile et souris</p>
    
    <div class="space-y-6">
        <!-- Signature par défaut -->
        <div class="form-control">
            <x-daisy::ui.advanced.label value="Signature par défaut" />
            <x-daisy::ui.inputs.sign name="signature1" />
        </div>

        <!-- Signature avec dimensions personnalisées -->
        <div class="form-control">
            <x-daisy::ui.advanced.label value="Signature personnalisée (600x300)" />
            <x-daisy::ui.inputs.sign 
                name="signature2" 
                :width="600" 
                :height="300"
                pen-color="#0066cc" />
        </div>

        <!-- Signature responsive -->
        <div class="form-control">
            <x-daisy::ui.advanced.label value="Signature responsive (s'adapte à la largeur)" />
            <x-daisy::ui.inputs.sign 
                name="signature3" 
                :width="400" 
                :height="200"
                :responsive="true" />
        </div>

        <!-- Signature sans boutons d'action -->
        <div class="form-control">
            <x-daisy::ui.advanced.label value="Signature sans boutons d'action" />
            <x-daisy::ui.inputs.sign 
                name="signature4" 
                :show-actions="false" />
        </div>

        <!-- Signature avec format SVG -->
        <div class="form-control">
            <x-daisy::ui.advanced.label value="Signature avec téléchargement SVG" />
            <x-daisy::ui.inputs.sign 
                name="signature5" 
                download-format="svg"
                download-filename="ma-signature" />
        </div>

        <!-- Signature désactivée -->
        <div class="form-control">
            <x-daisy::ui.advanced.label value="Signature désactivée" />
            <x-daisy::ui.inputs.sign 
                name="signature6" 
                :disabled="true" />
        </div>
    </div>
</section>


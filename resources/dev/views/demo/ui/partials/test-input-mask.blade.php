<!-- Input Mask -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Input Mask</h2>
    <p class="opacity-70">Masquage/obfuscation léger. Utilisez désormais les props Blade du composant <code>x-daisy::ui.inputs.input</code> (rétrocompatible avec les data-attributes).</p>
    <div class="grid md:grid-cols-2 gap-6 items-start">
        <div class="space-y-3">
            <div class="text-sm opacity-70">Obfuscation: tout masqué</div>
            <x-daisy::ui.inputs.input :obfuscate="true" obfuscateChar="•" />
        </div>

        <div class="space-y-3">
            <div class="text-sm opacity-70">Obfuscation partielle: garder les 4 derniers</div>
            <x-daisy::ui.inputs.input :obfuscate="true" obfuscateChar="*" :obfuscateKeepEnd="4" value="1234567890" />
        </div>

        <div class="space-y-3">
            <div class="text-sm opacity-70">Saisie mot de passe obfusquée</div>
            <x-daisy::ui.inputs.input type="text" placeholder="Mot de passe" :obfuscate="true" obfuscateChar="*" />
        </div>

        <div class="space-y-3">
            <div class="text-sm opacity-70">Numéro de carte (afficher les 4 derniers)</div>
            <x-daisy::ui.inputs.input type="text" value="4242424242424242" :obfuscate="true" :obfuscateKeepEnd="4" />
        </div>
    </div>
</section>



<!-- Radio -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Radio</h2>
    <div class="space-y-6">
        <!-- Basique -->
        <x-daisy::ui.fieldset legend="Basique">
            <div class="flex flex-wrap items-center gap-4">
                <x-daisy::ui.radio name="r1" value="a" :checked="true" />
                <x-daisy::ui.radio name="r1" value="b" />
            </div>
        </x-daisy::ui.fieldset>

        <!-- Couleurs -->
        <x-daisy::ui.fieldset legend="Couleurs">
            <div class="flex flex-wrap items-center gap-4">
                <x-daisy::ui.radio name="r2" value="n" color="neutral" :checked="true" />
                <x-daisy::ui.radio name="r2" value="p" color="primary" />
                <x-daisy::ui.radio name="r2" value="s" color="secondary" />
                <x-daisy::ui.radio name="r2" value="a" color="accent" />
                <x-daisy::ui.radio name="r2" value="i" color="info" />
                <x-daisy::ui.radio name="r2" value="su" color="success" />
                <x-daisy::ui.radio name="r2" value="w" color="warning" />
                <x-daisy::ui.radio name="r2" value="e" color="error" />
            </div>
        </x-daisy::ui.fieldset>

        <!-- Tailles -->
        <x-daisy::ui.fieldset legend="Tailles">
            <div class="flex flex-wrap items-center gap-4">
                <x-daisy::ui.radio name="r3" value="xs" size="xs" :checked="true" />
                <x-daisy::ui.radio name="r3" value="sm" size="sm" />
                <x-daisy::ui.radio name="r3" value="md" size="md" />
                <x-daisy::ui.radio name="r3" value="lg" size="lg" />
                <x-daisy::ui.radio name="r3" value="xl" size="xl" />
            </div>
        </x-daisy::ui.fieldset>

        <!-- Désactivé -->
        <x-daisy::ui.fieldset legend="Désactivé">
            <div class="flex flex-wrap items-center gap-4">
                <x-daisy::ui.radio name="r4" value="d1" :disabled="true" :checked="true" />
                <x-daisy::ui.radio name="r4" value="d2" :disabled="true" />
            </div>
        </x-daisy::ui.fieldset>

        <!-- Uncheckable -->
        <x-daisy::ui.fieldset legend="Décochable (uncheckable)">
            <div class="flex flex-wrap items-center gap-4">
                <x-daisy::ui.radio name="r5" value="u1" uncheckable :checked="true" />
                <x-daisy::ui.radio name="r5" value="u2" uncheckable />
            </div>
            <div class="text-xs opacity-70 mt-2">Permet de décocher un radio déjà coché (comportement non standard).</div>
        </x-daisy::ui.fieldset>
    </div>
</section>

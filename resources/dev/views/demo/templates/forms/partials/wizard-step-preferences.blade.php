<div class="space-y-4">
    <h3 class="text-lg font-semibold">Préférences</h3>
    <x-daisy::ui.partials.form-field name="newsletter" label="Abonnements">
        <div class="flex items-center gap-3">
            <x-daisy::ui.inputs.checkbox name="newsletter" :checked="old('newsletter')" />
            <span>Recevoir la newsletter</span>
        </div>
    </x-daisy::ui.partials.form-field>
    <x-daisy::ui.partials.form-field name="notifications" label="Notifications">
        <div class="flex items-center gap-3">
            <x-daisy::ui.inputs.toggle name="notifications" :checked="old('notifications', true)" />
            <span>Activer les notifications</span>
        </div>
    </x-daisy::ui.partials.form-field>
    <x-daisy::ui.partials.form-field name="theme" label="Thème">
        <x-daisy::ui.inputs.select name="theme" :value="old('theme', 'light')">
            <option value="light">Clair</option>
            <option value="dark">Sombre</option>
            <option value="auto">Automatique</option>
        </x-daisy::ui.inputs.select>
    </x-daisy::ui.partials.form-field>
</div>


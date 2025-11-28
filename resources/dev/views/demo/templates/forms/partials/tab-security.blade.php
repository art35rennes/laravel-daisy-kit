<div class="space-y-4">
    <x-daisy::ui.partials.form-field name="password" label="Nouveau mot de passe">
        <x-daisy::ui.inputs.input name="password" type="password" placeholder="••••••••" />
    </x-daisy::ui.partials.form-field>
    <x-daisy::ui.partials.form-field name="password_confirmation" label="Confirmer le mot de passe">
        <x-daisy::ui.inputs.input name="password_confirmation" type="password" placeholder="••••••••" />
    </x-daisy::ui.partials.form-field>
    <div class="alert alert-info">
        <x-bi-info-circle class="w-5 h-5" />
        <span>Laissez vide pour conserver le mot de passe actuel</span>
    </div>
</div>


    <x-daisy::ui.partials.form-field name="email" label="Email" hint="Used for login">
        <x-daisy::ui.inputs.input name="email" :error="$errors->first('email')" />
    </x-daisy::ui.partials.form-field>
<div class="space-y-4">
    <x-daisy::ui.partials.form-field name="newsletter" label="Newsletter">
        <div class="flex items-center gap-3">
            <x-daisy::ui.inputs.checkbox name="newsletter" :checked="old('newsletter', true)" />
            <span>Recevoir la newsletter par email</span>
        </div>
    </x-daisy::ui.partials.form-field>
    <x-daisy::ui.partials.form-field name="email_notifications" label="Notifications email">
        <div class="flex items-center gap-3">
            <x-daisy::ui.inputs.toggle name="email_notifications" :checked="old('email_notifications', true)" />
            <span>Activer les notifications par email</span>
        </div>
    </x-daisy::ui.partials.form-field>
    <x-daisy::ui.partials.form-field name="push_notifications" label="Notifications push">
        <div class="flex items-center gap-3">
            <x-daisy::ui.inputs.toggle name="push_notifications" :checked="old('push_notifications', false)" />
            <span>Activer les notifications push</span>
        </div>
    </x-daisy::ui.partials.form-field>
</div>


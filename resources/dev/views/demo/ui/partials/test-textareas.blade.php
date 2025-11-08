<!-- Textareas -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Textareas</h2>
    <div class="space-y-3">
        <div class="grid md:grid-cols-3 gap-3">
            <x-daisy::ui.inputs.textarea rows="3" placeholder="Default" />
            <x-daisy::ui.inputs.textarea rows="3" variant="ghost" placeholder="Ghost" />
            <x-daisy::ui.inputs.textarea rows="3" color="primary" placeholder="Primary" />
        </div>
        <div class="grid grid-cols-5 gap-3">
            <x-daisy::ui.inputs.textarea size="xs" rows="2" placeholder="Xsmall" />
            <x-daisy::ui.inputs.textarea size="sm" rows="2" placeholder="Small" />
            <x-daisy::ui.inputs.textarea size="md" rows="3" placeholder="Medium" />
            <x-daisy::ui.inputs.textarea size="lg" rows="4" placeholder="Large" />
            <x-daisy::ui.inputs.textarea size="xl" rows="5" placeholder="Xlarge" />
        </div>
        <x-daisy::ui.inputs.textarea placeholder="Disabled" :disabled="true" />
    </div>
</section>



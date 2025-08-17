<!-- Textareas -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Textareas</h2>
    <div class="space-y-3">
        <div class="grid md:grid-cols-3 gap-3">
            <x-daisy::ui.textarea rows="3" placeholder="Default" />
            <x-daisy::ui.textarea rows="3" variant="ghost" placeholder="Ghost" />
            <x-daisy::ui.textarea rows="3" color="primary" placeholder="Primary" />
        </div>
        <div class="grid grid-cols-5 gap-3">
            <x-daisy::ui.textarea size="xs" rows="2" placeholder="Xsmall" />
            <x-daisy::ui.textarea size="sm" rows="2" placeholder="Small" />
            <x-daisy::ui.textarea size="md" rows="3" placeholder="Medium" />
            <x-daisy::ui.textarea size="lg" rows="4" placeholder="Large" />
            <x-daisy::ui.textarea size="xl" rows="5" placeholder="Xlarge" />
        </div>
        <x-daisy::ui.textarea placeholder="Disabled" :disabled="true" />
    </div>
</section>



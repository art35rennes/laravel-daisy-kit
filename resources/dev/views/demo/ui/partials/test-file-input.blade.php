<!-- File Input -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">File Input</h2>
    <div class="grid md:grid-cols-3 gap-6">
        <!-- Default -->
        <x-daisy::ui.file-input />
        <!-- Ghost -->
        <x-daisy::ui.file-input variant="ghost" />
        <!-- Colors -->
        <x-daisy::ui.file-input color="primary" />
        <x-daisy::ui.file-input color="secondary" />
        <x-daisy::ui.file-input color="accent" />
        <x-daisy::ui.file-input color="neutral" />
        <x-daisy::ui.file-input color="info" />
        <x-daisy::ui.file-input color="success" />
        <x-daisy::ui.file-input color="warning" />
        <x-daisy::ui.file-input color="error" />
        <!-- Sizes -->
        <x-daisy::ui.file-input size="xs" />
        <x-daisy::ui.file-input size="sm" />
        <x-daisy::ui.file-input size="md" />
        <x-daisy::ui.file-input size="lg" />
        <x-daisy::ui.file-input size="xl" />
    </div>

    <div class="divider">Drag & Drop + Preview</div>
    <div class="grid md:grid-cols-2 gap-6">
        <x-daisy::ui.file-input :dragdrop="true" :preview="true" :multiple="true" />
        <x-daisy::ui.file-input :dragdrop="true" :preview="true" />
    </div>
</section>



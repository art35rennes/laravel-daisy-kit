<!-- Divider -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Divider</h2>
    <div class="space-y-6">
        <!-- Vertical (défaut) avec texte -->
        <div class="flex w-full flex-col">
            <div class="card bg-base-300 rounded-box grid h-20 place-items-center">content</div>
            <x-daisy::ui.layout.divider>OR</x-daisy::ui.layout.divider>
            <div class="card bg-base-300 rounded-box grid h-20 place-items-center">content</div>
        </div>

        <!-- Horizontal -->
        <div class="flex w-full">
            <div class="card bg-base-300 rounded-box grid h-20 grow place-items-center">content</div>
            <x-daisy::ui.layout.divider :horizontal="true">OR</x-daisy::ui.layout.divider>
            <div class="card bg-base-300 rounded-box grid h-20 grow place-items-center">content</div>
        </div>

        <!-- Sans texte -->
        <div class="flex w-full flex-col">
            <div class="card bg-base-300 rounded-box grid h-20 place-items-center">content</div>
            <x-daisy::ui.layout.divider />
            <div class="card bg-base-300 rounded-box grid h-20 place-items-center">content</div>
        </div>

        <!-- Responsive: lg horizontal -->
        <div class="flex w-full flex-col lg:flex-row">
            <div class="card bg-base-300 rounded-box grid h-32 grow place-items-center">content</div>
            <x-daisy::ui.layout.divider horizontalAt="lg">OR</x-daisy::ui.layout.divider>
            <div class="card bg-base-300 rounded-box grid h-32 grow place-items-center">content</div>
        </div>

        <!-- Couleurs et placements -->
        <div class="flex w-full flex-col">
            <x-daisy::ui.layout.divider>Default</x-daisy::ui.layout.divider>
            <x-daisy::ui.layout.divider color="neutral">Neutral</x-daisy::ui.layout.divider>
            <x-daisy::ui.layout.divider color="primary">Primary</x-daisy::ui.layout.divider>
            <x-daisy::ui.layout.divider color="secondary">Secondary</x-daisy::ui.layout.divider>
            <x-daisy::ui.layout.divider color="accent">Accent</x-daisy::ui.layout.divider>
            <x-daisy::ui.layout.divider color="success">Success</x-daisy::ui.layout.divider>
            <x-daisy::ui.layout.divider color="warning">Warning</x-daisy::ui.layout.divider>
            <x-daisy::ui.layout.divider color="info">Info</x-daisy::ui.layout.divider>
            <x-daisy::ui.layout.divider color="error">Error</x-daisy::ui.layout.divider>
        </div>
        <div class="flex w-full flex-col">
            <x-daisy::ui.layout.divider position="start">Start</x-daisy::ui.layout.divider>
            <x-daisy::ui.layout.divider>Default</x-daisy::ui.layout.divider>
            <x-daisy::ui.layout.divider position="end">End</x-daisy::ui.layout.divider>
        </div>
    </div>
</section>



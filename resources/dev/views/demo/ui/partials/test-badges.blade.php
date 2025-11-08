<!-- Badges -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Badges</h2>
    <div class="space-y-2">
        <div class="flex flex-wrap gap-2 items-center">
            <x-daisy::ui.data-display.badge color="neutral">Neutral</x-daisy::ui.data-display.badge>
            <x-daisy::ui.data-display.badge color="primary">Primary</x-daisy::ui.data-display.badge>
            <x-daisy::ui.data-display.badge color="secondary">Secondary</x-daisy::ui.data-display.badge>
            <x-daisy::ui.data-display.badge color="accent">Accent</x-daisy::ui.data-display.badge>
            <x-daisy::ui.data-display.badge color="info">Info</x-daisy::ui.data-display.badge>
            <x-daisy::ui.data-display.badge color="success">Success</x-daisy::ui.data-display.badge>
            <x-daisy::ui.data-display.badge color="warning">Warning</x-daisy::ui.data-display.badge>
            <x-daisy::ui.data-display.badge color="error">Error</x-daisy::ui.data-display.badge>
        </div>
        <div class="flex flex-wrap gap-2 items-center">
            <x-daisy::ui.data-display.badge color="primary" variant="outline">Outline</x-daisy::ui.data-display.badge>
            <x-daisy::ui.data-display.badge color="primary" variant="dash">Dash</x-daisy::ui.data-display.badge>
            <x-daisy::ui.data-display.badge color="primary" variant="ghost">Ghost</x-daisy::ui.data-display.badge>
            <x-daisy::ui.data-display.badge color="success" variant="soft">Soft</x-daisy::ui.data-display.badge>
        </div>
        <div class="flex flex-wrap gap-2 items-center">
            <x-daisy::ui.data-display.badge size="xs">XS</x-daisy::ui.data-display.badge>
            <x-daisy::ui.data-display.badge size="sm">SM</x-daisy::ui.data-display.badge>
            <x-daisy::ui.data-display.badge size="md">MD</x-daisy::ui.data-display.badge>
            <x-daisy::ui.data-display.badge size="lg">LG</x-daisy::ui.data-display.badge>
            <x-daisy::ui.data-display.badge size="xl">XL</x-daisy::ui.data-display.badge>
        </div>
        <div class="space-y-1">
            <h3 class="text-xl font-semibold">Heading 1 <x-daisy::ui.data-display.badge size="xl">Badge</x-daisy::ui.data-display.badge></h3>
            <h4 class="text-lg font-semibold">Heading 2 <x-daisy::ui.data-display.badge size="lg">Badge</x-daisy::ui.data-display.badge></h4>
            <h5 class="text-base font-semibold">Heading 3 <x-daisy::ui.data-display.badge size="md">Badge</x-daisy::ui.data-display.badge></h5>
            <h6 class="text-sm font-semibold">Heading 4 <x-daisy::ui.data-display.badge size="sm">Badge</x-daisy::ui.data-display.badge></h6>
            <p class="text-xs">Paragraph <x-daisy::ui.data-display.badge size="xs">Badge</x-daisy::ui.data-display.badge></p>
        </div>
        <div class="flex items-center gap-3">
            <button class="btn">Inbox <x-daisy::ui.data-display.badge size="sm" class="ml-2">+99</x-daisy::ui.data-display.badge></button>
            <button class="btn">Inbox <x-daisy::ui.data-display.badge size="sm" color="secondary" class="ml-2">+99</x-daisy::ui.data-display.badge></button>
        </div>
    </div>
</section>



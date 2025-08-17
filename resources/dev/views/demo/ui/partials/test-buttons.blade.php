<!-- Buttons -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Buttons</h2>
    <div class="space-y-3">
        <div class="flex flex-wrap gap-2">
            <x-daisy::ui.button color="primary">Primary</x-daisy::ui.button>
            <x-daisy::ui.button color="secondary">Secondary</x-daisy::ui.button>
            <x-daisy::ui.button color="accent">Accent</x-daisy::ui.button>
            <x-daisy::ui.button color="neutral">Neutral</x-daisy::ui.button>
            <x-daisy::ui.button color="info">Info</x-daisy::ui.button>
            <x-daisy::ui.button color="success">Success</x-daisy::ui.button>
            <x-daisy::ui.button color="warning">Warning</x-daisy::ui.button>
            <x-daisy::ui.button color="error">Error</x-daisy::ui.button>
        </div>
        <div class="flex flex-wrap gap-2">
            <x-daisy::ui.button variant="outline" color="primary">Outline</x-daisy::ui.button>
            <x-daisy::ui.button variant="ghost" color="primary">Ghost</x-daisy::ui.button>
            <x-daisy::ui.button variant="link" color="primary">Link</x-daisy::ui.button>
            <x-daisy::ui.button variant="soft" color="primary">Soft</x-daisy::ui.button>
            <x-daisy::ui.button variant="dash" color="primary">Dash</x-daisy::ui.button>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <x-daisy::ui.button size="xs" color="primary">XS</x-daisy::ui.button>
            <x-daisy::ui.button size="sm" color="primary">SM</x-daisy::ui.button>
            <x-daisy::ui.button size="md" color="primary">MD</x-daisy::ui.button>
            <x-daisy::ui.button size="lg" color="primary">LG</x-daisy::ui.button>
            <x-daisy::ui.button size="xl" color="primary">XL</x-daisy::ui.button>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <x-daisy::ui.button class="btn-xs sm:btn-sm md:btn-md lg:btn-lg xl:btn-xl">Responsive</x-daisy::ui.button>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <x-daisy::ui.button color="primary" :loading="true">Loading</x-daisy::ui.button>
            <x-daisy::ui.button color="primary" :active="true">Active</x-daisy::ui.button>
            <x-daisy::ui.button color="primary" :noAnimation="true">No animation</x-daisy::ui.button>
            <x-daisy::ui.button disabled>Disabled</x-daisy::ui.button>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <x-daisy::ui.button color="primary" :wide="true">Wide</x-daisy::ui.button>
            <x-daisy::ui.button color="primary" :block="true">Block</x-daisy::ui.button>
            <x-daisy::ui.button color="primary" :circle="true">
                <x-slot:icon>
                    <x-bi-heart class="h-5 w-5" />
                </x-slot:icon>
            </x-daisy::ui.button>
            <x-daisy::ui.button color="primary" :square="true">
                <x-slot:icon>
                    <x-bi-x class="h-5 w-5" />
                </x-slot:icon>
            </x-daisy::ui.button>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <x-daisy::ui.button color="primary">
                <x-slot:icon>
                    <x-bi-arrow-right class="h-5 w-5" />
                </x-slot:icon>
                Icône à gauche
            </x-daisy::ui.button>
            <x-daisy::ui.button variant="link" color="primary">
                Icône à droite
                <x-slot:iconRight>
                    <x-bi-box-arrow-up-right class="h-5 w-5" />
                </x-slot:iconRight>
            </x-daisy::ui.button>
        </div>
    </div>
</section>



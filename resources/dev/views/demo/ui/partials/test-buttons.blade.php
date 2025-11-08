<!-- Buttons -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Buttons</h2>
    <div class="space-y-3">
        <div class="flex flex-wrap gap-2">
            <x-daisy::ui.inputs.button color="primary">Primary</x-daisy::ui.inputs.button>
            <x-daisy::ui.inputs.button color="secondary">Secondary</x-daisy::ui.inputs.button>
            <x-daisy::ui.inputs.button color="accent">Accent</x-daisy::ui.inputs.button>
            <x-daisy::ui.inputs.button color="neutral">Neutral</x-daisy::ui.inputs.button>
            <x-daisy::ui.inputs.button color="info">Info</x-daisy::ui.inputs.button>
            <x-daisy::ui.inputs.button color="success">Success</x-daisy::ui.inputs.button>
            <x-daisy::ui.inputs.button color="warning">Warning</x-daisy::ui.inputs.button>
            <x-daisy::ui.inputs.button color="error">Error</x-daisy::ui.inputs.button>
        </div>
        <div class="flex flex-wrap gap-2">
            <x-daisy::ui.inputs.button variant="outline" color="primary">Outline</x-daisy::ui.inputs.button>
            <x-daisy::ui.inputs.button variant="ghost" color="primary">Ghost</x-daisy::ui.inputs.button>
            <x-daisy::ui.inputs.button variant="link" color="primary">Link</x-daisy::ui.inputs.button>
            <x-daisy::ui.inputs.button variant="soft" color="primary">Soft</x-daisy::ui.inputs.button>
            <x-daisy::ui.inputs.button variant="dash" color="primary">Dash</x-daisy::ui.inputs.button>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <x-daisy::ui.inputs.button size="xs" color="primary">XS</x-daisy::ui.inputs.button>
            <x-daisy::ui.inputs.button size="sm" color="primary">SM</x-daisy::ui.inputs.button>
            <x-daisy::ui.inputs.button size="md" color="primary">MD</x-daisy::ui.inputs.button>
            <x-daisy::ui.inputs.button size="lg" color="primary">LG</x-daisy::ui.inputs.button>
            <x-daisy::ui.inputs.button size="xl" color="primary">XL</x-daisy::ui.inputs.button>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <x-daisy::ui.inputs.button class="btn-xs sm:btn-sm md:btn-md lg:btn-lg xl:btn-xl">Responsive</x-daisy::ui.inputs.button>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <x-daisy::ui.inputs.button color="primary" :loading="true">Loading</x-daisy::ui.inputs.button>
            <x-daisy::ui.inputs.button color="primary" :active="true">Active</x-daisy::ui.inputs.button>
            <x-daisy::ui.inputs.button color="primary" :noAnimation="true">No animation</x-daisy::ui.inputs.button>
            <x-daisy::ui.inputs.button disabled>Disabled</x-daisy::ui.inputs.button>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <x-daisy::ui.inputs.button color="primary" :wide="true">Wide</x-daisy::ui.inputs.button>
            <x-daisy::ui.inputs.button color="primary" :block="true">Block</x-daisy::ui.inputs.button>
            <x-daisy::ui.inputs.button color="primary" :circle="true">
                <x-slot:icon>
                    <x-bi-heart class="h-5 w-5" />
                </x-slot:icon>
            </x-daisy::ui.inputs.button>
            <x-daisy::ui.inputs.button color="primary" :square="true">
                <x-slot:icon>
                    <x-bi-x class="h-5 w-5" />
                </x-slot:icon>
            </x-daisy::ui.inputs.button>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <x-daisy::ui.inputs.button color="primary">
                <x-slot:icon>
                    <x-bi-arrow-right class="h-5 w-5" />
                </x-slot:icon>
                Icône à gauche
            </x-daisy::ui.inputs.button>
            <x-daisy::ui.inputs.button variant="link" color="primary">
                Icône à droite
                <x-slot:iconRight>
                    <x-bi-box-arrow-up-right class="h-5 w-5" />
                </x-slot:iconRight>
            </x-daisy::ui.inputs.button>
        </div>
    </div>
</section>



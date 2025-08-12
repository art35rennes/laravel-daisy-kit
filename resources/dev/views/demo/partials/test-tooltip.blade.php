<!-- Tooltip -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Tooltip</h2>
    <div class="flex flex-wrap items-center gap-4">
        <x-daisy::ui.tooltip text="hello"><x-daisy::ui.button>Hover me</x-daisy::ui.button></x-daisy::ui.tooltip>
        <x-daisy::ui.tooltip :open="true" position="top" text="Top"><x-daisy::ui.button>Top</x-daisy::ui.button></x-daisy::ui.tooltip>
        <x-daisy::ui.tooltip :open="true" position="bottom" text="Bottom"><x-daisy::ui.button>Bottom</x-daisy::ui.button></x-daisy::ui.tooltip>
        <x-daisy::ui.tooltip :open="true" position="left" text="Left"><x-daisy::ui.button>Left</x-daisy::ui.button></x-daisy::ui.tooltip>
        <x-daisy::ui.tooltip :open="true" position="right" text="Right"><x-daisy::ui.button>Right</x-daisy::ui.button></x-daisy::ui.tooltip>
        <x-daisy::ui.tooltip :open="true" color="neutral" text="neutral"><x-daisy::ui.button class="btn-neutral">neutral</x-daisy::ui.button></x-daisy::ui.tooltip>
        <x-daisy::ui.tooltip :open="true" color="primary" text="primary"><x-daisy::ui.button class="btn-primary">primary</x-daisy::ui.button></x-daisy::ui.tooltip>
        <x-daisy::ui.tooltip :open="true" color="secondary" text="secondary"><x-daisy::ui.button class="btn-secondary">secondary</x-daisy::ui.button></x-daisy::ui.tooltip>
        <x-daisy::ui.tooltip :open="true" color="accent" text="accent"><x-daisy::ui.button class="btn-accent">accent</x-daisy::ui.button></x-daisy::ui.tooltip>
        <x-daisy::ui.tooltip :open="true" color="info" text="info"><x-daisy::ui.button class="btn-info">info</x-daisy::ui.button></x-daisy::ui.tooltip>
        <x-daisy::ui.tooltip :open="true" color="success" text="success"><x-daisy::ui.button class="btn-success">success</x-daisy::ui.button></x-daisy::ui.tooltip>
        <x-daisy::ui.tooltip :open="true" color="warning" text="warning"><x-daisy::ui.button class="btn-warning">warning</x-daisy::ui.button></x-daisy::ui.tooltip>
        <x-daisy::ui.tooltip :open="true" color="error" text="error"><x-daisy::ui.button class="btn-error">error</x-daisy::ui.button></x-daisy::ui.tooltip>
        <x-daisy::ui.tooltip>
            <x-slot:contentSlot>
                <div class="animate-bounce text-orange-400 -rotate-10 text-2xl font-black">Wow!</div>
            </x-slot:contentSlot>
            <x-daisy::ui.button>Hover me</x-daisy::ui.button>
        </x-daisy::ui.tooltip>
    </div>
</section>



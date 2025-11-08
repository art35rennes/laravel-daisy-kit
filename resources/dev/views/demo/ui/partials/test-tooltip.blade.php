<!-- Tooltip -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Tooltip</h2>
    <div class="flex flex-wrap gap-4 items-center">
        <x-daisy::ui.overlay.tooltip text="hello">
            <x-daisy::ui.inputs.button>Hover me</x-daisy::ui.inputs.button>
        </x-daisy::ui.overlay.tooltip>

        <x-daisy::ui.overlay.tooltip open position="top" text="Top">
            <x-daisy::ui.inputs.button>Top</x-daisy::ui.inputs.button>
        </x-daisy::ui.overlay.tooltip>
        <x-daisy::ui.overlay.tooltip open position="bottom" text="Bottom">
            <x-daisy::ui.inputs.button>Bottom</x-daisy::ui.inputs.button>
        </x-daisy::ui.overlay.tooltip>
        <x-daisy::ui.overlay.tooltip open position="left" text="Left">
            <x-daisy::ui.inputs.button>Left</x-daisy::ui.inputs.button>
        </x-daisy::ui.overlay.tooltip>
        <x-daisy::ui.overlay.tooltip open position="right" text="Right">
            <x-daisy::ui.inputs.button>Right</x-daisy::ui.inputs.button>
        </x-daisy::ui.overlay.tooltip>

        <x-daisy::ui.overlay.tooltip open color="neutral" text="neutral">
            <x-daisy::ui.inputs.button color="neutral">neutral</x-daisy::ui.inputs.button>
        </x-daisy::ui.overlay.tooltip>
        <x-daisy::ui.overlay.tooltip open color="primary" text="primary">
            <x-daisy::ui.inputs.button color="primary">primary</x-daisy::ui.inputs.button>
        </x-daisy::ui.overlay.tooltip>
        <x-daisy::ui.overlay.tooltip open color="secondary" text="secondary">
            <x-daisy::ui.inputs.button color="secondary">secondary</x-daisy::ui.inputs.button>
        </x-daisy::ui.overlay.tooltip>
        <x-daisy::ui.overlay.tooltip open color="accent" text="accent">
            <x-daisy::ui.inputs.button>accent</x-daisy::ui.inputs.button>
        </x-daisy::ui.overlay.tooltip>
        <x-daisy::ui.overlay.tooltip open color="info" text="info">
            <x-daisy::ui.inputs.button color="info">info</x-daisy::ui.inputs.button>
        </x-daisy::ui.overlay.tooltip>
        <x-daisy::ui.overlay.tooltip open color="success" text="success">
            <x-daisy::ui.inputs.button color="success">success</x-daisy::ui.inputs.button>
        </x-daisy::ui.overlay.tooltip>
        <x-daisy::ui.overlay.tooltip open color="warning" text="warning">
            <x-daisy::ui.inputs.button color="warning">warning</x-daisy::ui.inputs.button>
        </x-daisy::ui.overlay.tooltip>
        <x-daisy::ui.overlay.tooltip open color="error" text="error">
            <x-daisy::ui.inputs.button color="error">error</x-daisy::ui.inputs.button>
        </x-daisy::ui.overlay.tooltip>
    </div>
</section>

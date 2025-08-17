<!-- Tooltip -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Tooltip</h2>
    <div class="flex flex-wrap gap-4 items-center">
        <x-daisy::ui.tooltip text="hello">
            <x-daisy::ui.button>Hover me</x-daisy::ui.button>
        </x-daisy::ui.tooltip>

        <x-daisy::ui.tooltip open position="top" text="Top">
            <x-daisy::ui.button>Top</x-daisy::ui.button>
        </x-daisy::ui.tooltip>
        <x-daisy::ui.tooltip open position="bottom" text="Bottom">
            <x-daisy::ui.button>Bottom</x-daisy::ui.button>
        </x-daisy::ui.tooltip>
        <x-daisy::ui.tooltip open position="left" text="Left">
            <x-daisy::ui.button>Left</x-daisy::ui.button>
        </x-daisy::ui.tooltip>
        <x-daisy::ui.tooltip open position="right" text="Right">
            <x-daisy::ui.button>Right</x-daisy::ui.button>
        </x-daisy::ui.tooltip>

        <x-daisy::ui.tooltip open color="neutral" text="neutral">
            <x-daisy::ui.button color="neutral">neutral</x-daisy::ui.button>
        </x-daisy::ui.tooltip>
        <x-daisy::ui.tooltip open color="primary" text="primary">
            <x-daisy::ui.button color="primary">primary</x-daisy::ui.button>
        </x-daisy::ui.tooltip>
        <x-daisy::ui.tooltip open color="secondary" text="secondary">
            <x-daisy::ui.button color="secondary">secondary</x-daisy::ui.button>
        </x-daisy::ui.tooltip>
        <x-daisy::ui.tooltip open color="accent" text="accent">
            <x-daisy::ui.button>accent</x-daisy::ui.button>
        </x-daisy::ui.tooltip>
        <x-daisy::ui.tooltip open color="info" text="info">
            <x-daisy::ui.button color="info">info</x-daisy::ui.button>
        </x-daisy::ui.tooltip>
        <x-daisy::ui.tooltip open color="success" text="success">
            <x-daisy::ui.button color="success">success</x-daisy::ui.button>
        </x-daisy::ui.tooltip>
        <x-daisy::ui.tooltip open color="warning" text="warning">
            <x-daisy::ui.button color="warning">warning</x-daisy::ui.button>
        </x-daisy::ui.tooltip>
        <x-daisy::ui.tooltip open color="error" text="error">
            <x-daisy::ui.button color="error">error</x-daisy::ui.button>
        </x-daisy::ui.tooltip>
    </div>
</section>
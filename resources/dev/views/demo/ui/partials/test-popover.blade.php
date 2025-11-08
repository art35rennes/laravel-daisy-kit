<!-- Popover -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Popover</h2>
    <div class="grid md:grid-cols-2 gap-6">
        <!-- Basique (click) -->
        <x-daisy::ui.overlay.popover title="Popover title">
            <x-slot:triggerSlot>
                <x-daisy::ui.inputs.button color="primary">Click to toggle popover</x-daisy::ui.inputs.button>
            </x-slot:triggerSlot>
            And here's some amazing content. It's very engaging. Right?
        </x-daisy::ui.overlay.popover>

        <!-- Directions -->
        <div class="flex flex-wrap items-center gap-3">
            <x-daisy::ui.overlay.popover position="top" trigger="click" title="Top">
                <x-slot:triggerSlot><x-daisy::ui.inputs.button>Top</x-daisy::ui.inputs.button></x-slot:triggerSlot>
                Vivamus sagittis lacus vel augue laoreet rutrum faucibus.
            </x-daisy::ui.overlay.popover>
            <x-daisy::ui.overlay.popover position="right" trigger="click" title="Right">
                <x-slot:triggerSlot><x-daisy::ui.inputs.button>Right</x-daisy::ui.inputs.button></x-slot:triggerSlot>
                Vivamus sagittis lacus vel augue laoreet rutrum faucibus.
            </x-daisy::ui.overlay.popover>
            <x-daisy::ui.overlay.popover position="bottom" trigger="click" title="Bottom">
                <x-slot:triggerSlot><x-daisy::ui.inputs.button>Bottom</x-daisy::ui.inputs.button></x-slot:triggerSlot>
                Vivamus sagittis lacus vel augue laoreet rutrum faucibus.
            </x-daisy::ui.overlay.popover>
            <x-daisy::ui.overlay.popover position="left" trigger="click" title="Left">
                <x-slot:triggerSlot><x-daisy::ui.inputs.button>Left</x-daisy::ui.inputs.button></x-slot:triggerSlot>
                Vivamus sagittis lacus vel augue laoreet rutrum faucibus.
            </x-daisy::ui.overlay.popover>
        </div>

        <!-- Trigger hover & focus -->
        <div class="flex flex-wrap items-center gap-3">
            <x-daisy::ui.overlay.popover trigger="hover" position="top" title="Hover popover">
                <x-slot:triggerSlot><x-daisy::ui.inputs.button variant="outline">Hover me</x-daisy::ui.inputs.button></x-slot:triggerSlot>
                Content when hovering.
            </x-daisy::ui.overlay.popover>
            <x-daisy::ui.overlay.popover trigger="focus" position="bottom" title="Focus popover">
                <x-slot:triggerSlot><x-daisy::ui.inputs.button>Focus me</x-daisy::ui.inputs.button></x-slot:triggerSlot>
                Content when focused.
            </x-daisy::ui.overlay.popover>
        </div>

        <!-- Avec flèche, header/footer slots -->
        <x-daisy::ui.overlay.popover :arrow="true">
            <x-slot:triggerSlot>
                <x-daisy::ui.inputs.button>With arrow</x-daisy::ui.inputs.button>
            </x-slot:triggerSlot>
            <x-slot:header>
                <div class="font-semibold">Header</div>
            </x-slot:header>
            <div>Some content with arrow and custom header/footer.</div>
            <x-slot:footer>
                <div class="text-xs opacity-70">Footer</div>
            </x-slot:footer>
        </x-daisy::ui.overlay.popover>
    </div>
</section>



<!-- Popover -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Popover</h2>
    <div class="grid md:grid-cols-2 gap-6">
        <!-- Basique (click) -->
        <x-daisy::ui.popover title="Popover title">
            <x-slot:triggerSlot>
                <x-daisy::ui.button color="primary">Click to toggle popover</x-daisy::ui.button>
            </x-slot:triggerSlot>
            And here's some amazing content. It's very engaging. Right?
        </x-daisy::ui.popover>

        <!-- Directions -->
        <div class="flex flex-wrap items-center gap-3">
            <x-daisy::ui.popover position="top" trigger="click" title="Top">
                <x-slot:triggerSlot><x-daisy::ui.button>Top</x-daisy::ui.button></x-slot:triggerSlot>
                Vivamus sagittis lacus vel augue laoreet rutrum faucibus.
            </x-daisy::ui.popover>
            <x-daisy::ui.popover position="right" trigger="click" title="Right">
                <x-slot:triggerSlot><x-daisy::ui.button>Right</x-daisy::ui.button></x-slot:triggerSlot>
                Vivamus sagittis lacus vel augue laoreet rutrum faucibus.
            </x-daisy::ui.popover>
            <x-daisy::ui.popover position="bottom" trigger="click" title="Bottom">
                <x-slot:triggerSlot><x-daisy::ui.button>Bottom</x-daisy::ui.button></x-slot:triggerSlot>
                Vivamus sagittis lacus vel augue laoreet rutrum faucibus.
            </x-daisy::ui.popover>
            <x-daisy::ui.popover position="left" trigger="click" title="Left">
                <x-slot:triggerSlot><x-daisy::ui.button>Left</x-daisy::ui.button></x-slot:triggerSlot>
                Vivamus sagittis lacus vel augue laoreet rutrum faucibus.
            </x-daisy::ui.popover>
        </div>

        <!-- Trigger hover & focus -->
        <div class="flex flex-wrap items-center gap-3">
            <x-daisy::ui.popover trigger="hover" position="top" title="Hover popover">
                <x-slot:triggerSlot><x-daisy::ui.button variant="outline">Hover me</x-daisy::ui.button></x-slot:triggerSlot>
                Content when hovering.
            </x-daisy::ui.popover>
            <x-daisy::ui.popover trigger="focus" position="bottom" title="Focus popover">
                <x-slot:triggerSlot><x-daisy::ui.button>Focus me</x-daisy::ui.button></x-slot:triggerSlot>
                Content when focused.
            </x-daisy::ui.popover>
        </div>

        <!-- Avec flèche, header/footer slots -->
        <x-daisy::ui.popover :arrow="true">
            <x-slot:triggerSlot>
                <x-daisy::ui.button>With arrow</x-daisy::ui.button>
            </x-slot:triggerSlot>
            <x-slot:header>
                <div class="font-semibold">Header</div>
            </x-slot:header>
            <div>Some content with arrow and custom header/footer.</div>
            <x-slot:footer>
                <div class="text-xs opacity-70">Footer</div>
            </x-slot:footer>
        </x-daisy::ui.popover>
    </div>
</section>



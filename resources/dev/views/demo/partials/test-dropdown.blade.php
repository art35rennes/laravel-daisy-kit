<!-- Dropdown -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Dropdown</h2>
    <div class="space-y-3">
        <x-daisy::ui.dropdown label="Dropdown">
            <li><a>Item 1</a></li>
            <li><a>Item 2</a></li>
        </x-daisy::ui.dropdown>
        <x-daisy::ui.dropdown label="End" :end="true">
            <li><a>Item 1</a></li>
            <li><a>Item 2</a></li>
        </x-daisy::ui.dropdown>
        <x-daisy::ui.dropdown label="Hover" :hover="true">
            <li><a>Item 1</a></li>
            <li><a>Item 2</a></li>
        </x-daisy::ui.dropdown>
    </div>
</section>



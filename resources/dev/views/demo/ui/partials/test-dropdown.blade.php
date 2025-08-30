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

        <!-- Helper dropdown (card) -->
        <div class="flex items-center gap-2">
            <span>Un texte normal et un helper dropdown</span>
            <x-daisy::ui.dropdown type="card" :end="true" :buttonCircle="true" :buttonClass="'btn btn-circle btn-ghost btn-xs text-info'">
                <x-slot:trigger>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="h-4 w-4 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </x-slot:trigger>
                <h2 class="card-title">Plus d'infos ?</h2>
                <p>Voici une description d'aide dans un helper dropdown.</p>
            </x-daisy::ui.dropdown>
        </div>
    </div>
</section>



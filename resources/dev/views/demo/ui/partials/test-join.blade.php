<!-- Join -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Join</h2>
    <div class="space-y-8">
        <!-- Barre de recherche combinée -->
        <x-daisy::ui.advanced.join class="w-full">
            <input class="input input-bordered join-item w-full max-w-xs" placeholder="Search" />
            <select class="select select-bordered join-item">
                <option disabled selected>Filter</option>
                <option>All</option>
                <option>Articles</option>
                <option>Users</option>
            </select>
            <button class="btn btn-primary join-item">Search</button>
        </x-daisy::ui.advanced.join>

        <!-- Pagination compacte (exemple réaliste) -->
        <x-daisy::ui.advanced.join>
            <button class="btn join-item"><x-bi-chevron-left class="w-4 h-4" /></button>
            <button class="btn join-item btn-active">1</button>
            <button class="btn join-item">2</button>
            <button class="btn join-item">3</button>
            <button class="btn join-item"><x-bi-chevron-right class="w-4 h-4" /></button>
        </x-daisy::ui.advanced.join>

        <!-- Groupe d'actions (vertical en mobile, horizontal en lg) -->
        <x-daisy::ui.advanced.join direction="vertical" horizontalAt="lg">
            <button class="btn join-item">Save draft</button>
            <button class="btn join-item">Preview</button>
            <button class="btn btn-primary join-item">Publish</button>
        </x-daisy::ui.advanced.join>

        <!-- Call to action avec rayon custom -->
        <x-daisy::ui.advanced.join>
            <input class="input input-bordered join-item w-full max-w-xs" placeholder="Email" />
            <button class="btn btn-accent join-item rounded-r-full">Subscribe</button>
        </x-daisy::ui.advanced.join>

        <!-- Segmented control (radios) -->
        <x-daisy::ui.advanced.join>
            <input class="join-item btn" type="radio" name="seg-join-demo" aria-label="Daily" />
            <input class="join-item btn" type="radio" name="seg-join-demo" aria-label="Weekly" />
            <input class="join-item btn" type="radio" name="seg-join-demo" aria-label="Monthly" />
        </x-daisy::ui.advanced.join>
    </div>
</section>



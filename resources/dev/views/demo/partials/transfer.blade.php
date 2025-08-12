<!-- Transfer -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Transfer</h2>
    <p class="opacity-70">Transférer des éléments entre deux listes.</p>

    <div class="grid md:grid-cols-2 gap-8 items-start">
        <div class="space-y-4">
            <h3 class="font-semibold">Basique</h3>
            <x-daisy::ui.transfer :source="[
                ['data' => 'Lorem ipsum'],
                ['data' => 'Something special'],
                ['data' => 'John Wick'],
                ['data' => 'Poland'],
                ['data' => 'Germany'],
                ['data' => 'USA'],
                ['data' => 'China'],
                ['data' => 'Madagascar'],
                ['data' => 'Argentina'],
            ]" />
        </div>

        <div class="space-y-4">
            <h3 class="font-semibold">Disabled items</h3>
            <x-daisy::ui.transfer :source="[
                ['data' => 'Lorem ipsum'],
                ['data' => 'Something special', 'disabled' => true],
                ['data' => 'John Wick', 'disabled' => true],
                ['data' => 'Poland'],
                ['data' => 'Germany'],
                ['data' => 'USA'],
                ['data' => 'China'],
                ['data' => 'Madagascar', 'disabled' => true],
                ['data' => 'Argentina'],
            ]" :target="[
                ['data' => 'Russia', 'disabled' => true],
                ['data' => 'Australia'],
                ['data' => 'Hungary', 'disabled' => true],
                ['data' => 'France'],
            ]" />
        </div>

        <div class="space-y-4">
            <h3 class="font-semibold">Checked items</h3>
            <x-daisy::ui.transfer :source="[
                ['data' => 'Lorem ipsum', 'checked' => true],
                ['data' => 'Something special', 'checked' => true],
                ['data' => 'John Wick', 'checked' => true],
                ['data' => 'Poland'],
                ['data' => 'Germany'],
                ['data' => 'USA', 'checked' => true],
                ['data' => 'China'],
                ['data' => 'Madagascar'],
                ['data' => 'Argentina'],
            ]" :target="[
                ['data' => 'Russia', 'checked' => true],
                ['data' => 'Australia', 'checked' => true],
                ['data' => 'Hungary'],
                ['data' => 'France'],
            ]" />
        </div>

        <div class="space-y-4">
            <h3 class="font-semibold">One way</h3>
            <x-daisy::ui.transfer :source="[
                ['data' => 'Lorem ipsum', 'checked' => true],
                ['data' => 'Something special', 'checked' => true],
                ['data' => 'John Wick', 'checked' => true],
                ['data' => 'Poland'],
                ['data' => 'Germany'],
                ['data' => 'USA', 'checked' => true],
                ['data' => 'China'],
                ['data' => 'Madagascar'],
                ['data' => 'Argentina'],
            ]" :target="[
                ['data' => 'Russia', 'checked' => true],
                ['data' => 'Australia', 'checked' => true],
                ['data' => 'Hungary'],
                ['data' => 'France'],
            ]" :oneWay="true" />
        </div>

        <div class="space-y-4">
            <h3 class="font-semibold">Pagination</h3>
            <x-daisy::ui.transfer :source="[
                ['data' => 'Lorem ipsum', 'checked' => true],
                ['data' => 'Something special', 'checked' => true],
                ['data' => 'John Wick', 'checked' => true],
                ['data' => 'Poland'],
                ['data' => 'Germany', 'disabled' => true],
                ['data' => 'USA', 'checked' => true],
                ['data' => 'China'],
                ['data' => 'Madagascar'],
                ['data' => 'Argentina'],
            ]" :target="[
                ['data' => 'Russia', 'checked' => true],
                ['data' => 'Australia', 'checked' => true],
                ['data' => 'Hungary'],
                ['data' => 'France'],
            ]" :pagination="true" />
        </div>

        <div class="space-y-4">
            <h3 class="font-semibold">Pagination custom</h3>
            <x-daisy::ui.transfer :source="[
                ['data' => 'Lorem ipsum', 'checked' => true],
                ['data' => 'Something special', 'checked' => true],
                ['data' => 'John Wick', 'checked' => true],
                ['data' => 'Poland'],
                ['data' => 'Germany', 'disabled' => true],
                ['data' => 'USA', 'checked' => true],
                ['data' => 'China'],
                ['data' => 'Madagascar'],
                ['data' => 'Argentina'],
                ['data' => 'Spain'],
                ['data' => 'Italy'],
                ['data' => 'Portugal'],
            ]" :target="[
                ['data' => 'Russia', 'checked' => true],
                ['data' => 'Australia', 'checked' => true],
                ['data' => 'Hungary'],
                ['data' => 'France'],
            ]" :pagination="true" :elementsPerPage="7" />
        </div>

        <div class="space-y-4">
            <h3 class="font-semibold">Search</h3>
            <x-daisy::ui.transfer :source="[
                ['data' => 'Lorem ipsum', 'checked' => true],
                ['data' => 'Something special', 'checked' => true],
                ['data' => 'John Wick', 'checked' => true],
                ['data' => 'Poland'],
                ['data' => 'Germany', 'disabled' => true],
                ['data' => 'USA', 'checked' => true],
                ['data' => 'China'],
                ['data' => 'Madagascar'],
                ['data' => 'Argentina'],
            ]" :target="[
                ['data' => 'Russia', 'checked' => true],
                ['data' => 'Australia', 'checked' => true],
                ['data' => 'Hungary'],
                ['data' => 'France'],
            ]" :pagination="true" :search="true" />
        </div>
    </div>
</section>



<!-- Media Gallery -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Media Gallery</h2>
    <div class="grid md:grid-cols-2 gap-6">
        <x-daisy::ui.media-gallery :images="[
            ['thumb' => Vite::asset('resources/dev/img/food/dummy-454x280-Strawberry.jpg'), 'src' => Vite::asset('resources/dev/img/food/dummy-1024x632-Wine.jpg'), 'alt' => 'Food'],
            ['thumb' => Vite::asset('resources/dev/img/object/dummy-454x280-Bottle.jpg'), 'src' => Vite::asset('resources/dev/img/object/dummy-1024x632-Zipper.jpg'), 'alt' => 'Object'],
            ['thumb' => Vite::asset('resources/dev/img/business/dummy-454x280-Chip.jpg'), 'src' => Vite::asset('resources/dev/img/business/dummy-1024x632-AE.jpg'), 'alt' => 'Business'],
        ]" activation="click" :zoomEffect="true" position="bottom" />

        <x-daisy::ui.media-gallery :images="[
            ['thumb' => Vite::asset('resources/dev/img/people/dummy-375x500-GambiaGirl.jpg'), 'src' => Vite::asset('resources/dev/img/people/dummy-683x1024-BarbaraStanwyck.jpg'), 'alt' => 'People 1'],
            ['thumb' => Vite::asset('resources/dev/img/divers/dummy-375x500-FairyLights.jpg'), 'src' => Vite::asset('resources/dev/img/divers/dummy-576x1024-Utrecht.jpg'), 'alt' => 'Divers 1'],
            ['thumb' => Vite::asset('resources/dev/img/object/dummy-375x500-ToyTruck.jpg'), 'src' => Vite::asset('resources/dev/img/object/dummy-576x1024-WinterScene.jpg'), 'alt' => 'Object 2'],
            ['thumb' => Vite::asset('resources/dev/img/business/dummy-375x500-Graph.jpg'), 'src' => Vite::asset('resources/dev/img/business/dummy-683x1024-Laptop.jpg'), 'alt' => 'Business 2'],
        ]" activation="mouseenter" position="right" :autoHeight="true" />
    </div>
</section>



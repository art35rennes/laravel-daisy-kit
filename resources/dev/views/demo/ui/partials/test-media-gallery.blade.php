<!-- Media Gallery -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Media Gallery</h2>
    <div class="grid md:grid-cols-2 gap-6">
        <x-daisy::ui.media-gallery :images="[
            ['thumb' => '/img/food/dummy-454x280-Strawberry.jpg', 'src' => '/img/food/dummy-1024x632-Wine.jpg', 'alt' => 'Food'],
            ['thumb' => '/img/object/dummy-454x280-Bottle.jpg', 'src' => '/img/object/dummy-1024x632-Zipper.jpg', 'alt' => 'Object'],
            ['thumb' => '/img/business/dummy-454x280-Chip.jpg', 'src' => '/img/business/dummy-1024x632-AE.jpg', 'alt' => 'Business'],
        ]" activation="click" :zoomEffect="true" position="bottom" />

        <x-daisy::ui.media-gallery :images="[
            ['thumb' => '/img/people/dummy-375x500-GambiaGirl.jpg', 'src' => '/img/people/dummy-683x1024-BarbaraStanwyck.jpg', 'alt' => 'People 1'],
            ['thumb' => '/img/divers/dummy-375x500-FairyLights.jpg', 'src' => '/img/divers/dummy-576x1024-Utrecht.jpg', 'alt' => 'Divers 1'],
            ['thumb' => '/img/object/dummy-375x500-ToyTruck.jpg', 'src' => '/img/object/dummy-576x1024-WinterScene.jpg', 'alt' => 'Object 2'],
            ['thumb' => '/img/business/dummy-375x500-Graph.jpg', 'src' => '/img/business/dummy-683x1024-Laptop.jpg', 'alt' => 'Business 2'],
        ]" activation="mouseenter" position="right" :autoHeight="true" />
    </div>
</section>



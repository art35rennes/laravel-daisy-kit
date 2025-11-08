<!-- Lightbox -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Lightbox</h2>
    <x-daisy::ui.media.lightbox :images="[
        ['thumb' => '/img/food/dummy-454x280-Strawberry.jpg', 'src' => '/img/food/dummy-1024x632-Wine.jpg', 'alt' => 'Food', 'caption' => 'Food'],
        ['thumb' => '/img/object/dummy-454x280-Bottle.jpg', 'src' => '/img/object/dummy-1024x632-Zipper.jpg', 'alt' => 'Object', 'caption' => 'Object'],
        ['thumb' => '/img/business/dummy-454x280-Chip.jpg', 'src' => '/img/business/dummy-1024x632-AE.jpg', 'alt' => 'Business', 'caption' => 'Business'],
    ]" cols="grid-cols-3 md:grid-cols-4" />
</section>



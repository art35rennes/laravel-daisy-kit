<!-- Lightbox -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Lightbox</h2>
    <x-daisy::ui.lightbox :images="[
        ['thumb' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=300', 'src' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=1600', 'alt' => 'Table Full of Spices', 'caption' => 'Table Full of Spices'],
        ['thumb' => 'https://images.unsplash.com/photo-1482192596544-9eb780fc7f66?w=300', 'src' => 'https://images.unsplash.com/photo-1482192596544-9eb780fc7f66?w=1600', 'alt' => 'Winter Landscape', 'caption' => 'Winter Landscape'],
        ['thumb' => 'https://images.unsplash.com/photo-1469474968028-56623f02e42e?w=300', 'src' => 'https://images.unsplash.com/photo-1469474968028-56623f02e42e?w=1600', 'alt' => 'View of the City in the Mountains', 'caption' => 'View of the City in the Mountains'],
    ]" cols="grid-cols-3 md:grid-cols-4" />
</section>



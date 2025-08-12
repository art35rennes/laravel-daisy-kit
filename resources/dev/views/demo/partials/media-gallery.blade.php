<!-- Media Gallery -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Media Gallery</h2>
    <div class="grid md:grid-cols-2 gap-6">
        <x-daisy::ui.media-gallery :images="[
            ['thumb' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=300', 'src' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=1600', 'alt' => 'Table Full of Spices'],
            ['thumb' => 'https://images.unsplash.com/photo-1482192596544-9eb780fc7f66?w=300', 'src' => 'https://images.unsplash.com/photo-1482192596544-9eb780fc7f66?w=1600', 'alt' => 'Winter Landscape'],
            ['thumb' => 'https://images.unsplash.com/photo-1469474968028-56623f02e42e?w=300', 'src' => 'https://images.unsplash.com/photo-1469474968028-56623f02e42e?w=1600', 'alt' => 'View of the City in the Mountains'],
        ]" activation="click" :zoomEffect="true" position="bottom" />

        <x-daisy::ui.media-gallery :images="[
            ['thumb' => 'https://images.unsplash.com/photo-1556909114-26dd1332b9de?w=300', 'src' => 'https://images.unsplash.com/photo-1556909114-26dd1332b9de?w=1600', 'alt' => 'White blouse'],
            ['thumb' => 'https://images.unsplash.com/photo-1520975922284-9bcd50f5fb39?w=300', 'src' => 'https://images.unsplash.com/photo-1520975922284-9bcd50f5fb39?w=1600', 'alt' => 'Blue Jeans Jacket'],
            ['thumb' => 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=300', 'src' => 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=1600', 'alt' => 'Red Sweatshirt'],
            ['thumb' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=300', 'src' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=1600', 'alt' => 'Black Shirt'],
        ]" activation="mouseenter" position="right" :autoHeight="true" />
    </div>
</section>



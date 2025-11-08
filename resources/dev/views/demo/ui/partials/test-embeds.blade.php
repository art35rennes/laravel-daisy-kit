<!-- Embeds / Ratio -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Embeds</h2>
    <div class="grid md:grid-cols-2 gap-6">
        <!-- YouTube 16x9 -->
        <x-daisy::ui.media.embed tag="iframe" ratio="16x9" src="https://www.youtube.com/embed/vlDzYIIOYmM" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" />

        <!-- 4x3 -->
        <x-daisy::ui.media.embed tag="div" ratio="4x3">
            <div class="flex items-center justify-center h-full w-full bg-base-300/50">4x3</div>
        </x-daisy::ui.media.embed>

        <!-- 1x1 -->
        <x-daisy::ui.media.embed tag="div" ratio="1x1">
            <div class="flex items-center justify-center h-full w-full bg-base-300/50">1x1</div>
        </x-daisy::ui.media.embed>

        <!-- Custom 2x1 via pourcentage -->
        <x-daisy::ui.media.embed tag="div" :ratioPercent="'50%'">
            <div class="flex items-center justify-center h-full w-full bg-base-300/50">2x1</div>
        </x-daisy::ui.media.embed>
    </div>
</section>



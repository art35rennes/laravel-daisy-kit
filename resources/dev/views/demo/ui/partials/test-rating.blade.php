<!-- Rating -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Rating</h2>
    <div class="flex flex-wrap items-center gap-6">
        <x-daisy::ui.advanced.rating name="r1" :value="3" size="md" />
        <x-daisy::ui.advanced.rating name="r2" :value="4.5" :half="true" size="lg" />
        <x-daisy::ui.advanced.rating name="r3" :value="0" :half="true" :clearable="true" size="xl" />
        <x-daisy::ui.advanced.rating name="r4" :value="4" :readOnly="true" />
    </div>
</section>



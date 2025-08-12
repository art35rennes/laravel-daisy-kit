<!-- Range Slider -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Range Slider</h2>
    <div class="space-y-6">
        <div class="grid md:grid-cols-5 gap-6">
            <x-daisy::ui.range value="40" />
            <x-daisy::ui.range value="40" color="primary" />
            <x-daisy::ui.range value="40" color="secondary" />
            <x-daisy::ui.range value="40" color="accent" />
            <x-daisy::ui.range value="40" color="neutral" />
            <x-daisy::ui.range value="40" color="info" />
            <x-daisy::ui.range value="40" color="success" />
            <x-daisy::ui.range value="40" color="warning" />
            <x-daisy::ui.range value="40" color="error" />
        </div>
        <div class="grid md:grid-cols-5 gap-6">
            <x-daisy::ui.range value="30" size="xs" />
            <x-daisy::ui.range value="40" size="sm" />
            <x-daisy::ui.range value="50" size="md" />
            <x-daisy::ui.range value="60" size="lg" />
            <x-daisy::ui.range value="70" size="xl" />
        </div>
        <div class="w-full max-w-xs">
            <x-daisy::ui.range value="25" step="25" />
            <div class="flex justify-between px-2.5 mt-2 text-xs">
                <span>|</span><span>|</span><span>|</span><span>|</span><span>|</span>
            </div>
            <div class="flex justify-between px-2.5 mt-2 text-xs">
                <span>1</span><span>2</span><span>3</span><span>4</span><span>5</span>
            </div>
        </div>
        <x-daisy::ui.range value="40" class="text-blue-300" :noFill="true" bg="orange" thumb="blue" />
    </div>
</section>



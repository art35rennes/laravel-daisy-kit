<!-- Stack -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Stack</h2>
    <div class="flex flex-wrap gap-8">
        <!-- 3 divs -->
        <x-daisy::ui.layout.stack class="h-20 w-32">
            <div class="bg-primary text-primary-content grid place-content-center rounded-box">1</div>
            <div class="bg-accent text-accent-content grid place-content-center rounded-box">2</div>
            <div class="bg-secondary text-secondary-content grid place-content-center rounded-box">3</div>
        </x-daisy::ui.layout.stack>

        <!-- Images -->
        <x-daisy::ui.layout.stack class="w-48">
            <img src="/img/object/dummy-600x450-Buoy.jpg" class="rounded-box" />
            <img src="/img/food/dummy-600x450-AzukiBeans.jpg" class="rounded-box" />
            <img src="/img/business/dummy-600x450-Bull.jpg" class="rounded-box" />
        </x-daisy::ui.layout.stack>

        <!-- Cards -->
        <x-daisy::ui.layout.stack class="size-28">
            <div class="card bg-base-100 border border-base-content text-center">
                <div class="card-body">A</div>
            </div>
            <div class="card bg-base-100 border border-base-content text-center">
                <div class="card-body">B</div>
            </div>
            <div class="card bg-base-100 border border-base-content text-center">
                <div class="card-body">C</div>
            </div>
        </x-daisy::ui.layout.stack>

        <!-- Alignements -->
        <div class="flex flex-col gap-4">
            <x-daisy::ui.layout.stack class="h-20 w-32" alignV="top">
                <div class="card bg-base-200 text-center shadow"><div class="card-body">A</div></div>
                <div class="card bg-base-200 text-center shadow"><div class="card-body">B</div></div>
                <div class="card bg-base-200 text-center shadow"><div class="card-body">C</div></div>
            </x-daisy::ui.layout.stack>
            <x-daisy::ui.layout.stack class="h-20 w-32" alignV="bottom" alignH="end">
                <div class="card bg-base-200 text-center shadow"><div class="card-body">A</div></div>
                <div class="card bg-base-200 text-center shadow"><div class="card-body">B</div></div>
                <div class="card bg-base-200 text-center shadow"><div class="card-body">C</div></div>
            </x-daisy::ui.layout.stack>
        </div>
    </div>
</section>



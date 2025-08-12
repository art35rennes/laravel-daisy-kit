<!-- Stack -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Stack</h2>
    <div class="flex flex-wrap gap-8">
        <!-- 3 divs -->
        <x-daisy::ui.stack class="h-20 w-32">
            <div class="bg-primary text-primary-content grid place-content-center rounded-box">1</div>
            <div class="bg-accent text-accent-content grid place-content-center rounded-box">2</div>
            <div class="bg-secondary text-secondary-content grid place-content-center rounded-box">3</div>
        </x-daisy::ui.stack>

        <!-- Images -->
        <x-daisy::ui.stack class="w-48">
            <img src="https://picsum.photos/seed/s1/400/300" class="rounded-box" />
            <img src="https://picsum.photos/seed/s2/400/300" class="rounded-box" />
            <img src="https://picsum.photos/seed/s3/400/300" class="rounded-box" />
        </x-daisy::ui.stack>

        <!-- Cards -->
        <x-daisy::ui.stack class="size-28">
            <div class="card bg-base-100 border border-base-content text-center">
                <div class="card-body">A</div>
            </div>
            <div class="card bg-base-100 border border-base-content text-center">
                <div class="card-body">B</div>
            </div>
            <div class="card bg-base-100 border border-base-content text-center">
                <div class="card-body">C</div>
            </div>
        </x-daisy::ui.stack>

        <!-- Alignements -->
        <div class="flex flex-col gap-4">
            <x-daisy::ui.stack class="h-20 w-32" alignV="top">
                <div class="card bg-base-200 text-center shadow-md"><div class="card-body">A</div></div>
                <div class="card bg-base-200 text-center shadow"><div class="card-body">B</div></div>
                <div class="card bg-base-200 text-center shadow-sm"><div class="card-body">C</div></div>
            </x-daisy::ui.stack>
            <x-daisy::ui.stack class="h-20 w-32" alignV="bottom" alignH="end">
                <div class="card bg-base-200 text-center shadow-md"><div class="card-body">A</div></div>
                <div class="card bg-base-200 text-center shadow"><div class="card-body">B</div></div>
                <div class="card bg-base-200 text-center shadow-sm"><div class="card-body">C</div></div>
            </x-daisy::ui.stack>
        </div>
    </div>
</section>



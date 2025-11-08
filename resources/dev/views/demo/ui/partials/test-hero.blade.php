<!-- Hero -->
<section class="space-y-6 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Hero</h2>
    <div class="space-y-6">
        <!-- Centered hero -->
        <x-daisy::ui.layout.hero bg="base-200" :fullScreen="false">
            <h1 class="text-5xl font-bold">Hello there</h1>
            <p class="py-6">Provident cupiditate voluptatem et in. Quaerat fugiat ut assumenda excepturi exercitationem quasi.</p>
            <x-daisy::ui.inputs.button color="primary">Get Started</x-daisy::ui.inputs.button>
        </x-daisy::ui.layout.hero>

        <!-- With figure -->
        <x-daisy::ui.layout.hero bg="base-200" :row="true">
            <x-slot:figure>
                <img src="/img/business/dummy-600x450-Bull.jpg" class="w-full max-w-sm rounded-lg shadow-2xl" />
            </x-slot:figure>
            <h1 class="text-5xl font-bold">Box Office News!</h1>
            <p class="py-6">Provident cupiditate voluptatem et in. Quaerat fugiat ut assumenda excepturi exercitationem quasi.</p>
            <x-daisy::ui.inputs.button color="primary">Get Started</x-daisy::ui.inputs.button>
        </x-daisy::ui.layout.hero>

        <!-- With figure reversed -->
        <x-daisy::ui.layout.hero bg="base-200" :row="true" :reverse="true">
            <x-slot:figure>
                <img src="/img/food/dummy-600x450-AzukiBeans.jpg" class="w-full max-w-sm rounded-lg shadow-2xl" />
            </x-slot:figure>
            <h1 class="text-5xl font-bold">Login now!</h1>
            <p class="py-6">Provident cupiditate voluptatem et in. Quaerat fugiat ut assumenda excepturi exercitationem quasi.</p>
        </x-daisy::ui.layout.hero>

        <!-- Overlay image -->
        <x-daisy::ui.layout.hero :overlay="true" imageUrl="/img/divers/dummy-576x1024-Utrecht.jpg" :fullScreen="true">
            <h1 class="mb-5 text-5xl font-bold">Hello there</h1>
            <p class="mb-5">Provident cupiditate voluptatem et in. Quaerat fugiat ut assumenda excepturi exercitationem quasi.</p>
            <x-daisy::ui.inputs.button color="primary">Get Started</x-daisy::ui.inputs.button>
        </x-daisy::ui.layout.hero>
    </div>
</section>



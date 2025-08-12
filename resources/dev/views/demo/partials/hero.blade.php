<!-- Hero -->
<section class="space-y-6 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Hero</h2>
    <div class="space-y-6">
        <!-- Centered hero -->
        <x-daisy::ui.hero bg="base-200" :fullScreen="false">
            <h1 class="text-5xl font-bold">Hello there</h1>
            <p class="py-6">Provident cupiditate voluptatem et in. Quaerat fugiat ut assumenda excepturi exercitationem quasi.</p>
            <x-daisy::ui.button color="primary">Get Started</x-daisy::ui.button>
        </x-daisy::ui.hero>

        <!-- With figure -->
        <x-daisy::ui.hero bg="base-200" :row="true">
            <x-slot:figure></x-slot:figure>
            <img src="https://picsum.photos/seed/hero1/600/400" class="max-w-sm rounded-lg shadow-2xl" />
            <h1 class="text-5xl font-bold">Box Office News!</h1>
            <p class="py-6">Provident cupiditate voluptatem et in. Quaerat fugiat ut assumenda excepturi exercitationem quasi.</p>
            <x-daisy::ui.button color="primary">Get Started</x-daisy::ui.button>
        </x-daisy::ui.hero>

        <!-- With figure reversed -->
        <x-daisy::ui.hero bg="base-200" :row="true" :reverse="true">
            <img src="https://picsum.photos/seed/hero2/600/400" class="max-w-sm rounded-lg shadow-2xl" />
            <h1 class="text-5xl font-bold">Login now!</h1>
            <p class="py-6">Provident cupiditate voluptatem et in. Quaerat fugiat ut assumenda excepturi exercitationem quasi.</p>
        </x-daisy::ui.hero>

        <!-- Overlay image -->
        <x-daisy::ui.hero :overlay="true" imageUrl="https://picsum.photos/seed/overlay/1200/400" :fullScreen="true">
            <h1 class="mb-5 text-5xl font-bold">Hello there</h1>
            <p class="mb-5">Provident cupiditate voluptatem et in. Quaerat fugiat ut assumenda excepturi exercitationem quasi.</p>
            <x-daisy::ui.button color="primary">Get Started</x-daisy::ui.button>
        </x-daisy::ui.hero>
    </div>
</section>



<!-- Mockup Phone -->
<section class="space-y-6 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Mockup Phone</h2>
    <div class="grid md:grid-cols-3 gap-6 items-start">
        <!-- Basique texte centré -->
        <x-daisy::ui.mockup-phone class="text-white grid place-content-center">It's Glowtime.</x-daisy::ui.mockup-phone>

        <!-- Avec couleur de bordure + wallpaper -->
        <x-daisy::ui.mockup-phone borderColor="primary" wallpaper="https://picsum.photos/seed/wall/300/600" />

        <!-- Sans camera + contenu custom -->
        <x-daisy::ui.mockup-phone :camera="false" displayClass="bg-base-100 grid place-content-center">
            <div class="text-center p-4">
                <div class="text-2xl font-bold">My App</div>
                <div class="text-sm opacity-70">Welcome back</div>
                <button class="btn btn-primary mt-4">Open</button>
            </div>
        </x-daisy::ui.mockup-phone>
    </div>
</section>



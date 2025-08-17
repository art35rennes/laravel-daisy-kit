<!-- Mockup Browser -->
<section class="space-y-6 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Mockup Browser</h2>
    <div class="space-y-6">
        <!-- Basique -->
        <x-daisy::ui.mockup-browser url="https://example.com" class="rounded-box">
            <div class="p-6">Contenu</div>
        </x-daisy::ui.mockup-browser>

        <!-- Personnalisation couleurs + toolbar custom -->
        <x-daisy::ui.mockup-browser url="https://docs.example.com" bg="base-100" contentBg="base-200" :bordered="true" class="rounded-box">
            <x-slot:toolbar>
                <div class="breadcrumbs text-sm">
                  <ul>
                    <li><a>Home</a></li>
                    <li><a>Docs</a></li>
                    <li>Getting started</li>
                  </ul>
                </div>
            </x-slot:toolbar>
            <div class="p-6">Page content</div>
        </x-daisy::ui.mockup-browser>

        <!-- Sans toolbar -->
        <x-daisy::ui.mockup-browser :showToolbar="false" bg="base-300" contentBg="base-100" class="rounded-box">
            <div class="p-6">No toolbar</div>
        </x-daisy::ui.mockup-browser>
    </div>
</section>



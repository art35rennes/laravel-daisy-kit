@php
    $prefix = config('daisy-kit.docs.prefix', 'docs');
@endphp
<x-daisy::layout.navbar-layout title="Template · Grid Layout">
    <x-slot:navbarStart>
        @if (Route::has('daisy.docs.templates'))
            <a href="{{ route('daisy.docs.templates') }}" class="btn btn-ghost btn-sm">Templates</a>
        @else
            <a href="/{{ $prefix }}/templates" class="btn btn-ghost btn-sm">Templates</a>
        @endif
    </x-slot:navbarStart>

    <div class="space-y-6">
        <h1 class="text-2xl font-semibold">Template · Grid Layout</h1>

        <x-daisy::ui.layout.grid-layout gap="6">
            <div class="col-12 col-lg-8">
                <x-daisy::ui.layout.card title="Article principal" class="mb-6">
                    <div class="space-y-2">
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer nec odio. Praesent libero.</p>
                        <p>Sed cursus ante dapibus diam. Sed nisi. Nulla quis sem at nibh elementum imperdiet.</p>
                    </div>
                </x-daisy::ui.layout.card>

                <x-daisy::ui.layout.grid-layout gap="4" class="mb-6">
                    <div class="col-12 col-md-6">
                        <x-daisy::ui.layout.card title="Bloc A">
                            <div>Contenu</div>
                        </x-daisy::ui.layout.card>
                    </div>
                    <div class="col-12 col-md-6">
                        <x-daisy::ui.layout.card title="Bloc B">
                            <div>Contenu</div>
                        </x-daisy::ui.layout.card>
                    </div>
                </x-daisy::ui.layout.grid-layout>
            </div>
            <div class="col-12 col-lg-4">
                <x-daisy::ui.layout.card title="Sidebar">
                    <div class="space-y-2">
                        <div class="list">
                            <div class="list-row"><div class="opacity-70">Lien 1</div></div>
                            <div class="list-row"><div class="opacity-70">Lien 2</div></div>
                            <div class="list-row"><div class="opacity-70">Lien 3</div></div>
                        </div>
                    </div>
                </x-daisy::ui.layout.card>
            </div>
        </x-daisy::ui.layout.grid-layout>
    </div>
</x-daisy::layout.navbar-layout>



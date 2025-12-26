@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $navItems = DocsHelper::getNavigationItems($prefix);
    $componentsByCategory = DocsHelper::getComponentsByCategory($prefix);
    $sections = [
        ['id' => 'introduction', 'label' => 'Introduction'],
        ['id' => 'categories', 'label' => 'Catégories'],
    ];
@endphp

<x-daisy::layout.docs title="Documentation" :sidebarItems="$navItems" :sections="$sections" :currentRoute="request()->path()">
    <x-slot:navbar>
        <x-daisy::ui.navigation.menu :vertical="false" :bg="false" :rounded="false" size="sm">
            <li><a href="/{{ $prefix }}" class="menu-active">Docs</a></li>
            <li><a href="{{ route('demo') }}">Démo</a></li>
            <li><a href="/{{ $prefix }}/templates">Templates</a></li>
        </x-daisy::ui.navigation.menu>
    </x-slot:navbar>

    <section id="introduction">
        <h1>Documentation Laravel Daisy Kit</h1>
        <p>Composants Blade basés sur daisyUI v5 / Tailwind v4, avec navigation par catégories et API des props.</p>
        <div class="alert alert-info mt-4">
            <span>Activez ces routes dans la config pour les publier dans votre application (voir <code>daisy-kit.docs</code>).</span>
        </div>
        <div class="mt-6 flex flex-wrap gap-3">
            <a href="/{{ $prefix }}/components" class="btn btn-primary btn-sm">Composants</a>
            <a href="/{{ $prefix }}/templates" class="btn btn-ghost btn-sm">Templates</a>
        </div>
    </section>

    <section id="categories" class="mt-12">
        <h2>Catégories</h2>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($componentsByCategory as $categoryId => $category)
                <div class="card bg-base-100 shadow">
                    <div class="card-body">
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <h3 class="card-title text-base">{{ $category['label'] ?? $categoryId }}</h3>
                            <span class="badge badge-outline badge-sm">{{ count($category['components'] ?? []) }}</span>
                        </div>
                        @if(!empty($category['components'] ?? []))
                            <ul class="list mt-2">
                                @foreach(($category['components'] ?? []) as $component)
                                    <li class="list-row">
                                        <a class="link" href="{{ $component['href'] ?? '#' }}">{{ $component['name'] ?? '' }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm text-base-content/60">Aucun composant</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</x-daisy::layout.docs>



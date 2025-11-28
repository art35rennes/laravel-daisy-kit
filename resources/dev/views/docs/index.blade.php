@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $navItems = DocsHelper::getNavigationItems($prefix);
    $sections = [
        ['id' => 'introduction', 'label' => 'Introduction'],
        ['id' => 'categories', 'label' => 'Catégories'],
    ];
@endphp

<x-daisy::layout.docs title="Documentation" :sidebarItems="$navItems" :sections="$sections" :currentRoute="request()->path()">
    <x-slot:navbar>
        <div class="join">
            <a href="/{{ $prefix }}" class="btn btn-sm join-item btn-ghost">Docs</a>
            <a href="{{ route('demo') }}" class="btn btn-sm join-item btn-ghost">Démo</a>
            <a href="/{{ $prefix }}/templates" class="btn btn-sm join-item btn-ghost">Template</a>
        </div>
    </x-slot:navbar>

    <section id="introduction">
        <h1>Documentation Laravel Daisy Kit</h1>
        <p>Composants Blade basés sur daisyUI v5 / Tailwind v4, avec navigation par catégories et API des props.</p>
        <div class="alert alert-info mt-4">
            <span>Activez ces routes dans la config pour les publier dans votre application (voir <code>daisy-kit.docs</code>).</span>
        </div>
    </section>

    <section id="categories" class="mt-12">
        <h2>Catégories</h2>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($navItems as $cat)
                <div class="card bg-base-100 shadow">
                    <div class="card-body">
                        <h3 class="card-title text-base">{{ $cat['label'] }}</h3>
                        <ul class="list mt-2">
                            @foreach(($cat['children'] ?? []) as $child)
                                <li class="list-row">
                                    <a class="link" href="{{ $child['href'] }}">{{ $child['label'] }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</x-daisy::layout.docs>



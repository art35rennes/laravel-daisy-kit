@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $navItems = DocsHelper::getNavigationItems($prefix);
    $templatesByCategory = DocsHelper::getTemplatesByCategory();
    $sections = array_map(function ($categoryId) use ($templatesByCategory) {
        $category = $templatesByCategory[$categoryId]['category'] ?? null;
        return [
            'id' => $categoryId,
            'label' => $category['label'] ?? ucfirst($categoryId),
        ];
    }, array_keys($templatesByCategory));
    array_unshift($sections, ['id' => 'templates', 'label' => 'Templates']);
@endphp

<x-daisy::layout.docs title="Templates" :sidebarItems="$navItems" :sections="$sections" :currentRoute="request()->path()">
    <x-slot:navbar>
        <div class="join">
            <a href="/{{ $prefix }}" class="btn btn-sm join-item btn-ghost">Docs</a>
            <a href="{{ route('demo') }}" class="btn btn-sm join-item btn-ghost">Démo</a>
            <a href="/{{ $prefix }}/templates" class="btn btn-sm join-item btn-ghost btn-active">Template</a>
        </div>
    </x-slot:navbar>

    <section id="templates">
        <h1>Templates</h1>
        <p>Accédez rapidement à des structures de pages prêtes à l'emploi.</p>
    </section>

    @foreach($templatesByCategory as $categoryId => $categoryData)
        @php
            $category = $categoryData['category'];
            $templates = $categoryData['templates'];
        @endphp
        <section id="{{ $categoryId }}" class="mt-12">
            <div class="mb-6">
                <h2 class="text-2xl font-semibold mb-2">{{ $category['label'] }}</h2>
                @if(!empty($category['description']))
                    <p class="text-base-content/70">{{ $category['description'] }}</p>
                @endif
            </div>

            <div class="grid gap-6 md:grid-cols-3">
                @foreach($templates as $template)
                    <div class="card bg-base-100 shadow">
                        <div class="card-body">
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <h3 class="card-title text-base break-words flex-1 min-w-0">{{ $template['label'] }}</h3>
                                @if(isset($template['type']))
                                    @if($template['type'] === 'reusable')
                                        <span class="badge badge-success badge-sm">Réutilisable</span>
                                    @else
                                        <span class="badge badge-info badge-sm">Exemple</span>
                                    @endif
                                @endif
                            </div>
                            <p class="text-sm mb-4">{{ $template['description'] }}</p>
                            @if(isset($template['type']))
                                <div class="text-xs text-base-content/60 mb-3 break-words">
                                    @if($template['type'] === 'reusable')
                                        <p class="mb-1"><strong>Usage :</strong> Composant Blade ou vue</p>
                                        <code class="text-xs break-words break-all">@if(isset($template['component']))&lt;x-daisy::{{ str_replace('daisy::', '', $template['component']) }}&gt;@endif</code>
                                    @else
                                        <p class="mb-1"><strong>Usage :</strong> Vue à copier/adapter</p>
                                        <code class="text-xs break-words break-all">view('{{ $template['view'] }}')</code>
                                    @endif
                                </div>
                            @endif
                            <div class="card-actions justify-end">
                                @php
                                    $routeName = $template['route'] ?? null;
                                    $hasRoute = $routeName && \Illuminate\Support\Facades\Route::has($routeName);
                                @endphp
                                @if($hasRoute)
                                    <a href="{{ route($routeName) }}" class="btn btn-primary btn-sm">Voir</a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endforeach
</x-daisy::layout.docs>



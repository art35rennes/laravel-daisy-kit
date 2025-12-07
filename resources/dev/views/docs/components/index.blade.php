@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $navItems = DocsHelper::getNavigationItems($prefix);
    $componentsByCategory = DocsHelper::getComponentsByCategory($prefix);
    $sections = array_map(function ($cat) {
        return [
            'id' => \Illuminate\Support\Str::slug($cat['label']),
            'label' => $cat['label'],
        ];
    }, $navItems);
    array_unshift($sections, ['id' => 'components', 'label' => 'Composants']);
@endphp

<x-daisy::docs.page 
    title="Composants" 
    category="" 
    name="index"
    type="index"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Composants UI" 
            subtitle="Catalogue des composants Blade du kit Daisy, organisés par famille (inputs, layout, overlay, etc.)."
        />
    </x-slot:intro>

    @foreach($componentsByCategory as $categoryId => $category)
        <section id="{{ $categoryId }}" class="mt-12">
            <div class="flex items-center justify-between gap-3 mb-4">
                <h2 class="text-2xl font-semibold">{{ $category['label'] ?? $categoryId }}</h2>
                <span class="badge badge-outline">{{ count($category['components'] ?? []) }} composants</span>
            </div>
            @if(empty($category['components'] ?? []))
                <p class="text-base-content/60 text-sm">Aucun composant listé dans cette catégorie.</p>
            @else
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($category['components'] as $component)
                        <a href="{{ $component['href'] ?? '#' }}" class="card bg-base-100 shadow hover:shadow-md transition-shadow">
                            <div class="card-body">
                                <div class="flex items-start justify-between gap-2">
                                    <h3 class="card-title text-base break-words">{{ $component['name'] ?? 'Composant' }}</h3>
                                    @if(!empty($component['status']))
                                        <span class="badge badge-sm {{ $component['status'] === 'active' ? 'badge-success' : 'badge-ghost' }}">{{ $component['status'] }}</span>
                                    @endif
                                </div>
                                @if(!empty($component['view']))
                                    <p class="text-xs text-base-content/60 break-words"><code>{{ $component['view'] }}</code></p>
                                @endif
                                @if(!empty($component['tags']))
                                    <div class="flex flex-wrap gap-1 mt-2">
                                        @foreach($component['tags'] as $tag)
                                            <span class="badge badge-ghost badge-xs">{{ $tag }}</span>
                                        @endforeach
                                    </div>
                                @endif
                                @if(!empty($component['jsModule']))
                                    <p class="text-xs text-info mt-2">Module JS : <code>{{ $component['jsModule'] }}</code></p>
                                @endif
                                <div class="card-actions justify-end">
                                    <span class="btn btn-sm btn-primary btn-outline">Ouvrir</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>
    @endforeach
</x-daisy::docs.page>

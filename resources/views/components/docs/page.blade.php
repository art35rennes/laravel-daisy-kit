@props([
    'title',
    'category' => null,
    'name' => null,
    'type' => 'component', // 'component'|'template'|'index'
    'sections' => [],
])

@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    
    if ($type === 'component' && $category && $name) {
        $navItems = DocsHelper::getNavigationItems($prefix);
        $props = DocsHelper::getComponentProps($category, $name);
    } elseif ($type === 'template') {
        $navItems = DocsHelper::getTemplateNavigationItems($prefix);
    } else {
        $navItems = DocsHelper::getNavigationItems($prefix);
    }
@endphp

<x-daisy::layout.docs :title="$title" :sidebarItems="$navItems" :sections="$sections" :currentRoute="request()->path()">
    <x-slot:navbar>
        @php
            $currentPath = request()->path();
            $isDocs = str_starts_with($currentPath, $prefix) && !str_contains($currentPath, 'templates');
            $isTemplates = str_contains($currentPath, 'templates');
            $isDemo = str_contains($currentPath, 'demo');
        @endphp
        <x-daisy::ui.navigation.menu :vertical="false" :bg="false" :rounded="false" size="sm">
            <li><a href="/{{ $prefix }}" class="{{ $isDocs && !$isTemplates ? 'menu-active' : '' }}">Docs</a></li>
            <li><a href="{{ route('demo') }}" class="{{ $isDemo ? 'menu-active' : '' }}">Démo</a></li>
            <li><a href="/{{ $prefix }}/templates" class="{{ $isTemplates ? 'menu-active' : '' }}">Templates</a></li>
        </x-daisy::ui.navigation.menu>
    </x-slot:navbar>

    {{ $intro ?? '' }}

    {{ $content ?? $slot }}
</x-daisy::layout.docs>


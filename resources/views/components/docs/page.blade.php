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
        <div class="join">
            <a href="/{{ $prefix }}" class="btn btn-sm join-item btn-ghost">Docs</a>
            <a href="{{ route('demo') }}" class="btn btn-sm join-item btn-ghost">Démo</a>
            <a href="/{{ $prefix }}/templates" class="btn btn-sm join-item btn-ghost {{ $type === 'template' ? 'btn-active' : '' }}">Template</a>
        </div>
    </x-slot:navbar>

    {{ $intro ?? '' }}

    {{ $content ?? $slot }}
</x-daisy::layout.docs>


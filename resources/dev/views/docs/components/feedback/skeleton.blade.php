@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'feedback';
    $name = 'skeleton';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Squelette" 
    category="feedback" 
    name="skeleton"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Squelette" 
            subtitle="Placeholder de chargement."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="skeleton">
        <x-slot:preview>
            <div class="space-y-2">
                <x-daisy::ui.feedback.skeleton width="300" height="20" />
                <x-daisy::ui.feedback.skeleton width="250" height="20" />
                <x-daisy::ui.feedback.skeleton width="200" height="20" />
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.feedback.skeleton width="300" height="20" />
<x-daisy::ui.feedback.skeleton width="250" height="20" />
<x-daisy::ui.feedback.skeleton width="200" height="20" />';
            @endphp
            <x-daisy::ui.advanced.code-editor 
                language="blade" 
                :value="$baseCode"
                :readonly="true"
                :showToolbar="false"
                :showFoldAll="false"
                :showUnfoldAll="false"
                :showFormat="false"
                :showCopy="true"
                height="200px"
            />
        </x-slot:code>
    </x-daisy::docs.sections.example>

    <x-daisy::docs.sections.api :category="$category" :name="$name" />
</x-daisy::docs.page>

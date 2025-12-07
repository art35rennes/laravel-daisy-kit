@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'feedback';
    $name = 'toast';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Notification" 
    category="feedback" 
    name="toast"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Notification" 
            subtitle="Notification toast."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="toast">
        <x-slot:preview>
            <x-daisy::ui.feedback.toast>
                <div class="alert alert-success">
                    <span>Message envoyé avec succès !</span>
                </div>
            </x-daisy::ui.feedback.toast>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.feedback.toast>
    <div class="alert alert-success">
        <span>Message envoyé avec succès !</span>
    </div>
</x-daisy::ui.feedback.toast>';
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

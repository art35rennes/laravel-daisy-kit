@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'data-display';
    $name = 'avatar';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Avatar" 
    category="data-display" 
    name="avatar"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Avatar" 
            subtitle="Avatar pour afficher une image de profil."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="avatar">
        <x-slot:preview>
            <x-daisy::ui.data-display.avatar src="https://i.pravatar.cc/150?img=12" alt="Jean Dupont" />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.data-display.avatar src="https://i.pravatar.cc/150?img=12" alt="Jean Dupont" />';
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

    <x-daisy::docs.sections.variants name="avatar">
        <x-slot:preview>
            <div class="space-y-6">
                <div>
                    <p class="text-sm font-semibold mb-2">Tailles</p>
                    <div class="flex items-center gap-4">
                        <x-daisy::ui.data-display.avatar size="xs" src="https://i.pravatar.cc/150?img=12" alt="XS" />
                        <x-daisy::ui.data-display.avatar size="sm" src="https://i.pravatar.cc/150?img=12" alt="SM" />
                        <x-daisy::ui.data-display.avatar size="md" src="https://i.pravatar.cc/150?img=12" alt="MD" />
                        <x-daisy::ui.data-display.avatar size="lg" src="https://i.pravatar.cc/150?img=12" alt="LG" />
                        <x-daisy::ui.data-display.avatar size="xl" src="https://i.pravatar.cc/150?img=12" alt="XL" />
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Avec initiales</p>
                    <div class="flex items-center gap-4">
                        <x-daisy::ui.data-display.avatar size="md">JD</x-daisy::ui.data-display.avatar>
                        <x-daisy::ui.data-display.avatar size="lg">AB</x-daisy::ui.data-display.avatar>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Statut en ligne/hors ligne</p>
                    <div class="flex items-center gap-4">
                        <x-daisy::ui.data-display.avatar :online="true" src="https://i.pravatar.cc/150?img=12" alt="En ligne" />
                        <x-daisy::ui.data-display.avatar :offline="true" src="https://i.pravatar.cc/150?img=12" alt="Hors ligne" />
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Groupe d'avatars</p>
                    <div class="avatar-group -space-x-6">
                        <x-daisy::ui.data-display.avatar size="md" src="https://i.pravatar.cc/150?img=1" alt="User 1" />
                        <x-daisy::ui.data-display.avatar size="md" src="https://i.pravatar.cc/150?img=2" alt="User 2" />
                        <x-daisy::ui.data-display.avatar size="md" src="https://i.pravatar.cc/150?img=3" alt="User 3" />
                        <x-daisy::ui.data-display.avatar size="md" src="https://i.pravatar.cc/150?img=4" alt="User 4" />
                    </div>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Tailles --}}
<x-daisy::ui.data-display.avatar size="sm" src="image.jpg" alt="User" />
<x-daisy::ui.data-display.avatar size="lg" src="image.jpg" alt="User" />

{{-- Avec initiales --}}
<x-daisy::ui.data-display.avatar size="md">JD</x-daisy::ui.data-display.avatar>

{{-- Statut --}}
<x-daisy::ui.data-display.avatar :online="true" src="image.jpg" alt="User" />
<x-daisy::ui.data-display.avatar :offline="true" src="image.jpg" alt="User" />

{{-- Groupe --}}
<div class="avatar-group -space-x-6">
    <x-daisy::ui.data-display.avatar src="user1.jpg" />
    <x-daisy::ui.data-display.avatar src="user2.jpg" />
</div>';
            @endphp
            <x-daisy::ui.advanced.code-editor 
                language="blade" 
                :value="$variantsCode"
                :readonly="true"
                :showToolbar="false"
                :showFoldAll="false"
                :showUnfoldAll="false"
                :showFormat="false"
                :showCopy="true"
                height="350px"
            />
        </x-slot:code>
    </x-daisy::docs.sections.variants>

    <x-daisy::docs.sections.api :category="$category" :name="$name" />
</x-daisy::docs.page>

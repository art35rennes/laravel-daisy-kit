@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'advanced';
    $name = 'swap';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Échange" 
    category="advanced" 
    name="swap"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Échange" 
            subtitle="Échange entre deux éléments au clic."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="swap">
        <x-slot:preview>
            <label class="swap">
                <input type="checkbox" />
                <div class="swap-on text-2xl">🌙</div>
                <div class="swap-off text-2xl">☀️</div>
            </label>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<label class="swap">
    <input type="checkbox" />
    <div class="swap-on text-2xl">🌙</div>
    <div class="swap-off text-2xl">☀️</div>
</label>';
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

    <x-daisy::docs.sections.variants name="swap">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Rotation</p>
                    <label class="swap swap-rotate">
                        <input type="checkbox" />
                        <div class="swap-on text-2xl">✓</div>
                        <div class="swap-off text-2xl">✗</div>
                    </label>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Flip</p>
                    <label class="swap swap-flip">
                        <input type="checkbox" />
                        <div class="swap-on text-2xl">ON</div>
                        <div class="swap-off text-2xl">OFF</div>
                    </label>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Avec images</p>
                    <label class="swap">
                        <input type="checkbox" />
                        <img src="https://picsum.photos/100/100?random=1" class="swap-on w-20 h-20 object-cover rounded-box" />
                        <img src="https://picsum.photos/100/100?random=2" class="swap-off w-20 h-20 object-cover rounded-box" />
                    </label>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Rotation --}}
<label class="swap swap-rotate">
    <input type="checkbox" />
    <div class="swap-on">✓</div>
    <div class="swap-off">✗</div>
</label>

{{-- Flip --}}
<label class="swap swap-flip">
    <input type="checkbox" />
    <div class="swap-on">ON</div>
    <div class="swap-off">OFF</div>
</label>

{{-- Avec images --}}
<label class="swap">
    <input type="checkbox" />
    <img src="..." class="swap-on w-20 h-20 object-cover rounded-box" />
    <img src="..." class="swap-off w-20 h-20 object-cover rounded-box" />
</label>';
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

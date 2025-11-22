@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $navItems = DocsHelper::getNavigationItems($prefix);
    $sections = [
            ['id' => 'intro', 'label' => 'Introduction'],
            ['id' => 'base', 'label' => 'Exemple de base'],
            ['id' => 'variants', 'label' => 'Variantes'],
            ['id' => 'api', 'label' => 'API'],
        ];
    $props = DocsHelper::getComponentProps('data-display', 'file-preview');
@endphp

<x-daisy::layout.docs title="File Preview" :sidebarItems="$navItems" :sections="$sections" :currentRoute="request()->path()">
    <x-slot:navbar>
        <div class="join">
            <a href="/{{$prefix}}" class="btn btn-sm join-item btn-ghost">Docs</a>
            <a href="{{ route('demo') }}" class="btn btn-sm join-item btn-ghost">Démo</a>
            <a href="/{{$prefix}}/templates" class="btn btn-sm join-item btn-ghost">Template</a>
        </div>
    </x-slot:navbar>

    <section id="intro">
        <h1>File Preview</h1>
        <p>Composant compatible daisyUI v5 et Tailwind CSS v4.</p>
    </section>

    <section id="base" class="mt-10">
        <h2>Exemple de base</h2>
        <div class="tabs tabs-box">
            <input type="radio" name="base-example-file-preview" class="tab" aria-label="Preview" checked />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                <div class="not-prose">
                    <x-daisy::ui.data-display.file-preview 
                        url="https://www.placeholderimage.eu/api/id/1/400/300"
                        name="example-image.jpg"
                        type="image"
                    />
                </div>
            </div>
            <input type="radio" name="base-example-file-preview" class="tab" aria-label="Code" />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                @php
                    $baseCode = '<x-daisy::ui.data-display.file-preview 
    url="https://example.com/image.jpg"
    name="example-image.jpg"
    type="image"
/>';
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
            </div>
        </div>
    </section>

    <section id="variants" class="mt-10">
        <h2>Variantes</h2>
        <div class="space-y-6">
            <div>
                <h3 class="text-lg font-semibold mb-3">Types de fichiers</h3>
                <div class="tabs tabs-box">
                    <input type="radio" name="types-example-file-preview" class="tab" aria-label="Preview" checked />
                    <div class="tab-content border-base-300 bg-base-100 p-6">
                        <div class="not-prose space-y-4">
                            <div>
                                <p class="text-sm font-medium mb-2">Image</p>
                                <x-daisy::ui.data-display.file-preview 
                                    url="https://www.placeholderimage.eu/api/id/1/400/300"
                                    name="photo.jpg"
                                    type="image"
                                />
                            </div>
                            <div>
                                <p class="text-sm font-medium mb-2">PDF</p>
                                <x-daisy::ui.data-display.file-preview 
                                    url="https://example.com/document.pdf"
                                    name="document.pdf"
                                    type="pdf"
                                    fileSize="2.5 MB"
                                />
                            </div>
                            <div>
                                <p class="text-sm font-medium mb-2">Document</p>
                                <x-daisy::ui.data-display.file-preview 
                                    url="https://example.com/report.docx"
                                    name="report.docx"
                                    type="document"
                                    fileSize="1.2 MB"
                                />
                            </div>
                        </div>
                    </div>
                    <input type="radio" name="types-example-file-preview" class="tab" aria-label="Code" />
                    <div class="tab-content border-base-300 bg-base-100 p-6">
                        @php
                            $typesCode = '&lt;!-- Image --&gt;
&lt;x-daisy::ui.data-display.file-preview 
    url=&quot;https://example.com/image.jpg&quot;
    name=&quot;photo.jpg&quot;
    type=&quot;image&quot;
/&gt;

&lt;!-- PDF --&gt;
&lt;x-daisy::ui.data-display.file-preview 
    url=&quot;https://example.com/document.pdf&quot;
    name=&quot;document.pdf&quot;
    type=&quot;pdf&quot;
    fileSize=&quot;2.5 MB&quot;
/&gt;

&lt;!-- Document --&gt;
&lt;x-daisy::ui.data-display.file-preview 
    url=&quot;https://example.com/report.docx&quot;
    name=&quot;report.docx&quot;
    type=&quot;document&quot;
    fileSize=&quot;1.2 MB&quot;
/&gt;';
                        @endphp
                        <x-daisy::ui.advanced.code-editor 
                            language="blade" 
                            :value="$typesCode"
                            :readonly="true"
                            :showToolbar="false"
                            :showFoldAll="false"
                            :showUnfoldAll="false"
                            :showFormat="false"
                            :showCopy="true"
                            height="400px"
                        />
                    </div>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-semibold mb-3">Tailles</h3>
                <div class="tabs tabs-box">
                    <input type="radio" name="sizes-example-file-preview" class="tab" aria-label="Preview" checked />
                    <div class="tab-content border-base-300 bg-base-100 p-6">
                        <div class="not-prose flex flex-wrap items-center gap-4">
                            <x-daisy::ui.data-display.file-preview 
                                url="https://www.placeholderimage.eu/api/id/1/200/150"
                                name="small.jpg"
                                type="image"
                                size="xs"
                            />
                            <x-daisy::ui.data-display.file-preview 
                                url="https://www.placeholderimage.eu/api/id/2/300/200"
                                name="medium.jpg"
                                type="image"
                                size="md"
                            />
                            <x-daisy::ui.data-display.file-preview 
                                url="https://www.placeholderimage.eu/api/id/3/400/300"
                                name="large.jpg"
                                type="image"
                                size="lg"
                            />
                        </div>
                    </div>
                    <input type="radio" name="sizes-example-file-preview" class="tab" aria-label="Code" />
                    <div class="tab-content border-base-300 bg-base-100 p-6">
                        @php
                            $sizesCode = '&lt;x-daisy::ui.data-display.file-preview 
    url=&quot;https://example.com/image.jpg&quot;
    name=&quot;small.jpg&quot;
    type=&quot;image&quot;
    size=&quot;xs&quot;
/&gt;

&lt;x-daisy::ui.data-display.file-preview 
    url=&quot;https://example.com/image.jpg&quot;
    name=&quot;medium.jpg&quot;
    type=&quot;image&quot;
    size=&quot;md&quot;
/&gt;

&lt;x-daisy::ui.data-display.file-preview 
    url=&quot;https://example.com/image.jpg&quot;
    name=&quot;large.jpg&quot;
    type=&quot;image&quot;
    size=&quot;lg&quot;
/&gt;';
                        @endphp
                        <x-daisy::ui.advanced.code-editor 
                            language="blade" 
                            :value="$sizesCode"
                            :readonly="true"
                            :showToolbar="false"
                            :showFoldAll="false"
                            :showUnfoldAll="false"
                            :showFormat="false"
                            :showCopy="true"
                            height="300px"
                        />
                    </div>
                </div>
            </div>
        </div>
    </section>
    @if(!empty($props))
    <section id="api" class="mt-10">
        <h2>API</h2>
        <div class="overflow-x-auto">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Prop</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($props as $prop)
                        <tr>
                            <td><code>{{ $prop }}</code></td>
                            <td class="opacity-70">Voir les commentaires dans le composant Blade pour les valeurs et défauts.</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    @endif
</x-daisy::layout.docs>

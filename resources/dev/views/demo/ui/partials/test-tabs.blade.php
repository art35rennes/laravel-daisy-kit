<!-- Tabs -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Tabs</h2>
    <div class="space-y-6">
        <!-- Basique boxed -->
        <x-daisy::ui.tabs :items="[
            ['label' => 'Tab 1', 'active' => true],
            ['label' => 'Tab 2'],
            ['label' => 'Tab 3']
        ]" variant="boxed" />

        <!-- Border (bottom) + tailles -->
        <x-daisy::ui.tabs size="sm" variant="border" :items="[
            ['label' => 'Small 1', 'active' => true],
            ['label' => 'Small 2'],
            ['label' => 'Small 3', 'disabled' => true]
        ]" />
        <x-daisy::ui.tabs size="lg" variant="lifted" :items="[
            ['label' => 'Large 1', 'active' => true],
            ['label' => 'Large 2'],
            ['label' => 'Large 3']
        ]" />

        <!-- Placement bottom -->
        <x-daisy::ui.tabs placement="bottom" variant="boxed" :items="[
            ['label' => 'Bottom 1', 'active' => true],
            ['label' => 'Bottom 2'],
        ]" />

        <!-- Radio + contenus -->
        <x-daisy::ui.tabs variant="lifted" radioName="my_tabs_demo" :items="[
            ['label' => 'Tab A', 'active' => true, 'content' => 'Contenu A'],
            ['label' => 'Tab B', 'content' => 'Contenu B'],
            ['label' => 'Tab C', 'content' => 'Contenu C']
        ]" />
    </div>
</section>



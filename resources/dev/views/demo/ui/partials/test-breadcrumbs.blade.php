<!-- Breadcrumbs -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Breadcrumbs</h2>
    <div class="space-y-3">
        <x-daisy::ui.navigation.breadcrumbs :items="[
            ['label' => 'Home', 'href' => '/'],
            ['label' => 'Library', 'href' => '#'],
            ['label' => 'Data']
        ]" />

        <x-daisy::ui.navigation.breadcrumbs size="sm" as="nav" label="Breadcrumb with icons" :items="[
            ['label' => 'Home', 'href' => '/', 'icon' => '<svg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' class=\'h-4 w-4 stroke-current\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z\'></path></svg>'],
            ['label' => 'Documents', 'href' => '#', 'icon' => '<svg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' class=\'h-4 w-4 stroke-current\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z\'></path></svg>'],
            ['label' => 'Add Document', 'icon' => '<svg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' class=\'h-4 w-4 stroke-current\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z\'></path></svg>'],
        ]" />

        <x-daisy::ui.navigation.breadcrumbs size="sm" class="max-w-xs" :items="[
            ['label' => 'Long text 1'],
            ['label' => 'Long text 2'],
            ['label' => 'Long text 3'],
            ['label' => 'Long text 4'],
            ['label' => 'Long text 5'],
        ]" />
    </div>
</section>



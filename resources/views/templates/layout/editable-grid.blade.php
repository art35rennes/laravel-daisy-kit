@props([
    'title' => 'Editable dashboard',
    'theme' => null,
    'editable' => true,
    'columns' => 12,
    'cellHeight' => 96,
    'gap' => 16,
    'static' => false,
])

<x-daisy::layout.app :title="$title" :theme="$theme" :container="true">
    <div class="space-y-6">
        <x-daisy::ui.layout.hero
            class="rounded-box border border-base-content/10 bg-base-200/40"
        >
            <div class="space-y-4">
                <div class="space-y-2">
                    <h1 class="text-3xl font-semibold tracking-tight">Editable dashboard</h1>
                    <p class="text-sm text-base-content/70">
                        Gridstack is isolated to this optional surface. The rest of the package keeps using the existing static grid system.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                <x-daisy::ui.data-display.badge color="primary">Optional surface</x-daisy::ui.data-display.badge>
                <x-daisy::ui.data-display.badge color="neutral">{{ $editable && ! $static ? 'Editable' : 'Read only' }}</x-daisy::ui.data-display.badge>
                <x-daisy::ui.data-display.badge color="accent">{{ $columns }} columns</x-daisy::ui.data-display.badge>
                </div>
            </div>
        </x-daisy::ui.layout.hero>

        <x-daisy::ui.layout.editable-grid
            :editable="$editable"
            :columns="$columns"
            :cell-height="$cellHeight"
            :gap="$gap"
            :static="$static"
        >
            <x-daisy::ui.layout.editable-grid-item id="kpi-revenue" type="stat" :x="0" :y="0" :w="4" :h="2">
                <x-daisy::ui.layout.card title="Revenue" class="h-full card-border">
                    <x-daisy::ui.data-display.stat
                        title="Monthly recurring revenue"
                        value="$42,500"
                        desc="+12.4% vs last month"
                    />
                </x-daisy::ui.layout.card>
            </x-daisy::ui.layout.editable-grid-item>

            <x-daisy::ui.layout.editable-grid-item id="kpi-users" type="stat" :x="4" :y="0" :w="4" :h="2">
                <x-daisy::ui.layout.card title="Users" class="h-full card-border">
                    <x-daisy::ui.data-display.stat
                        title="Active users"
                        value="1,284"
                        desc="84 new this week"
                    />
                </x-daisy::ui.layout.card>
            </x-daisy::ui.layout.editable-grid-item>

            <x-daisy::ui.layout.editable-grid-item id="kpi-sla" type="stat" :x="8" :y="0" :w="4" :h="2">
                <x-daisy::ui.layout.card title="Operations" class="h-full card-border">
                    <x-daisy::ui.data-display.stat
                        title="Average response time"
                        value="2m 14s"
                        desc="Within target SLA"
                    />
                </x-daisy::ui.layout.card>
            </x-daisy::ui.layout.editable-grid-item>

            <x-daisy::ui.layout.editable-grid-item id="team-priorities" type="list" :x="0" :y="2" :w="6" :h="3">
                <x-daisy::ui.layout.card title="Team priorities" class="h-full card-border">
                    <x-daisy::ui.layout.list title="This week" :bg="false">
                        <x-daisy::ui.layout.list-row>Ship package demo for editable layouts</x-daisy::ui.layout.list-row>
                        <x-daisy::ui.layout.list-row>Review widget persistence contract</x-daisy::ui.layout.list-row>
                        <x-daisy::ui.layout.list-row>Prepare follow-up form builder spike</x-daisy::ui.layout.list-row>
                    </x-daisy::ui.layout.list>
                </x-daisy::ui.layout.card>
            </x-daisy::ui.layout.editable-grid-item>

            <x-daisy::ui.layout.editable-grid-item id="release-notes" type="list" :x="6" :y="2" :w="6" :h="3">
                <x-daisy::ui.layout.card title="Release checklist" class="h-full card-border">
                    <x-daisy::ui.layout.list title="Before publish" :bg="false">
                        <x-daisy::ui.layout.list-row>Validate the editable grid in read-only mode</x-daisy::ui.layout.list-row>
                        <x-daisy::ui.layout.list-row>Confirm drag and resize serialization events</x-daisy::ui.layout.list-row>
                        <x-daisy::ui.layout.list-row>Keep `grid-layout` as the default layout surface</x-daisy::ui.layout.list-row>
                    </x-daisy::ui.layout.list>
                </x-daisy::ui.layout.card>
            </x-daisy::ui.layout.editable-grid-item>
        </x-daisy::ui.layout.editable-grid>
    </div>
</x-daisy::layout.app>

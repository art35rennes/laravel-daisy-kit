@props([
    'title' => 'Editable dashboard',
    'theme' => null,
    'editable' => true,
    'columns' => 12,
    'cellHeight' => 112,
    'gap' => 16,
    'static' => false,
])

<x-daisy::layout.app :title="$title" :theme="$theme" :container="true">
    <div class="space-y-5">
        <x-daisy::ui.layout.hero
            class="rounded-box border border-base-content/10 bg-base-200/35"
        >
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-3xl space-y-2">
                    <h1 class="text-3xl font-semibold">Editable dashboard</h1>
                    <p class="text-sm leading-6 text-base-content/70">
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

        <div class="rounded-box border border-base-content/10 bg-base-100 p-4 shadow-sm">
            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold">Dashboard workspace</h2>
                    <p class="text-sm text-base-content/65">Move and resize widgets without clipping their content.</p>
                </div>

                <div class="flex flex-wrap gap-2 text-xs">
                    <span class="badge badge-soft badge-primary">{{ $columns }} columns</span>
                    <span class="badge badge-soft badge-neutral">Drag & resize</span>
                    <span class="badge badge-soft badge-accent">Compact layout</span>
                </div>
            </div>

            <x-daisy::ui.layout.editable-grid
                :editable="$editable"
                :columns="$columns"
                :cell-height="$cellHeight"
                :gap="$gap"
                :static="$static"
                layout="compact"
            >
                <x-daisy::ui.layout.editable-grid-item id="kpi-revenue" type="stat" :x="0" :y="0" :w="3" :h="2">
                    <section class="flex h-full min-h-0 flex-col justify-between rounded-box border border-base-content/10 bg-base-100 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-xs font-medium uppercase text-base-content/55">Revenue</p>
                                <p class="mt-2 text-3xl font-semibold">$42.5k</p>
                            </div>
                            <span class="badge badge-soft badge-primary">MRR</span>
                        </div>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-xs text-base-content/60">
                                <span>Target</span>
                                <span>82%</span>
                            </div>
                            <progress class="progress progress-primary h-2" value="82" max="100"></progress>
                        </div>
                    </section>
                </x-daisy::ui.layout.editable-grid-item>

                <x-daisy::ui.layout.editable-grid-item id="kpi-users" type="stat" :x="3" :y="0" :w="3" :h="2">
                    <section class="flex h-full min-h-0 flex-col justify-between rounded-box border border-base-content/10 bg-base-100 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-xs font-medium uppercase text-base-content/55">Users</p>
                                <p class="mt-2 text-3xl font-semibold">1,284</p>
                            </div>
                            <span class="badge badge-soft badge-secondary">Active</span>
                        </div>
                        <p class="text-sm text-base-content/65">84 new accounts this week</p>
                    </section>
                </x-daisy::ui.layout.editable-grid-item>

                <x-daisy::ui.layout.editable-grid-item id="kpi-sla" type="stat" :x="6" :y="0" :w="3" :h="2">
                    <section class="flex h-full min-h-0 flex-col justify-between rounded-box border border-base-content/10 bg-base-100 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-xs font-medium uppercase text-base-content/55">Ops</p>
                                <p class="mt-2 text-3xl font-semibold">2m 14s</p>
                            </div>
                            <span class="badge badge-soft badge-accent">SLA</span>
                        </div>
                        <p class="text-sm text-base-content/65">Response time under target</p>
                    </section>
                </x-daisy::ui.layout.editable-grid-item>

                <x-daisy::ui.layout.editable-grid-item id="kpi-risk" type="stat" :x="9" :y="0" :w="3" :h="2">
                    <section class="flex h-full min-h-0 flex-col justify-between rounded-box border border-base-content/10 bg-base-100 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-xs font-medium uppercase text-base-content/55">Risk</p>
                                <p class="mt-2 text-3xl font-semibold">3</p>
                            </div>
                            <span class="badge badge-soft badge-warning">Open</span>
                        </div>
                        <p class="text-sm text-base-content/65">Two are waiting on review</p>
                    </section>
                </x-daisy::ui.layout.editable-grid-item>

                <x-daisy::ui.layout.editable-grid-item id="team-priorities" type="list" :x="0" :y="2" :w="6" :h="3">
                    <section class="flex h-full min-h-0 flex-col rounded-box border border-base-content/10 bg-base-100 p-4">
                        <div class="mb-3 flex items-start justify-between gap-3">
                            <div>
                                <h3 class="font-semibold">Team priorities</h3>
                                <p class="text-sm text-base-content/60">This week</p>
                            </div>
                            <span class="badge badge-ghost">3 tasks</span>
                        </div>
                        <div class="min-h-0 flex-1 divide-y divide-base-content/10 overflow-hidden text-sm">
                            <div class="flex items-center justify-between gap-3 py-3">
                                <span>Ship editable layout demo</span>
                                <span class="badge badge-sm badge-success">Ready</span>
                            </div>
                            <div class="flex items-center justify-between gap-3 py-3">
                                <span>Review persistence contract</span>
                                <span class="badge badge-sm badge-warning">Review</span>
                            </div>
                            <div class="flex items-center justify-between gap-3 py-3">
                                <span>Prepare form builder follow-up</span>
                                <span class="badge badge-sm badge-ghost">Queued</span>
                            </div>
                        </div>
                    </section>
                </x-daisy::ui.layout.editable-grid-item>

                <x-daisy::ui.layout.editable-grid-item id="release-checklist" type="list" :x="6" :y="2" :w="3" :h="3">
                    <section class="flex h-full min-h-0 flex-col rounded-box border border-base-content/10 bg-base-100 p-4">
                        <h3 class="font-semibold">Release checklist</h3>
                        <div class="mt-3 flex min-h-0 flex-1 flex-col gap-3 text-sm">
                            <label class="flex items-center gap-3">
                                <input type="checkbox" checked class="checkbox checkbox-sm checkbox-success" disabled>
                                <span>Static mode verified</span>
                            </label>
                            <label class="flex items-center gap-3">
                                <input type="checkbox" checked class="checkbox checkbox-sm checkbox-success" disabled>
                                <span>Drag events serialized</span>
                            </label>
                            <label class="flex items-center gap-3">
                                <input type="checkbox" class="checkbox checkbox-sm" disabled>
                                <span>Docs screenshot updated</span>
                            </label>
                        </div>
                    </section>
                </x-daisy::ui.layout.editable-grid-item>

                <x-daisy::ui.layout.editable-grid-item id="layout-health" type="chart" :x="9" :y="2" :w="3" :h="3">
                    <section class="flex h-full min-h-0 flex-col rounded-box border border-base-content/10 bg-base-100 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="font-semibold">Layout health</h3>
                                <p class="text-sm text-base-content/60">Widget fit score</p>
                            </div>
                            <span class="text-sm font-medium text-success">Stable</span>
                        </div>
                        <div class="flex flex-1 items-center justify-center">
                            <div class="radial-progress text-primary" style="--value:92; --size:7rem; --thickness:0.7rem;" role="progressbar">92%</div>
                        </div>
                    </section>
                </x-daisy::ui.layout.editable-grid-item>
            </x-daisy::ui.layout.editable-grid>
        </div>
    </div>
</x-daisy::layout.app>

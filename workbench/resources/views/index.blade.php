<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daisy Kit v5 Workbench</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-base-200 text-base-content">
    <main class="mx-auto max-w-6xl space-y-8 p-4 sm:p-8">
        <header class="hero rounded-box bg-base-100 shadow-sm">
            <div class="hero-content text-center">
            <h1>Daisy Kit v5 Workbench</h1>
            <p>Each section is an independently mounted package module.</p>
            </div>
        </header>

        <section class="min-w-0 space-y-6" aria-labelledby="table-heading">
            <h2 id="table-heading">Table</h2>

            <h3 id="table-client-heading">Client directory</h3>
            <x-daisy-kit::table
                :columns="[
                    [
                        'key' => 'name',
                        'label' => 'Name',
                        'sortable' => true,
                        'cell' => ['renderer' => 'blade', 'view' => 'workbench::table.cells.person'],
                    ],
                    ['key' => 'team', 'label' => 'Team', 'sortable' => true],
                    ['key' => 'status', 'label' => 'Status', 'sortable' => true],
                ]"
                :rows="[
                    ['id' => 'ada', 'name' => 'Ada Lovelace', 'team' => 'Platform', 'status' => 'ready'],
                    ['id' => 'grace', 'name' => 'Grace Hopper', 'team' => 'Infrastructure', 'status' => 'review'],
                    ['id' => 'margaret', 'name' => 'Margaret Hamilton', 'team' => 'Flight software', 'status' => 'ready'],
                    ['id' => 'katherine', 'name' => 'Katherine Johnson', 'team' => 'Research', 'status' => 'paused'],
                    ['id' => 'dorothy', 'name' => 'Dorothy Vaughan', 'team' => 'Research', 'status' => 'ready'],
                    ['id' => 'mary', 'name' => 'Mary Jackson', 'team' => 'Platform', 'status' => 'review'],
                    ['id' => 'annie', 'name' => 'Annie Easley', 'team' => 'Infrastructure', 'status' => 'paused'],
                    ['id' => 'joan', 'name' => 'Joan Clarke', 'team' => 'Research', 'status' => 'ready'],
                    ['id' => 'hedy', 'name' => 'Hedy Lamarr', 'team' => 'Platform', 'status' => 'review'],
                    ['id' => 'radia', 'name' => 'Radia Perlman', 'team' => 'Infrastructure', 'status' => 'ready'],
                    ['id' => 'evelyn', 'name' => 'Evelyn Boyd Granville', 'team' => 'Flight software', 'status' => 'paused'],
                    ['id' => 'susan', 'name' => 'Susan Kare', 'team' => 'Platform', 'status' => 'ready'],
                ]"
                :filters="[
                    ['id' => 'name', 'label' => 'Name', 'type' => 'text'],
                    [
                        'id' => 'team',
                        'label' => 'Team',
                        'type' => 'select',
                        'options' => [
                            ['value' => 'Platform', 'label' => 'Platform'],
                            ['value' => 'Infrastructure', 'label' => 'Infrastructure'],
                            ['value' => 'Flight software', 'label' => 'Flight software'],
                            ['value' => 'Research', 'label' => 'Research'],
                        ],
                    ],
                    [
                        'id' => 'status',
                        'label' => 'Status',
                        'type' => 'select',
                        'options' => [
                            ['value' => 'ready', 'label' => 'Ready'],
                            ['value' => 'review', 'label' => 'Review'],
                            ['value' => 'paused', 'label' => 'Paused'],
                        ],
                    ],
                ]"
                :page-size="4"
                :page-size-options="[4, 8, 12]"
                caption="People directory"
                state-key="workbench-client-directory"
                persist-state="url"
            />

            <h3 id="table-server-heading">Server queue and bulk selection</h3>
            <x-daisy-kit::table
                id="server-queue-table"
                mode="server"
                :endpoint="route('workbench.table.rows')"
                server-adapter="spatie-query-builder"
                global-filter-key="global"
                filter-mode="manual"
                :columns="[
                    ['key' => 'reference', 'label' => 'Reference', 'sortKey' => 'cases.reference'],
                    ['key' => 'customer', 'label' => 'Customer', 'sortKey' => 'cases.customer'],
                    ['key' => 'priority', 'label' => 'Priority', 'sortKey' => 'cases.priority'],
                    ['key' => 'status', 'label' => 'Status', 'sortKey' => 'cases.status'],
                ]"
                :filters="[
                    ['id' => 'customer', 'label' => 'Customer', 'type' => 'text'],
                    ['id' => 'priority', 'label' => 'Priority', 'type' => 'select', 'options' => ['Urgent', 'High', 'Normal', 'Low']],
                    ['id' => 'status', 'filterKey' => 'state', 'label' => 'Status', 'type' => 'select', 'options' => ['Open', 'Review', 'Waiting', 'Closed']],
                ]"
                selection="multiple"
                row-key="id"
                :bulk-actions="[
                    ['id' => 'assign', 'label' => 'Assign selected'],
                    ['id' => 'close', 'label' => 'Close selected'],
                ]"
                :page-size="3"
                :page-size-options="[3, 6, 12]"
                caption="Support queue"
            />

            <h3 id="table-details-heading">Contextual details</h3>
            <x-daisy-kit::table
                :columns="[
                    ['key' => 'service', 'label' => 'Service'],
                    ['key' => 'owner', 'label' => 'Owner'],
                    ['key' => 'health', 'label' => 'Health'],
                ]"
                :rows="[
                    ['id' => 'api', 'service' => 'Public API', 'owner' => 'Platform', 'health' => 'Operational', 'summary' => '12 instances across three regions. Last deployment succeeded.'],
                    ['id' => 'worker', 'service' => 'Media workers', 'owner' => 'Content', 'health' => 'Degraded', 'summary' => 'Two delayed jobs. Automatic retry is in progress.'],
                    ['id' => 'billing', 'service' => 'Billing gateway', 'owner' => 'Finance', 'health' => 'Operational', 'summary' => 'All payment providers are responding normally.'],
                    ['id' => 'search', 'service' => 'Search index', 'owner' => 'Data', 'health' => 'Operational', 'summary' => 'The last full index completed 18 minutes ago.'],
                    ['id' => 'mail', 'service' => 'Transactional mail', 'owner' => 'Growth', 'health' => 'Delayed', 'summary' => 'Delivery is delayed by approximately four minutes.'],
                    ['id' => 'storage', 'service' => 'Object storage', 'owner' => 'Infrastructure', 'health' => 'Operational', 'summary' => 'Replication is healthy in all configured regions.'],
                ]"
                :row-details="['accessor' => 'summary', 'label' => 'Show details', 'mode' => 'inline']"
                :page-size="3"
                :page-size-options="[3, 6, 12]"
                caption="Service health"
            />

            <h3 id="table-editing-heading">Inline editing</h3>
            <x-daisy-kit::table
                :columns="[
                    ['key' => 'name', 'label' => 'Project'],
                    ['key' => 'state', 'label' => 'State'],
                    ['key' => 'owner', 'label' => 'Owner'],
                ]"
                :rows="[
                    ['id' => 'atlas', 'name' => 'Atlas migration', 'state' => 'Review', 'owner' => 'Ada'],
                    ['id' => 'relay', 'name' => 'Relay launch', 'state' => 'Draft', 'owner' => 'Grace'],
                    ['id' => 'orbit', 'name' => 'Orbit billing', 'state' => 'Ready', 'owner' => 'Margaret'],
                    ['id' => 'nova', 'name' => 'Nova search', 'state' => 'Review', 'owner' => 'Katherine'],
                    ['id' => 'harbor', 'name' => 'Harbor storage', 'state' => 'Draft', 'owner' => 'Dorothy'],
                    ['id' => 'signal', 'name' => 'Signal alerts', 'state' => 'Ready', 'owner' => 'Mary'],
                ]"
                :editable="[
                    'columns' => ['name', 'state', 'owner'],
                    'endpoint' => url('/_daisy-kit-test/table/rows/{rowId}'),
                    'method' => 'PATCH',
                ]"
                :page-size="3"
                :page-size-options="[3, 6, 12]"
                caption="Project planning"
            />
        </section>

        <section class="min-w-0" aria-labelledby="tree-heading">
            <h2 id="tree-heading">Tree</h2>
            <x-daisy-kit::tree
                :searchable="true"
                :search-source="route('workbench.tree.search')"
                :items="[
                    [
                        'id' => 'documentation',
                        'label' => 'Documentation',
                        'expanded' => true,
                        'children' => [
                            ['id' => 'getting-started', 'label' => 'Getting started'],
                            ['id' => 'api-reference', 'label' => 'API reference'],
                        ],
                    ],
                ]"
            />
        </section>

        <section class="min-w-0" aria-labelledby="blueprint-heading">
            <h2 id="blueprint-heading">Blueprint</h2>
            <x-daisy-kit::blueprint
                :nodes="[
                    ['id' => 'source', 'label' => 'Source', 'value' => ['state' => 'ready']],
                    ['id' => 'destination', 'label' => 'Destination'],
                ]"
                :edges="[
                    ['source' => 'source', 'target' => 'destination'],
                ]"
                :value="[
                    'nodes' => [
                        ['id' => 'source', 'label' => 'Source', 'value' => ['state' => 'ready']],
                        ['id' => 'destination', 'label' => 'Destination'],
                    ],
                    'edges' => [],
                ]"
                :editable="true"
                name="workbench_blueprint"
            />
        </section>

        <section class="min-w-0" aria-labelledby="file-preview-heading">
            <h2 id="file-preview-heading">File Preview</h2>
            <x-daisy-kit::file-preview
                src="/_daisy-kit-test/files/preview.txt"
                type="text"
                name="Workbench note"
                notice="Rendered in an isolated sandbox."
            />
        </section>

        <section class="card min-w-0 space-y-6 bg-base-100 p-6" aria-labelledby="focused-components-heading">
            <h2 id="focused-components-heading">Focused interaction components</h2>
            <x-daisy-kit::copyable class="card" value="release-2026-08-29">Copy release identifier</x-daisy-kit::copyable>

            @if(session()->has('workbench.review.saved'))
                <p class="alert alert-success" role="status">The review assignment was saved.</p>
            @endif

            <form class="space-y-6" method="POST" action="{{ route('workbench.reviews.store') }}">
                @csrf
                <x-daisy-kit::combobox
                    class="card"
                    name="reviewers"
                    label="Reviewers"
                    :multiple="true"
                    :allow-custom="true"
                    :source="route('workbench.combobox.reviewers')"
                    :min-chars="1"
                    :options="[['value' => 'ada', 'label' => 'Ada Lovelace', 'description' => 'Platform']]"
                    :value="['ada']"
                />
                <x-daisy-kit::signature class="card" name="approval_signature" label="Approval signature" />
                <x-daisy-kit::transfer-list
                    class="card"
                    name="assignees"
                    :items="[
                        ['value' => 'ada', 'label' => 'Ada Lovelace'],
                        ['value' => 'grace', 'label' => 'Grace Hopper'],
                        ['value' => 'margaret', 'label' => 'Margaret Hamilton'],
                    ]"
                    :value="['ada']"
                />
                <button class="btn btn-primary" type="submit">Save review assignment</button>
            </form>

            <x-daisy-kit::truncate class="card" :text="str_repeat('Selectable release notes remain available in full. ', 12)" :lines="2" />
            <div id="workbench-scrollspy-content" class="max-h-48 overflow-auto" tabindex="0">
                <h3 id="workbench-overview">Overview</h3><p>{{ str_repeat('Overview content. ', 20) }}</p>
                <h3 id="workbench-details">Details</h3><p>{{ str_repeat('Detailed content. ', 20) }}</p>
            </div>
            <x-daisy-kit::scrollspy
                class="card"
                target="#workbench-scrollspy-content"
                :items="[['id' => 'workbench-overview', 'label' => 'Overview'], ['id' => 'workbench-details', 'label' => 'Details']]"
            />
        </section>

        <section class="min-w-0" aria-labelledby="map-heading">
            <h2 id="map-heading">Map</h2>

            <div class="grid gap-8">
                <article>
                    <h3>Markers, popups and clustering</h3>
                    <p class="text-base-content/70">Eight nearby operations sites are grouped as the view changes.</p>
                    <x-daisy-kit::map
                        id="map-cluster"
                        label="Operations sites"
                        :fit-bounds="false"
                        :zoom="12"
                        :cluster="['maxClusterRadius' => 72]"
                        :markers="[
                            ['id' => 'rennes', 'label' => 'Rennes office', 'position' => [48.1173, -1.6778], 'popup' => 'Rennes office'],
                            ['id' => 'depot', 'label' => 'Central depot', 'position' => [48.1181, -1.6769], 'popup' => 'Central depot'],
                            ['id' => 'lab', 'label' => 'Materials lab', 'position' => [48.1167, -1.6786], 'popup' => ['renderer' => 'trusted-html', 'content' => '<strong>Materials lab</strong><br>Open 08:00–18:00']],
                            ['id' => 'workshop', 'label' => 'Workshop', 'position' => [48.1178, -1.6791], 'popup' => 'Workshop'],
                            ['id' => 'dispatch', 'label' => 'Dispatch center', 'position' => [48.1169, -1.6762], 'popup' => 'Dispatch center'],
                            ['id' => 'storage', 'label' => 'Storage', 'position' => [48.1185, -1.6781], 'popup' => 'Storage'],
                            ['id' => 'training', 'label' => 'Training room', 'position' => [48.1164, -1.6771], 'popup' => 'Training room'],
                            ['id' => 'support', 'label' => 'Support desk', 'position' => [48.1175, -1.6758], 'popup' => 'Support desk'],
                        ]"
                    />
                </article>

                <article>
                    <h3>Basemaps and typed layers</h3>
                    <p class="text-base-content/70">GeoJSON is fetched locally; XYZ and WMS overlays use deterministic local endpoints.</p>
                    <x-daisy-kit::map
                        id="map-layers"
                        label="Network layers"
                        :scale="true"
                        :basemaps="[
                            ['id' => 'light', 'label' => 'Light grid', 'type' => 'xyz', 'url' => '/_daisy-kit-test/map/tiles/light/{z}/{x}/{y}.png', 'selected' => true],
                            ['id' => 'dark', 'label' => 'Dark grid', 'type' => 'xyz', 'url' => '/_daisy-kit-test/map/tiles/dark/{z}/{x}/{y}.png'],
                        ]"
                        :layers="[
                            ['id' => 'districts', 'label' => 'Service districts', 'type' => 'geojson', 'url' => '/_daisy-kit-test/map/districts.geojson', 'style' => ['color' => '#2563eb', 'weight' => 2]],
                            ['id' => 'works', 'label' => 'Works tiles', 'type' => 'xyz', 'url' => '/_daisy-kit-test/map/tiles/works/{z}/{x}/{y}.png', 'visible' => false],
                            ['id' => 'zoning', 'label' => 'Zoning WMS', 'type' => 'wms', 'url' => '/_daisy-kit-test/map/wms', 'options' => ['layers' => 'workbench:zoning', 'format' => 'image/png', 'transparent' => true], 'visible' => false],
                        ]"
                    />
                </article>

                <article>
                    <h3>Drawing, measurement and form export</h3>
                    <p class="text-base-content/70">Draw objects, edit or select them, use history and submit the resulting GeoJSON.</p>
                    <x-daisy-kit::map
                        id="map-drawing"
                        label="Maintenance drawing"
                        name="maintenance_geometry"
                        :drawing="true"
                        :measure="true"
                        :spatial-selection="['mode' => 'both']"
                        :geojson="[
                            'type' => 'FeatureCollection',
                            'features' => [
                                ['type' => 'Feature', 'id' => 'site-north', 'properties' => ['name' => 'North maintenance site'], 'geometry' => ['type' => 'Point', 'coordinates' => [-1.684, 48.124]]],
                                ['type' => 'Feature', 'id' => 'site-south', 'properties' => ['name' => 'South maintenance site'], 'geometry' => ['type' => 'Point', 'coordinates' => [-1.671, 48.109]]],
                            ],
                        ]"
                        :object-types="[
                            ['id' => 'hydrant', 'label' => 'Hydrant', 'geometry' => 'point'],
                            ['id' => 'pipe', 'label' => 'Pipe', 'geometry' => 'line'],
                            ['id' => 'zone', 'label' => 'Intervention zone', 'geometry' => 'polygon'],
                        ]"
                        :draw-layers="[
                            ['id' => 'water', 'label' => 'Water network'],
                            ['id' => 'electricity', 'label' => 'Electricity network'],
                        ]"
                    />
                </article>

                <article>
                    <h3>Persistence and geolocation</h3>
                    <p class="text-base-content/70">The map restores its host-scoped view and offers its configured location controls.</p>
                    <x-daisy-kit::map
                        id="map-controlled"
                        label="Externally controlled map"
                        :fullscreen="true"
                        :gesture-handling="true"
                        :geolocation="['watch' => true, 'setView' => true]"
                        :persist-state="true"
                        state-key="workbench-controlled-map"
                        :markers="[['id' => 'center', 'label' => 'Initial center', 'position' => [48.1173, -1.6778]]]"
                        :basemaps="[
                            ['id' => 'offline', 'label' => 'Local test grid', 'type' => 'xyz', 'url' => '/_daisy-kit-test/map/tiles/light/{z}/{x}/{y}.png', 'selected' => true],
                            ['id' => 'osm', 'label' => 'OpenStreetMap — activate network', 'type' => 'xyz', 'url' => 'https://tile.openstreetmap.org/{z}/{x}/{y}.png', 'attribution' => '<a href=&quot;https://www.openstreetmap.org/copyright&quot;>© OpenStreetMap contributors</a>', 'trustedAttribution' => true],
                        ]"
                    />
                </article>
            </div>
        </section>
    </main>
</body>
</html>

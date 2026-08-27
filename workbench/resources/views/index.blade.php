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

        <section class="min-w-0" aria-labelledby="forms-viewer-heading">
            <h2 id="forms-viewer-heading">Forms Viewer</h2>
            <x-daisy-kit::forms.viewer
                :schema="[
                    'fields' => [
                        ['name' => 'name', 'label' => 'Display name', 'type' => 'text', 'rules' => ['required']],
                        ['name' => 'role', 'label' => 'Role', 'type' => 'select', 'options' => ['Maintainer', 'Reviewer']],
                        ['name' => 'updates', 'label' => 'Receive updates', 'type' => 'toggle'],
                    ],
                    'submit' => ['label' => 'Save profile'],
                ]"
                :value="['name' => 'Ada Lovelace', 'role' => 'Maintainer', 'updates' => true]"
            />
        </section>

        <section class="min-w-0" aria-labelledby="forms-builder-heading">
            <h2 id="forms-builder-heading">Forms Builder</h2>
            <x-daisy-kit::forms.builder
                :schema="['fields' => [['name' => 'email', 'label' => 'Email address', 'type' => 'email', 'rules' => ['required', 'email']]]]"
                name="workbench_schema"
            />
        </section>

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
                mode="server"
                :endpoint="route('workbench.table.rows')"
                server-adapter="spatie-query-builder"
                global-filter-key="global"
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
                :items="[
                    [
                        'id' => 'documentation',
                        'label' => 'Documentation',
                        'expanded' => true,
                        'children' => [
                            ['id' => 'getting-started', 'label' => 'Getting started'],
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

        <section class="min-w-0" aria-labelledby="map-heading">
            <h2 id="map-heading">Map</h2>
            <x-daisy-kit::map
                :drawing="true"
                :geojson="[
                    'type' => 'Feature',
                    'geometry' => ['type' => 'Point', 'coordinates' => [-1.6778, 48.1173]],
                    'properties' => ['label' => 'Rennes'],
                ]"
                :markers="[['id' => 'rennes', 'label' => 'Rennes', 'position' => [48.1173, -1.6778]]]"
            />
        </section>
    </main>
</body>
</html>

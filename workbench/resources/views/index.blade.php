<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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

        <section class="min-w-0" aria-labelledby="table-heading">
            <h2 id="table-heading">Table</h2>
            <x-daisy-kit::table
                :columns="[
                    ['id' => 'name', 'label' => 'Name'],
                    ['id' => 'status', 'label' => 'Status'],
                ]"
                :rows="[
                    ['id' => 'ada', 'name' => 'Ada Lovelace', 'status' => 'Ready'],
                    ['id' => 'grace', 'name' => 'Grace Hopper', 'status' => 'Review'],
                ]"
                :selectable="true"
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

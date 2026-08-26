<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daisy Kit v5 Workbench</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <main>
        <header>
            <h1>Daisy Kit v5 Workbench</h1>
            <p>Each section is an independently mounted package module.</p>
        </header>

        <section aria-labelledby="forms-viewer-heading">
            <h2 id="forms-viewer-heading">Forms Viewer</h2>
            <x-daisy-kit::forms.viewer />
        </section>

        <section aria-labelledby="forms-builder-heading">
            <h2 id="forms-builder-heading">Forms Builder</h2>
            <x-daisy-kit::forms.builder />
        </section>

        <section aria-labelledby="table-heading">
            <h2 id="table-heading">Table</h2>
            <x-daisy-kit::table />
        </section>

        <section aria-labelledby="tree-heading">
            <h2 id="tree-heading">Tree</h2>
            <x-daisy-kit::tree
                :items="[
                    [
                        'id' => 'documentation',
                        'label' => 'Documentation',
                        'expanded' => false,
                        'children' => [
                            ['id' => 'getting-started', 'label' => 'Getting started'],
                        ],
                    ],
                ]"
            />
        </section>

        <section aria-labelledby="blueprint-heading">
            <h2 id="blueprint-heading">Blueprint</h2>
            <x-daisy-kit::blueprint
                :nodes="[
                    ['id' => 'source', 'label' => 'Source'],
                    ['id' => 'destination', 'label' => 'Destination'],
                ]"
                :edges="[
                    ['source' => 'source', 'target' => 'destination'],
                ]"
            />
        </section>

        <section aria-labelledby="file-preview-heading">
            <h2 id="file-preview-heading">File Preview</h2>
            <x-daisy-kit::file-preview />
        </section>

        <section aria-labelledby="map-heading">
            <h2 id="map-heading">Map</h2>
            <x-daisy-kit::map />
        </section>
    </main>
</body>
</html>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daisy Kit strict CSP fixture</title>
    @vite(['resources/css/app.css', 'resources/js/csp-strict.js'])
</head>
<body>
    <main class="space-y-8">
        <h1>Daisy Kit strict CSP fixture</h1>

        <x-daisy-kit::table
            :columns="[['key' => 'name', 'label' => 'Name']]"
            :rows="[['id' => 'ada', 'name' => 'Ada Lovelace']]"
            caption="Reviewers"
        />
        <x-daisy-kit::tree
            label="Project files"
            :items="[['id' => 'docs', 'label' => 'Documentation']]"
        />
        <x-daisy-kit::blueprint
            label="Approval flow"
            :nodes="[['id' => 'review', 'label' => 'Review'], ['id' => 'publish', 'label' => 'Publish']]"
            :edges="[['source' => 'review', 'target' => 'publish']]"
        />
        <x-daisy-kit::file-preview
            src="/_daisy-kit-test/files/preview.txt"
            type="text"
            name="Release note"
        />
        <x-daisy-kit::map
            label="Delivery area"
            :fit-bounds="false"
            :markers="[['id' => 'office', 'label' => 'Office', 'position' => [48.1173, -1.6778]]]"
        />
        <x-daisy-kit::copyable value="release-2026-08-29">Copy release identifier</x-daisy-kit::copyable>
        <x-daisy-kit::combobox
            name="reviewer"
            label="Reviewer"
            :options="[['value' => 'ada', 'label' => 'Ada Lovelace']]"
            value="ada"
        />
        <x-daisy-kit::truncate :text="str_repeat('A selectable release note. ', 8)" :lines="2" />

        <article id="strict-csp-sections">
            <h2 id="strict-csp-overview">Overview</h2>
            <p>Release overview.</p>
            <h2 id="strict-csp-details">Details</h2>
            <p>Release details.</p>
        </article>
        <x-daisy-kit::scrollspy
            target="#strict-csp-sections"
            :items="[
                ['id' => 'strict-csp-overview', 'label' => 'Overview'],
                ['id' => 'strict-csp-details', 'label' => 'Details'],
            ]"
        />
    </main>
</body>
</html>

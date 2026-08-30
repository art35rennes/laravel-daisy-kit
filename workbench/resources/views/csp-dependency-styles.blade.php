<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daisy Kit dependency style CSP fixture</title>
    @vite(['resources/css/app.css', 'resources/js/csp-dependency-styles.js'])
</head>
<body>
    <main class="space-y-8">
        <h1>Daisy Kit dependency style CSP fixture</h1>
        <x-daisy-kit::signature name="approval_signature" label="Approval signature" />
        <x-daisy-kit::transfer-list
            name="assignees"
            label="Release assignees"
            :items="[
                ['value' => 'ada', 'label' => 'Ada Lovelace'],
                ['value' => 'grace', 'label' => 'Grace Hopper'],
            ]"
            :value="['ada']"
        />
    </main>
</body>
</html>

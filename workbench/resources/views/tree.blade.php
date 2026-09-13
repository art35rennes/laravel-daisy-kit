<!doctype html>
<html lang="{{ app()->getLocale() }}" data-theme="{{ request('theme') === 'dark' ? 'dark' : 'light' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tree · Daisy Kit Workbench</title>
    @vite(['resources/css/app.css', 'resources/js/tree.js'])
</head>
<body class="bg-base-200 text-base-content" data-workbench-module="tree">
    <main class="space-y-8">
        <header class="flex flex-wrap items-center justify-between gap-4">
            <div><h1 class="text-2xl font-semibold">Tree · Daisy Kit Workbench</h1><p class="text-sm text-base-content/70">Classification, permissions and remote catalogues.</p></div>
            <form class="flex flex-wrap items-end gap-3" method="GET">
                <label class="grid gap-1 text-sm">Language<select class="select select-sm" name="lang"><option value="en" @selected(app()->getLocale() === 'en')>English</option><option value="fr" @selected(app()->getLocale() === 'fr')>Français</option></select></label>
                <label class="grid gap-1 text-sm">Theme<select class="select select-sm" name="theme"><option value="light" @selected(request('theme') !== 'dark')>Light</option><option value="dark" @selected(request('theme') === 'dark')>Dark</option></select></label>
                <button class="btn btn-sm" type="submit">Apply</button>
            </form>
        </header>
        <a class="link text-base-content" href="{{ route('workbench.index') }}">Back to component modules</a>
        @include('workbench::tree-scenarios')
    </main>
</body>
</html>

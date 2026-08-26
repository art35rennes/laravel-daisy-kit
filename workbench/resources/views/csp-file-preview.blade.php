<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daisy Kit CSP File Preview</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <main>
        <h1>Daisy Kit CSP File Preview</h1>
        <x-daisy-kit::file-preview src="/_daisy-kit-test/files/preview.txt" type="text" />
    </main>
</body>
</html>

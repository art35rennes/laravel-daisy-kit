<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daisy Kit CSP Map</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <main>
        <h1>Daisy Kit CSP Map</h1>
        <x-daisy-kit::map
            :drawing="true"
            :geojson="[
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'LineString',
                    'coordinates' => [[-1.68, 48.11], [-1.67, 48.12]],
                ],
                'properties' => [],
            ]"
        />
    </main>
</body>
</html>

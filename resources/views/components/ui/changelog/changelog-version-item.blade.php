@props([
    'version' => null, // Version string
    'date' => null, // Date string
    'isCurrent' => false,
    'yanked' => false,
    'tagUrl' => null, // Lien vers le tag Git
    'compareUrl' => null, // Lien de comparaison
    'items' => [], // Array de changements (format enrichi)
    'changes' => [], // Array de changements (format simple: ['added' => [...], 'fixed' => [...]])
    'expandByDefault' => false,
    'highlightCurrent' => true,
])

@php
    use Carbon\Carbon;

    // Normaliser les données : convertir le format simple en format enrichi si nécessaire
    $normalizedItems = [];

    if (!empty($items)) {
        // Format enrichi déjà fourni
        $normalizedItems = $items;
    } elseif (!empty($changes)) {
        // Format simple : convertir en format enrichi
        foreach ($changes as $type => $changeList) {
            if (is_array($changeList)) {
                foreach ($changeList as $change) {
                    if (is_string($change)) {
                        $normalizedItems[] = [
                            'type' => $type,
                            'description' => $change,
                        ];
                    } elseif (is_array($change)) {
                        $normalizedItems[] = array_merge(['type' => $type], $change);
                    }
                }
            }
        }
    }

    // Formater la date
    $formattedDate = null;
    if ($date) {
        try {
            $formattedDate = Carbon::parse($date)->format('d/m/Y');
        } catch (\Exception $e) {
            $formattedDate = $date;
        }
    }

    // Construire le titre du collapse
    $titleParts = [];
    if ($version) {
        $titleParts[] = __('changelog.version').' '.$version;
    }
    if ($formattedDate) {
        $titleParts[] = __('changelog.released_on').' '.$formattedDate;
    }
    $collapseTitle = implode(' - ', $titleParts);

    // Classes pour le collapse
    $collapseClasses = '';
    if ($yanked) {
        $collapseClasses .= ' opacity-60';
    }
@endphp

<div class="changelog-version-item {{ $collapseClasses }} relative grid grid-cols-1 md:grid-cols-[auto_1fr] gap-4 md:gap-6" data-version="{{ $version }}">
    {{-- Date: au-dessus sur mobile, à gauche sur desktop --}}
    <div class="timeline-column flex flex-row md:flex-col items-center md:items-start gap-3 md:gap-0 text-sm text-base-content/70 md:text-left">
        <span class="font-medium md:mb-3">{{ $formattedDate }}</span>
        <div class="relative hidden md:block">
            <span class="flex h-4 w-4 items-center justify-center rounded-full border-2 border-primary bg-base-100 text-primary">
                <span class="h-1 w-1 rounded-full bg-primary"></span>
            </span>
            <span class="absolute left-1/2 top-4 h-full w-px -translate-x-1/2 bg-base-200"></span>
        </div>
    </div>

    {{-- Contenu: prend toute la largeur sur mobile --}}
    <div class="space-y-3 rounded-3xl border border-base-300 bg-base-100 p-4 md:p-6 shadow-md transition hover:shadow-lg">
        <div class="flex flex-col sm:flex-row sm:flex-wrap sm:items-start gap-4">
            <div class="flex-1 min-w-0">
                <p class="text-xs uppercase tracking-wide text-base-content/60">{{ __('changelog.version') }}</p>
                <h3 class="text-xl font-semibold text-base-content">{{ $version ?? __('changelog.version') }}</h3>
                @if($collapseTitle)
                    <p class="text-sm text-base-content/70 hidden md:block">{{ $collapseTitle }}</p>
                @endif
            </div>

            <div class="flex flex-wrap items-center gap-2 text-xs">
                @if($isCurrent)
                    <x-daisy::ui.data-display.badge color="primary" size="sm">
                        {{ __('changelog.current_version') }}
                    </x-daisy::ui.data-display.badge>
                @endif

                @if($yanked)
                    <x-daisy::ui.data-display.badge color="error" size="sm" variant="soft">
                        {{ __('changelog.yanked') }}
                    </x-daisy::ui.data-display.badge>
                @endif

                @if($tagUrl)
                    <a href="{{ $tagUrl }}" target="_blank" rel="noopener noreferrer" class="link link-primary font-semibold text-xs">
                        {{ __('changelog.view_tag') }}
                    </a>
                @endif

                @if($compareUrl)
                    <a href="{{ $compareUrl }}" target="_blank" rel="noopener noreferrer" class="link link-info font-semibold text-xs">
                        {{ __('changelog.compare_versions') }}
                    </a>
                @endif
            </div>
        </div>

        <div class="space-y-3">
            @forelse($normalizedItems as $item)
                <x-daisy::ui.changelog.changelog-change-item
                    :type="$item['type'] ?? 'added'"
                    :category="$item['category'] ?? null"
                    :description="$item['description'] ?? ''"
                    :breaking="(bool)($item['breaking'] ?? false)"
                    :issues="$item['issues'] ?? []"
                    :contributors="$item['contributors'] ?? []"
                    :image="$item['image'] ?? null"
                    :migration="(bool)($item['migration'] ?? false)"
                    :migration-guide="$item['migrationGuide'] ?? null"
                    :cve="$item['cve'] ?? null"
                    :severity="$item['severity'] ?? null"
                    :issue-base-url="$item['issueBaseUrl'] ?? 'https://github.com/user/repo/issues'"
                />
            @empty
                <p class="text-sm text-base-content/60">{{ __('changelog.no_results') }}</p>
            @endforelse
        </div>
    </div>
</div>


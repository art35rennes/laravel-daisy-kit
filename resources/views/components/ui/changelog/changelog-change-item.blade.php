@props([
    'type' => 'added', // added|changed|fixed|removed|security
    'category' => null, // Catégorie optionnelle
    'description' => '',
    'breaking' => false,
    'issues' => [], // [123, 456] ou [['number' => 123, 'url' => '...']]
    'contributors' => [], // ['username1', 'username2']
    'image' => null, // URL de l'image
    'migration' => false,
    'migrationGuide' => null,
    'cve' => null, // Numéro CVE
    'severity' => null, // high|medium|low
    'issueBaseUrl' => 'https://github.com/user/repo/issues', // Base URL pour les issues
])

@php
    // Mapping des types vers les couleurs daisyUI
    $typeColorMap = [
        'added' => 'success',
        'changed' => 'info',
        'fixed' => 'warning',
        'removed' => 'error',
        'security' => 'error',
    ];
    $typeColor = $typeColorMap[$type] ?? 'neutral';
    $typeLabel = __('changelog.'.$type);

    // Formatage des issues
    $formattedIssues = [];
    foreach ($issues as $issue) {
        if (is_array($issue)) {
            $formattedIssues[] = $issue;
        } else {
            $formattedIssues[] = [
                'number' => $issue,
                'url' => rtrim($issueBaseUrl, '/').'/'.$issue,
            ];
        }
    }
@endphp

<div class="changelog-change-item rounded-box card-border bg-base-100 p-4 shadow">
    {{-- Zone des badges/tags en haut --}}
    <div class="mb-3 flex flex-wrap items-center gap-2">
        <x-daisy::ui.data-display.badge :color="$typeColor" size="sm">
            {{ $typeLabel }}
        </x-daisy::ui.data-display.badge>

        @if($breaking)
            <x-daisy::ui.data-display.badge color="error" size="xs" variant="soft">
                {{ __('changelog.breaking_change') }}
            </x-daisy::ui.data-display.badge>
        @endif

        @if($migration)
            <x-daisy::ui.data-display.badge color="warning" size="xs" variant="soft">
                {{ __('changelog.migration_required') }}
            </x-daisy::ui.data-display.badge>
        @endif

        @if($cve)
            <x-daisy::ui.data-display.badge color="error" size="xs">
                {{ __('changelog.cve') }}: {{ $cve }}
            </x-daisy::ui.data-display.badge>
        @endif

        @if($severity)
            @php
                $severityColorMap = [
                    'high' => 'error',
                    'medium' => 'warning',
                    'low' => 'info',
                ];
                $severityColor = $severityColorMap[$severity] ?? 'neutral';
                $severityLabel = __('changelog.severity_'.$severity);
            @endphp
            <x-daisy::ui.data-display.badge :color="$severityColor" size="xs" variant="soft">
                {{ $severityLabel }}
            </x-daisy::ui.data-display.badge>
        @endif
    </div>

    {{-- Zone du texte/description --}}
    <div class="mb-3">
        <p class="text-sm text-base-content/90 leading-relaxed">
            {{ $description }}
        </p>

        @if($category)
            <p class="mt-1 text-xs text-base-content/60">
                {{ __('changelog.category_'.strtolower($category)) ?? $category }}
            </p>
        @endif
    </div>

    <div class="mt-3 space-y-2 text-sm text-base-content/80">
        @if(!empty($formattedIssues))
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs uppercase tracking-wide text-base-content/50">{{ __('changelog.issues') }}</span>
                @foreach($formattedIssues as $issue)
                    <a href="{{ $issue['url'] }}" target="_blank" rel="noopener noreferrer" class="link link-primary text-xs font-semibold">
                        #{{ $issue['number'] }}
                    </a>
                @endforeach
            </div>
        @endif

        @if($migration && $migrationGuide)
            <div class="flex items-center gap-2 text-xs">
                <span class="uppercase tracking-wide text-base-content/50">{{ __('changelog.migration_guide') }}</span>
                <a href="{{ $migrationGuide }}" target="_blank" rel="noopener noreferrer" class="link link-warning font-semibold">
                    {{ __('changelog.view_migration_guide') }}
                </a>
            </div>
        @endif

        @if(!empty($contributors))
            <div class="flex flex-wrap items-center gap-2 text-xs">
                <span class="uppercase tracking-wide text-base-content/50">{{ __('changelog.contributors') }}</span>
                <span>{{ implode(', ', $contributors) }}</span>
            </div>
        @endif
    </div>

    @if($image)
        <div class="mt-3 rounded-box card-border bg-base-100">
            <x-daisy::ui.media.lightbox
                :images="[['src' => $image, 'thumb' => $image, 'alt' => $description, 'caption' => __('changelog.view_screenshot')]]"
                cols="grid-cols-1"
                gap="gap-0"
            />
        </div>
    @endif
</div>


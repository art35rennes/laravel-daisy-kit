@props([
    'title' => __('daisy::changelog.changelog'),
    'theme' => null,
    // Changelog data
    'versions' => [], // Array of version data
    'currentVersion' => null, // Current app version
    // Routes
    'rssUrl' => null, // RSS feed URL (optional)
    'atomUrl' => null, // Atom feed URL (optional)
    'documentationUrl' => null, // Optional CTA target
    // Options
    'showFilters' => true,
    'showSearch' => true,
    'showVersionBadge' => true,
    'showDocumentationCta' => null, // Auto-detect from documentationUrl when null
    'groupByMonth' => false, // Group versions by month
    'highlightCurrent' => true, // Highlight current version
    'expandLatest' => true, // Expand latest version by default
    'itemsPerPage' => 20, // If pagination enabled
    'pagination' => false, // Enable pagination
])

@php
    use Carbon\Carbon;

    // Auto-détecter la version actuelle si non fournie
    if (is_null($currentVersion)) {
        $currentVersion = config('app.version');
    }

    $documentationUrl ??= Route::has('templates.documentation.changelog')
        ? route('templates.documentation.changelog')
        : null;

    $showDocumentationCta ??= filled($documentationUrl);

    // Normaliser les versions : déterminer isCurrent si non fourni
    $normalizedVersions = collect($versions)->map(function($version) use ($currentVersion) {
        if (!isset($version['isCurrent']) && $currentVersion) {
            $version['isCurrent'] = ($version['version'] ?? null) === $currentVersion;
        }
        return $version;
    })->values()->all();

    // Grouper par mois si demandé
    if ($groupByMonth && !empty($normalizedVersions)) {
        $groupedVersions = collect($normalizedVersions)->groupBy(function($version) {
            try {
                $date = $version['date'] ?? now();
                return Carbon::parse($date)->format('Y-m');
            } catch (\Exception $e) {
                return 'unknown';
            }
        })->sortKeysDesc()->all();
    } else {
        $groupedVersions = ['all' => $normalizedVersions];
    }

    // Déterminer quelle version doit être ouverte par défaut
    $latestVersionIndex = null;
    if ($expandLatest && !empty($normalizedVersions)) {
        $latestVersionIndex = 0; // La première version est la plus récente
    }
@endphp

<x-daisy::layout.app :title="$title" :theme="$theme" :container="true">
    <x-daisy::ui.partials.theme-selector position="fixed" placement="top-right" />
    <div class="changelog-container mx-auto max-w-5xl space-y-8 py-4">
        <section class="rounded-box bg-base-100/90 p-8 shadow">
            <div class="flex flex-wrap items-start gap-6">
                <div class="flex-1 min-w-0 space-y-3">
                    <p class="text-sm font-semibold uppercase tracking-wide text-primary">{{ __('daisy::changelog.changelog') }}</p>
                    <h1 class="text-4xl font-semibold text-base-content">{{ $title }}</h1>
                    <p class="text-base text-base-content/70">
                        {{ __('daisy::changelog.intro_description') }}
                    </p>
                </div>
                <div class="flex flex-col items-end gap-3">
                    @if($showVersionBadge && $currentVersion)
                        <x-daisy::ui.data-display.badge color="primary" size="lg">
                            {{ __('daisy::changelog.current_version') }} {{ $currentVersion }}
                        </x-daisy::ui.data-display.badge>
                    @endif
                    @if($showDocumentationCta && $documentationUrl)
                        <x-daisy::ui.inputs.button
                            tag="a"
                            :href="$documentationUrl"
                            color="primary"
                            class="btn-wide"
                        >
                            {{ __('daisy::changelog.cta_get_template') }}
                        </x-daisy::ui.inputs.button>
                    @endif
                </div>
            </div>
        </section>

        @if($showSearch || $showFilters)
            <x-daisy::ui.changelog.changelog-toolbar
                :showSearch="$showSearch"
                :showFilters="$showFilters"
            />
        @endif

        @if(empty($normalizedVersions))
            <x-daisy::ui.feedback.empty-state
                :title="__('daisy::changelog.no_versions')"
                :message="__('daisy::changelog.no_results')"
                class="rounded-box card-border bg-base-100/80"
            />
        @else
            <div
                class="changelog-versions space-y-10"
                data-changelog-container
                @if($showSearch || $showFilters) data-module="changelog-filter" data-all-types-label="{{ __('daisy::changelog.all_types') }}" @endif
            >
                @if($groupByMonth)
                    @foreach($groupedVersions as $month => $monthVersions)
                        @php
                            try {
                                $monthDate = Carbon::createFromFormat('Y-m', $month);
                                $monthLabel = $monthDate->format('F Y');
                            } catch (\Exception $e) {
                                $monthLabel = $month;
                            }
                        @endphp
                        <div class="changelog-month-group space-y-6">
                            <h2 class="text-lg font-semibold text-base-content/70">
                                {{ $monthLabel }}
                            </h2>
                            <div class="space-y-8">
                                @foreach($monthVersions as $index => $version)
                                    <x-daisy::ui.changelog.changelog-version-item
                                        :version="$version['version'] ?? null"
                                        :date="$version['date'] ?? null"
                                        :isCurrent="(bool)($version['isCurrent'] ?? false)"
                                        :yanked="(bool)($version['yanked'] ?? false)"
                                        :tagUrl="$version['tagUrl'] ?? null"
                                        :compareUrl="$version['compareUrl'] ?? null"
                                        :items="$version['items'] ?? []"
                                        :changes="$version['changes'] ?? []"
                                        :expandByDefault="$expandLatest && $loop->first"
                                        :highlightCurrent="$highlightCurrent"
                                    />
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="space-y-8">
                        @foreach($normalizedVersions as $index => $version)
                            <x-daisy::ui.changelog.changelog-version-item
                                :version="$version['version'] ?? null"
                                :date="$version['date'] ?? null"
                                :isCurrent="(bool)($version['isCurrent'] ?? false)"
                                :yanked="(bool)($version['yanked'] ?? false)"
                                :tagUrl="$version['tagUrl'] ?? null"
                                :compareUrl="$version['compareUrl'] ?? null"
                                :items="$version['items'] ?? []"
                                :changes="$version['changes'] ?? []"
                                :expandByDefault="$expandLatest && $index === 0"
                                :highlightCurrent="$highlightCurrent"
                            />
                        @endforeach
                    </div>
                @endif
                <div class="changelog-no-results text-center py-8 text-base-content/70" data-changelog-empty hidden>
                    {{ __('daisy::changelog.no_results') }}
                </div>
            </div>

            @if($pagination && isset($paginationData))
                <div class="flex justify-center">
                    <x-daisy::ui.navigation.pagination
                        :total="$paginationData['total'] ?? 1"
                        :current="$paginationData['current'] ?? 1"
                    />
                </div>
            @endif
        @endif
    </div>
</x-daisy::layout.app>

@props([
    'title' => __('changelog.changelog'),
    'theme' => null,
    // Changelog data
    'versions' => [], // Array of version data
    'currentVersion' => null, // Current app version
    // Routes
    'rssUrl' => null, // RSS feed URL (optional)
    'atomUrl' => null, // Atom feed URL (optional)
    // Options
    'showFilters' => true,
    'showSearch' => true,
    'showVersionBadge' => true,
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
                    <p class="text-sm font-semibold uppercase tracking-wide text-primary">{{ __('changelog.changelog') }}</p>
                    <h1 class="text-4xl font-semibold text-base-content">{{ $title }}</h1>
                    <p class="text-base text-base-content/70">
                        {{ __('changelog.intro_description') }}
                    </p>
                </div>
                <div class="flex flex-col items-end gap-3">
                    @if($showVersionBadge && $currentVersion)
                        <x-daisy::ui.data-display.badge color="primary" size="lg">
                            {{ __('changelog.current_version') }} {{ $currentVersion }}
                        </x-daisy::ui.data-display.badge>
                    @endif
                    <x-daisy::ui.inputs.button
                        tag="a"
                        :href="Route::has('templates.documentation.changelog') ? route('templates.documentation.changelog') : '#'"
                        color="primary"
                        class="btn-wide"
                    >
                        {{ __('changelog.cta_get_template') }}
                    </x-daisy::ui.inputs.button>
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
                :title="__('changelog.no_versions')"
                :message="__('changelog.no_results')"
                class="rounded-box card-border bg-base-100/80"
            />
        @else
            <div class="changelog-versions space-y-10" data-changelog-container>
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

    @if($showSearch || $showFilters)
        @push('scripts')
        <script>
            (function() {
                const container = document.querySelector('[data-changelog-container]');
                if (!container) return;

                const searchInput = document.querySelector('[data-changelog-search]');
                const filterInputs = document.querySelectorAll('input[name=\"changelog-filter\"]');

                function filterChangelog() {
                    const searchTerm = searchInput?.value.toLowerCase() || '';
                    const selectedFilter = Array.from(filterInputs).find(input => input.checked)?.ariaLabel?.toLowerCase() || '';

                    const versionItems = container.querySelectorAll('.changelog-version-item');
                    let visibleCount = 0;

                    versionItems.forEach(versionItem => {
                        const changeItems = versionItem.querySelectorAll('.changelog-change-item');
                        let versionVisible = false;

                        changeItems.forEach(changeItem => {
                            const description = changeItem.textContent.toLowerCase();
                            const typeBadge = changeItem.querySelector('.badge')?.textContent.toLowerCase() || '';

                            const matchesSearch = !searchTerm || description.includes(searchTerm);
                            const matchesFilter = !selectedFilter ||
                                selectedFilter === @json(__('changelog.all_types')).toLowerCase() ||
                                typeBadge.includes(selectedFilter);

                            if (matchesSearch && matchesFilter) {
                                changeItem.style.display = '';
                                versionVisible = true;
                            } else {
                                changeItem.style.display = 'none';
                            }
                        });

                        if (versionVisible) {
                            versionItem.style.display = '';
                            visibleCount++;
                        } else {
                            versionItem.style.display = 'none';
                        }
                    });

                    const noResults = container.querySelector('.changelog-no-results');
                    if (visibleCount === 0) {
                        if (!noResults) {
                            const message = document.createElement('div');
                            message.className = 'changelog-no-results text-center py-8 text-base-content/70';
                            message.innerText = @json(__('changelog.no_results'));
                            container.appendChild(message);
                        }
                    } else if (noResults) {
                        noResults.remove();
                    }
                }

                if (searchInput) {
                    searchInput.addEventListener('input', filterChangelog);
                }

                filterInputs.forEach(input => {
                    input.addEventListener('change', filterChangelog);
                });
            })();
        </script>
        @endpush
    @endif
</x-daisy::layout.app>


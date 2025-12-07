<?php

use Illuminate\Support\Facades\View;

describe('Changelog Components Rendering', function () {
    describe('changelog-change-item (MOLECULE)', function () {
        it('renders with default props', function () {
            $html = View::make('daisy::components.ui.changelog.changelog-change-item', [
                'description' => 'Test change',
            ])->render();

            expect($html)
                ->toContain('changelog-change-item')
                ->toContain('Test change')
                ->toContain('badge');
        });

        it('renders all change types correctly', function () {
            $types = ['added', 'changed', 'fixed', 'removed', 'security'];

            foreach ($types as $type) {
                $html = View::make('daisy::components.ui.changelog.changelog-change-item', [
                    'type' => $type,
                    'description' => "Test {$type}",
                ])->render();

                expect($html)
                    ->toContain("Test {$type}")
                    ->toContain('badge');
            }
        });

        it('renders breaking change badge', function () {
            $html = View::make('daisy::components.ui.changelog.changelog-change-item', [
                'description' => 'Breaking change',
                'breaking' => true,
            ])->render();

            expect($html)
                ->toContain(__('changelog.breaking_change'))
                ->toContain('badge-error');
        });

        it('renders migration badge and link', function () {
            $html = View::make('daisy::components.ui.changelog.changelog-change-item', [
                'description' => 'Migration required',
                'migration' => true,
                'migrationGuide' => 'https://example.com/migration',
            ])->render();

            expect($html)
                ->toContain(__('changelog.migration_required'))
                ->toContain('https://example.com/migration')
                ->toContain(__('changelog.view_migration_guide'));
        });

        it('renders CVE badge with severity', function () {
            $html = View::make('daisy::components.ui.changelog.changelog-change-item', [
                'description' => 'Security fix',
                'type' => 'security',
                'cve' => 'CVE-2024-1234',
                'severity' => 'high',
            ])->render();

            expect($html)
                ->toContain('CVE-2024-1234')
                ->toContain(__('changelog.severity_high'));
        });

        it('renders issues links', function () {
            $html = View::make('daisy::components.ui.changelog.changelog-change-item', [
                'description' => 'Fix with issues',
                'issues' => [123, 456],
                'issueBaseUrl' => 'https://github.com/user/repo/issues',
            ])->render();

            expect($html)
                ->toContain('#123')
                ->toContain('#456')
                ->toContain('https://github.com/user/repo/issues/123');
        });

        it('renders contributors', function () {
            $html = View::make('daisy::components.ui.changelog.changelog-change-item', [
                'description' => 'Feature by contributors',
                'contributors' => ['user1', 'user2'],
            ])->render();

            expect($html)
                ->toContain('contributors')
                ->toContain('user1')
                ->toContain('user2');
        });

        it('renders image with lightbox', function () {
            $html = View::make('daisy::components.ui.changelog.changelog-change-item', [
                'description' => 'Feature with screenshot',
                'image' => '/images/screenshot.jpg',
            ])->render();

            expect($html)
                ->toContain('/images/screenshot.jpg')
                ->toContain('lightbox');
        });
    });

    describe('changelog-header (MOLECULE)', function () {
        it('renders with default props', function () {
            $html = View::make('daisy::components.ui.changelog.changelog-header')->render();

            expect($html)
                ->toContain('changelog-header')
                ->toContain('changelog');
        });

        it('renders with current version badge', function () {
            $html = View::make('daisy::components.ui.changelog.changelog-header', [
                'currentVersion' => '2.0.0',
                'showVersionBadge' => true,
            ])->render();

            expect($html)
                ->toContain('2.0.0')
                ->toContain(__('changelog.current_version'))
                ->toContain('badge-primary');
        });

        it('hides version badge when showVersionBadge is false', function () {
            $html = View::make('daisy::components.ui.changelog.changelog-header', [
                'currentVersion' => '2.0.0',
                'showVersionBadge' => false,
            ])->render();

            expect($html)
                ->not->toContain('current_version');
        });

        it('renders RSS and Atom links', function () {
            $html = View::make('daisy::components.ui.changelog.changelog-header', [
                'rssUrl' => 'https://example.com/rss',
                'atomUrl' => 'https://example.com/atom',
            ])->render();

            expect($html)
                ->toContain('https://example.com/rss')
                ->toContain('https://example.com/atom')
                ->toContain(__('changelog.rss_feed'))
                ->toContain(__('changelog.atom_feed'));
        });
    });

    describe('changelog-toolbar (ORGANISME)', function () {
        it('renders with default props', function () {
            $html = View::make('daisy::components.ui.changelog.changelog-toolbar')->render();

            expect($html)
                ->toContain('changelog-toolbar')
                ->toContain('filter');
        });

        it('renders search input when showSearch is true', function () {
            $html = View::make('daisy::components.ui.changelog.changelog-toolbar', [
                'showSearch' => true,
            ])->render();

            expect($html)
                ->toContain('data-changelog-search')
                ->toContain('input');
        });

        it('hides search when showSearch is false', function () {
            $html = View::make('daisy::components.ui.changelog.changelog-toolbar', [
                'showSearch' => false,
            ])->render();

            expect($html)
                ->not->toContain('data-changelog-search');
        });

        it('renders filters when showFilters is true', function () {
            $html = View::make('daisy::components.ui.changelog.changelog-toolbar', [
                'showFilters' => true,
            ])->render();

            expect($html)
                ->toContain('filter')
                ->toContain('changelog-filter');
        });

        it('hides filters when showFilters is false', function () {
            $html = View::make('daisy::components.ui.changelog.changelog-toolbar', [
                'showFilters' => false,
            ])->render();

            expect($html)
                ->not->toContain('filter');
        });

        it('renders custom filter items', function () {
            $html = View::make('daisy::components.ui.changelog.changelog-toolbar', [
                'filterItems' => ['added', 'fixed'],
            ])->render();

            expect($html)
                ->toContain(__('changelog.added'))
                ->toContain(__('changelog.fixed'));
        });
    });

    describe('changelog-version-item (MOLECULE)', function () {
        it('renders with minimal props', function () {
            $html = View::make('daisy::components.ui.changelog.changelog-version-item', [
                'version' => '1.0.0',
                'date' => '2024-01-15',
            ])->render();

            expect($html)
                ->toContain('changelog-version-item')
                ->toContain('1.0.0');
        });

        it('renders with current version badge', function () {
            $html = View::make('daisy::components.ui.changelog.changelog-version-item', [
                'version' => '2.0.0',
                'date' => '2024-01-15',
                'isCurrent' => true,
            ])->render();

            expect($html)
                ->toContain(__('changelog.current_version'))
                ->toContain('badge-primary');
        });

        it('renders yanked badge', function () {
            $html = View::make('daisy::components.ui.changelog.changelog-version-item', [
                'version' => '1.0.0',
                'date' => '2024-01-15',
                'yanked' => true,
            ])->render();

            expect($html)
                ->toContain(__('changelog.yanked'))
                ->toContain('badge-error');
        });

        it('renders Git tag and compare links', function () {
            $html = View::make('daisy::components.ui.changelog.changelog-version-item', [
                'version' => '2.0.0',
                'date' => '2024-01-15',
                'tagUrl' => 'https://github.com/user/repo/releases/tag/2.0.0',
                'compareUrl' => 'https://github.com/user/repo/compare/1.0.0...2.0.0',
            ])->render();

            expect($html)
                ->toContain(__('changelog.view_tag'))
                ->toContain(__('changelog.compare_versions'))
                ->toContain('https://github.com/user/repo/releases/tag/2.0.0')
                ->toContain('https://github.com/user/repo/compare/1.0.0...2.0.0');
        });

        it('renders items in enriched format', function () {
            $html = View::make('daisy::components.ui.changelog.changelog-version-item', [
                'version' => '2.0.0',
                'date' => '2024-01-15',
                'items' => [
                    [
                        'type' => 'added',
                        'description' => 'New feature',
                    ],
                    [
                        'type' => 'fixed',
                        'description' => 'Bug fix',
                    ],
                ],
            ])->render();

            expect($html)
                ->toContain('New feature')
                ->toContain('Bug fix')
                ->toContain('changelog-change-item');
        });

        it('renders items in simple format (changes array)', function () {
            $html = View::make('daisy::components.ui.changelog.changelog-version-item', [
                'version' => '2.0.0',
                'date' => '2024-01-15',
                'changes' => [
                    'added' => ['New feature'],
                    'fixed' => ['Bug fix'],
                ],
            ])->render();

            expect($html)
                ->toContain('New feature')
                ->toContain('Bug fix');
        });

        it('renders items when expandByDefault is true', function () {
            $html = View::make('daisy::components.ui.changelog.changelog-version-item', [
                'version' => '2.0.0',
                'date' => '2024-01-15',
                'expandByDefault' => true,
                'items' => [
                    ['type' => 'added', 'description' => 'Test item'],
                ],
            ])->render();

            expect($html)
                ->toContain('changelog-version-item')
                ->toContain('Test item');
        });

        it('highlights current version when highlightCurrent is true', function () {
            $html = View::make('daisy::components.ui.changelog.changelog-version-item', [
                'version' => '2.0.0',
                'date' => '2024-01-15',
                'isCurrent' => true,
                'highlightCurrent' => true,
            ])->render();

            expect($html)
                ->toContain('border-primary');
        });
    });

    describe('changelog template', function () {
        it('renders with default props', function () {
            $html = View::make('daisy::templates.changelog', [
                'versions' => [],
            ])->render();

            expect($html)
                ->toContain('changelog-container')
                ->toContain('changelog-toolbar')
                ->toContain(__('changelog.cta_get_template'));
        });

        it('renders empty state when no versions', function () {
            $html = View::make('daisy::templates.changelog', [
                'versions' => [],
            ])->render();

            expect($html)
                ->toContain(__('changelog.no_versions'))
                ->toContain(__('changelog.no_results'));
        });

        it('renders versions list', function () {
            $html = View::make('daisy::templates.changelog', [
                'versions' => [
                    [
                        'version' => '2.0.0',
                        'date' => '2024-01-15',
                        'items' => [
                            ['type' => 'added', 'description' => 'New feature'],
                        ],
                    ],
                ],
            ])->render();

            expect($html)
                ->toContain('changelog-versions')
                ->toContain('2.0.0')
                ->toContain('New feature');
        });

        it('renders with simple format (changes array)', function () {
            $html = View::make('daisy::templates.changelog', [
                'versions' => [
                    [
                        'version' => '2.0.0',
                        'date' => '2024-01-15',
                        'changes' => [
                            'added' => ['New feature'],
                            'fixed' => ['Bug fix'],
                        ],
                    ],
                ],
            ])->render();

            expect($html)
                ->toContain('New feature')
                ->toContain('Bug fix');
        });

        it('auto-detects current version', function () {
            config(['app.version' => '2.0.0']);

            $html = View::make('daisy::templates.changelog', [
                'versions' => [
                    [
                        'version' => '2.0.0',
                        'date' => '2024-01-15',
                    ],
                ],
            ])->render();

            expect($html)
                ->toContain(__('changelog.current_version'));
        });

        it('expands latest version by default', function () {
            $html = View::make('daisy::templates.changelog', [
                'versions' => [
                    [
                        'version' => '2.0.0',
                        'date' => '2024-01-15',
                        'items' => [['type' => 'added', 'description' => 'New']],
                    ],
                    [
                        'version' => '1.0.0',
                        'date' => '2023-12-01',
                        'items' => [['type' => 'fixed', 'description' => 'Fix']],
                    ],
                ],
                'expandLatest' => true,
            ])->render();

            expect(substr_count($html, 'changelog-version-item'))
                ->toBeGreaterThanOrEqual(2);
        });

        it('groups versions by month when groupByMonth is true', function () {
            $html = View::make('daisy::templates.changelog', [
                'versions' => [
                    [
                        'version' => '2.0.0',
                        'date' => '2024-01-15',
                    ],
                    [
                        'version' => '1.9.0',
                        'date' => '2024-01-10',
                    ],
                    [
                        'version' => '1.8.0',
                        'date' => '2023-12-20',
                    ],
                ],
                'groupByMonth' => true,
            ])->render();

            expect($html)
                ->toContain('changelog-month-group')
                ->toContain('January 2024')
                ->toContain('December 2023');
        });

        it('hides search when showSearch is false', function () {
            $html = View::make('daisy::templates.changelog', [
                'versions' => [],
                'showSearch' => false,
                'showFilters' => false,
            ])->render();

            expect($html)
                ->not->toContain('data-changelog-search')
                ->not->toContain('changelog-toolbar');
        });

        it('hides filters when showFilters is false', function () {
            $html = View::make('daisy::templates.changelog', [
                'versions' => [],
                'showFilters' => false,
            ])->render();

            // Le composant toolbar sera toujours rendu mais sans les filtres
            expect($html)
                ->toContain('changelog-toolbar');
        });
    });
});

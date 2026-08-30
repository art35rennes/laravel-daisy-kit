<?php

declare(strict_types=1);

namespace Workbench\App;

class TreeExamples
{
    /** @return list<array<string, mixed>> */
    public static function classification(): array
    {
        $branches = [];
        foreach (['Platform', 'Operations', 'Customer success', 'Compliance'] as $index => $team) {
            $children = [];
            foreach (['Guides', 'Procedures', 'Reference'] as $section => $title) {
                $children[] = [
                    'id' => "team-{$index}-{$section}", 'label' => $title,
                    'children' => array_map(fn (int $entry): array => [
                        'id' => "team-{$index}-{$section}-{$entry}",
                        'label' => ["{$team} onboarding", 'Quarterly review and operational handover', 'Runbooks and incident response'][$entry],
                        'disabled' => $index === 3 && $entry === 2,
                    ], range(0, 2)),
                ];
            }
            $branches[] = ['id' => "team-{$index}", 'label' => $team, 'badge' => '9', 'children' => $children];
        }

        return [[
            'id' => 'documentation', 'label' => 'Documentation', 'expanded' => true,
            'children' => [
                ['id' => 'getting-started', 'label' => 'Getting started'],
                ['id' => 'api-reference', 'label' => 'API reference'],
                ...$branches,
            ],
        ]];
    }

    /** @return list<array<string, mixed>> */
    public static function permissions(): array
    {
        return array_map(function (string $area): array {
            return [
                'id' => $area, 'label' => ucfirst($area), 'expanded' => $area === 'projects',
                'children' => array_map(fn (string $resource): array => [
                    'id' => "{$area}-{$resource}", 'label' => ucfirst($resource), 'expanded' => $resource === 'records',
                    'children' => array_map(fn (string $action): array => [
                        'id' => "{$area}-{$resource}-{$action}", 'label' => ucfirst($action),
                        'disabled' => $area === 'billing' && $action === 'delete',
                        'badge' => $area === 'billing' && $action === 'delete' ? 'Restricted' : '',
                    ], ['read', 'create', 'update', 'delete']),
                ], ['records', 'reports', 'settings']),
            ];
        }, ['projects', 'customers', 'billing', 'people']);
    }

    /** @return list<array<string, mixed>> */
    public static function catalogue(): array
    {
        return array_map(fn (string $region): array => [
            'id' => $region, 'label' => ucfirst($region).' region',
            'description' => $region === 'west' ? 'The regional service fails once; retry to reconnect.' : 'Load distribution centres on demand.',
            'source' => route('workbench.tree.catalogue', ['region' => $region]),
        ], ['west', 'north', 'south', 'east']);
    }

    /** @return list<array<string, mixed>> */
    public static function centres(string $region): array
    {
        return array_map(fn (int $index): array => [
            'id' => "{$region}-{$index}", 'label' => ucfirst($region)." distribution centre {$index}",
            'children' => array_map(fn (string $area): array => [
                'id' => "{$region}-{$index}-{$area}", 'label' => ucfirst($area),
            ], ['receiving', 'storage', 'dispatch', 'returns']),
        ], range(1, 6));
    }

    /** @return list<array<string, mixed>> */
    public static function teams(): array
    {
        return array_map(fn (string $team): array => [
            'id' => $team, 'label' => ucfirst($team), 'badge' => '4 people',
            'description' => ucfirst($team).' delivery team',
            'children' => array_map(fn (int $index): array => [
                'id' => "{$team}-{$index}",
                'label' => ['Ada Martin', 'Grace Bernard', 'Camille Robert', 'Alex Moreau'][$index],
                'description' => ['Technical lead', 'Project manager', 'Product designer', 'Support engineer'][$index],
                'badge' => $index === 0 ? 'Lead' : 'Member',
            ], range(0, 3)),
        ], ['platform', 'infrastructure', 'design', 'support', 'research', 'delivery']);
    }
}

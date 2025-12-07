@props([
    'props' => [],
    'propsDetailed' => null,
    'category' => null,
    'name' => null,
    'showSlots' => false,
    'slots' => [],
])

@php
    use App\Helpers\DocsHelper;
    
    // Si propsDetailed n'est pas fourni mais qu'on a category et name, les parser
    if (empty($propsDetailed) && $category && $name) {
        $propsDetailed = DocsHelper::getComponentPropsDetailed($category, $name);
    }
    
    // Fallback: convertir les props simples en format détaillé
    if (empty($propsDetailed) && !empty($props)) {
        $propsDetailed = array_map(fn($prop) => ['name' => $prop], $props);
    }
@endphp

@if(!empty($propsDetailed) || $showSlots)
    <section id="api" class="mt-10">
        <h2>API</h2>
        @if(!empty($propsDetailed))
            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Prop</th>
                            <th>Type</th>
                            <th>Défaut</th>
                            <th>Valeurs</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($propsDetailed as $prop)
                            <tr>
                                <td><code>{{ $prop['name'] }}</code></td>
                                <td><code class="text-xs">{{ $prop['type'] ?? 'mixed' }}</code></td>
                                <td>
                                    @if(isset($prop['default']))
                                        @if(is_null($prop['default']))
                                            <code class="text-xs text-base-content/50">null</code>
                                        @elseif(is_bool($prop['default']))
                                            <code class="text-xs">{{ $prop['default'] ? 'true' : 'false' }}</code>
                                        @elseif(is_string($prop['default']))
                                            <code class="text-xs">"{{ $prop['default'] }}"</code>
                                        @else
                                            <code class="text-xs">{{ $prop['default'] }}</code>
                                        @endif
                                    @else
                                        <span class="text-xs text-base-content/50">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($prop['values']))
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($prop['values'] as $value)
                                                <code class="text-xs bg-base-200 px-1 rounded">{{ $value }}</code>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-xs text-base-content/50">—</span>
                                    @endif
                                </td>
                                <td class="text-sm">
                                    {{ $prop['description'] ?? '' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
        @if($showSlots && !empty($slots))
            <div class="mt-6">
                <h3 class="text-base font-semibold">Slots</h3>
                <ul class="list-disc list-inside text-sm space-y-1">
                    @foreach($slots as $slot)
                        <li><code>{{ $slot['name'] }}</code> : {{ $slot['description'] ?? '' }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        {{ $slot ?? '' }}
    </section>
@endif


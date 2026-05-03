@props([
    'name' => null,
    'items' => [],
    'type' => 'radio',
    'value' => null,
    'values' => [],
    'legend' => null,
    'hint' => null,
    'required' => false,
    'columns' => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
    'color' => 'primary',
    'size' => 'md',
    'showControl' => true,
    'iconPrefix' => null,
])

@php
    if (blank($name)) {
        throw new \InvalidArgumentException('The [name] prop is required for the choice-card-group component.');
    }

    $inputType = $type === 'checkbox' ? 'checkbox' : 'radio';
    $inputName = $inputType === 'checkbox' && ! str_ends_with($name, '[]') ? "{$name}[]" : $name;
    $fieldId = $attributes->get('id') ?? 'choice-card-group-'.str_replace(['[', ']'], '', $name).'-'.uniqid();

    $selectedRadioValue = old($name, $value);
    $selectedCheckboxValues = old(str_replace('[]', '', $name), $values);

    if ($selectedCheckboxValues instanceof \Illuminate\Support\Collection) {
        $selectedCheckboxValues = $selectedCheckboxValues->all();
    }

    if (! is_array($selectedCheckboxValues)) {
        $selectedCheckboxValues = filled($selectedCheckboxValues) ? [$selectedCheckboxValues] : [];
    }

    $validColors = ['neutral', 'primary', 'secondary', 'accent', 'info', 'success', 'warning', 'error'];
    $accentColor = in_array($color, $validColors, true) ? $color : 'primary';

    $controlClass = $inputType === 'checkbox' ? 'checkbox' : 'radio';
    $controlClass .= " {$controlClass}-{$accentColor}";

    $accentClasses = match($accentColor) {
        'neutral' => [
            'card' => 'peer-focus-visible:outline-neutral peer-checked:border-neutral peer-checked:bg-neutral/10',
            'control' => 'peer-checked:border-neutral peer-checked:bg-neutral',
        ],
        'secondary' => [
            'card' => 'peer-focus-visible:outline-secondary peer-checked:border-secondary peer-checked:bg-secondary/10',
            'control' => 'peer-checked:border-secondary peer-checked:bg-secondary',
        ],
        'accent' => [
            'card' => 'peer-focus-visible:outline-accent peer-checked:border-accent peer-checked:bg-accent/10',
            'control' => 'peer-checked:border-accent peer-checked:bg-accent',
        ],
        'info' => [
            'card' => 'peer-focus-visible:outline-info peer-checked:border-info peer-checked:bg-info/10',
            'control' => 'peer-checked:border-info peer-checked:bg-info',
        ],
        'success' => [
            'card' => 'peer-focus-visible:outline-success peer-checked:border-success peer-checked:bg-success/10',
            'control' => 'peer-checked:border-success peer-checked:bg-success',
        ],
        'warning' => [
            'card' => 'peer-focus-visible:outline-warning peer-checked:border-warning peer-checked:bg-warning/10',
            'control' => 'peer-checked:border-warning peer-checked:bg-warning',
        ],
        'error' => [
            'card' => 'peer-focus-visible:outline-error peer-checked:border-error peer-checked:bg-error/10',
            'control' => 'peer-checked:border-error peer-checked:bg-error',
        ],
        default => [
            'card' => 'peer-focus-visible:outline-primary peer-checked:border-primary peer-checked:bg-primary/10',
            'control' => 'peer-checked:border-primary peer-checked:bg-primary',
        ],
    };

    $sizeClasses = match($size) {
        'sm' => [
            'card' => 'gap-3 p-4',
            'icon' => 'sm',
            'title' => 'text-sm',
            'description' => 'text-xs',
            'control' => $inputType === 'checkbox' ? 'checkbox-sm' : 'radio-sm',
        ],
        'lg' => [
            'card' => 'gap-4 p-6',
            'icon' => 'lg',
            'title' => 'text-base',
            'description' => 'text-sm',
            'control' => $inputType === 'checkbox' ? 'checkbox-md' : 'radio-md',
        ],
        default => [
            'card' => 'gap-4 p-5',
            'icon' => 'md',
            'title' => 'text-sm',
            'description' => 'text-sm',
            'control' => $inputType === 'checkbox' ? 'checkbox-sm' : 'radio-sm',
        ],
    };

    $controlClass .= ' '.$sizeClasses['control'];
@endphp

<fieldset
    {{ $attributes->except('id')->merge(['class' => 'space-y-3']) }}
    id="{{ $fieldId }}"
>
    @if($legend)
        <legend class="text-sm font-medium text-base-content">
            {{ $legend }}
            @if($required)
                <span aria-hidden="true" class="ml-1 text-error">*</span>
            @endif
        </legend>
    @endif

    @if($hint)
        <p class="text-sm text-base-content/70">{{ $hint }}</p>
    @endif

    <div class="grid {{ $columns }} gap-3">
        @foreach($items as $index => $item)
            @php
                $itemValue = is_array($item) ? ($item['value'] ?? $index) : $index;
                $itemLabel = is_array($item) ? ($item['label'] ?? $itemValue) : $item;
                $itemDescription = is_array($item) ? ($item['description'] ?? null) : null;
                $itemIcon = is_array($item) ? ($item['icon'] ?? null) : null;
                $itemBadge = is_array($item) ? ($item['badge'] ?? null) : null;
                $itemDisabled = is_array($item) ? (bool) ($item['disabled'] ?? false) : false;
                $itemId = "{$fieldId}-".\Illuminate\Support\Str::slug((string) $itemValue, '-');

                $isChecked = $inputType === 'checkbox'
                    ? in_array((string) $itemValue, array_map('strval', $selectedCheckboxValues), true)
                    : (string) $selectedRadioValue === (string) $itemValue;
            @endphp

            <label
                for="{{ $itemId }}"
                @class([
                    'group relative block min-h-full',
                    'cursor-pointer' => ! $itemDisabled,
                    'cursor-not-allowed opacity-60' => $itemDisabled,
                ])
            >
                <input
                    id="{{ $itemId }}"
                    type="{{ $inputType }}"
                    name="{{ $inputName }}"
                    value="{{ $itemValue }}"
                    class="peer sr-only"
                    @checked($isChecked)
                    @disabled($itemDisabled)
                    @required($required && $inputType === 'radio' && $loop->first)
                    @if($required && $inputType === 'checkbox') required @endif
                />

                @if($showControl)
                    <span aria-hidden="true" class="{{ $controlClass }} {{ $accentClasses['control'] }} pointer-events-none absolute right-4 top-4 z-10 border-base-300 bg-base-100 transition"></span>
                @endif

                <span class="flex min-h-full rounded-box border border-base-300 bg-base-100 {{ $sizeClasses['card'] }} {{ $showControl ? 'pe-12' : '' }} {{ $accentClasses['card'] }} shadow-sm transition peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-disabled:bg-base-200">
                    @if($itemIcon)
                        <span class="mt-0.5 flex size-10 shrink-0 items-center justify-center rounded-field bg-base-200 text-base-content/70">
                            <x-daisy::ui.advanced.icon :name="$itemIcon" :prefix="$iconPrefix" :size="$sizeClasses['icon']" />
                        </span>
                    @endif

                    <span class="flex min-w-0 flex-1 flex-col gap-1">
                        <span class="flex items-start justify-between gap-3">
                            <span class="font-medium leading-6 text-base-content {{ $sizeClasses['title'] }}">{{ $itemLabel }}</span>

                            @if($itemBadge)
                                <span class="badge badge-sm">{{ $itemBadge }}</span>
                            @endif
                        </span>

                        @if($itemDescription)
                            <span class="leading-5 text-base-content/70 {{ $sizeClasses['description'] }}">{{ $itemDescription }}</span>
                        @endif
                    </span>
                </span>
            </label>
        @endforeach
    </div>
</fieldset>

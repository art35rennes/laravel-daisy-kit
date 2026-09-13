@if ($tableView['filters'] !== [] || ($tableView['filterMode'] === 'manual' && $tableView['hasColumnFilters']))
    <fieldset class="daisy-kit-table__filters">
        <legend class="daisy-kit-table__filters-title">{{ $tableView['labels']['filters'] }}</legend>

        @foreach ($tableView['filters'] as $filter)
            @php
                $filterId = is_string($filter['id'] ?? null) ? $filter['id'] : '';
                $filterLabel = is_string($filter['label'] ?? null) ? $filter['label'] : $filterId;
                $filterType = is_string($filter['type'] ?? null) ? $filter['type'] : 'text';
            @endphp

            <label class="form-control" @if ($filterId !== '') data-daisy-kit-table-filter-field="{{ $filterId }}" @endif>
                <span class="label-text">{{ $filterLabel }}</span>

                @if ($filterType === 'boolean')
                    <select class="select select-bordered select-sm" data-daisy-kit-table-filter="{{ $filterId }}">
                        <option value="">{{ $tableView['labels']['all'] }}</option>
                        <option value="true">{{ $tableView['labels']['yes'] }}</option>
                        <option value="false">{{ $tableView['labels']['no'] }}</option>
                    </select>
                @elseif ($filterType === 'select')
                    <select class="select select-bordered select-sm" data-daisy-kit-table-filter="{{ $filterId }}">
                        <option value="">{{ $tableView['labels']['all'] }}</option>
                        @foreach (($filter['options'] ?? []) as $option)
                            @php
                                $optionValue = is_array($option) ? ($option['value'] ?? '') : $option;
                                $optionLabel = is_array($option) ? ($option['label'] ?? $optionValue) : $option;
                            @endphp
                            <option value="{{ $optionValue }}">{{ $optionLabel }}</option>
                        @endforeach
                    </select>
                @else
                    <input
                        class="input input-bordered input-sm"
                        data-daisy-kit-table-filter="{{ $filterId }}"
                        type="{{ in_array($filterType, ['date', 'number'], true) ? $filterType : 'search' }}"
                    >
                @endif
            </label>
        @endforeach

        @if ($tableView['filterMode'] === 'manual')
            <div class="daisy-kit-table__filter-actions">
                <button class="btn btn-primary btn-sm" data-daisy-kit-table-apply-filters type="button">
                    {{ $tableView['labels']['applyFilters'] }}
                </button>
            </div>
        @endif
    </fieldset>
@endif

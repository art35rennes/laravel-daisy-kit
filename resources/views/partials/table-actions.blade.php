@foreach($actions as $action)
    <button
        type="button"
        class="btn btn-xs {{ \Art35rennes\DaisyKit\Support\DaisyTableActions::Variants[$action['variant']] }}"
        data-table-row-action="{{ $action['action'] }}"
        data-table-row-id="{{ $rowId }}"
        data-table-column-id="{{ $columnId }}"
        @if($action['ariaLabel'] !== '') aria-label="{{ $action['ariaLabel'] }}" @endif
        @disabled($action['disabled'])
        @if($action['disabled']) aria-disabled="true" @endif
    >{{ $action['label'] }}</button>
@endforeach

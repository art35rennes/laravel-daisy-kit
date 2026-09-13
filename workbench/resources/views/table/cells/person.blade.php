<span class="inline-flex items-center gap-3" data-workbench-person-cell>
    <span class="avatar placeholder" aria-hidden="true">
        <span class="bg-neutral text-neutral-content size-8 rounded-full text-xs font-semibold">
            {{ mb_strtoupper(mb_substr($value, 0, 1)) }}
        </span>
    </span>
    <span class="grid leading-tight">
        <strong>{{ $value }}</strong>
        <small class="opacity-65">{{ $row['team'] }}</small>
    </span>
</span>

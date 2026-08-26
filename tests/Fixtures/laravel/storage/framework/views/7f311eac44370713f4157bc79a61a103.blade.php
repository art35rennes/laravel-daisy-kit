    <x-daisy::ui.utilities.indicator
        label="12"
        position="bottom-start"
        color="secondary"
        item-class="sm:indicator-middle"
        data-testid="inbox-indicator"
    >
        <button type="button" class="btn">Inbox</button>
    </x-daisy::ui.utilities.indicator>

    <x-daisy::ui.utilities.indicator type="status" status-color="success">
        <span>Online</span>
    </x-daisy::ui.utilities.indicator>

    <x-daisy::ui.utilities.indicator position="middle-center">
        <x-slot:indicator>
            <button type="button" class="btn btn-primary">Apply</button>
        </x-slot:indicator>

        <article>Job</article>
    </x-daisy::ui.utilities.indicator>
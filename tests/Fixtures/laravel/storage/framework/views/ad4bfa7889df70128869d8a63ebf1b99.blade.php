    <x-daisy::ui.overlay.modal id="delete-user" title="Delete user" close-label="Close delete dialog" initial-focus="[data-confirm-delete]" :teleport="false" open>
        <x-slot:header><h2>Custom header</h2></x-slot:header>
        Body
        <x-slot:footer><button type="button" data-confirm-delete>Confirm</button></x-slot:footer>
    </x-daisy::ui.overlay.modal>
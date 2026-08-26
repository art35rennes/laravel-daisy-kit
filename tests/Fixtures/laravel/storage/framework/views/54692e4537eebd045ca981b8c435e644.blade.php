    <x-daisy::ui.layout.crud-layout actions-alignment="between">
        <x-slot:header><h1>Edit profile</h1></x-slot:header>
        <x-daisy::ui.layout.crud-section title="Profile" sticky-aside actions-alignment="start">
            <x-slot:headerActions><a href="/help">Help</a></x-slot:headerActions>
            <x-slot:aside><p>Aside help</p></x-slot:aside>
            Main form
            <x-slot:actions><button type="button">Save</button></x-slot:actions>
        </x-daisy::ui.layout.crud-section>
        <x-slot:actions><button type="button">Cancel</button><button type="submit">Save all</button></x-slot:actions>
    </x-daisy::ui.layout.crud-layout>
    <x-daisy::ui.data-display.table
        :columns="[['key' => 'name', 'label' => 'Name']]"
        :rows="[['name' => 'Jane']]"
    >
        <x-slot:toolbar><button type="button">Import</button></x-slot:toolbar>
        <x-slot:actions><a href="/users/create">Create</a></x-slot:actions>
    </x-daisy::ui.data-display.table>
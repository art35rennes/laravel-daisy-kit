<!-- Alert -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Alert</h2>
    <div class="space-y-4">
        <!-- Basique -->
        <x-daisy::ui.alert color="info">
            <x-slot:icon>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-info h-6 w-6 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </x-slot:icon>
             New software update available.
        </x-daisy::ui.alert>

        <!-- Couleurs -->
        <x-daisy::ui.alert color="success">Your purchase has been confirmed!</x-daisy::ui.alert>
        <x-daisy::ui.alert color="warning">Warning: Low disk space.</x-daisy::ui.alert>
        <x-daisy::ui.alert color="error">Error: Something went wrong.</x-daisy::ui.alert>

        <!-- Variantes -->
        <x-daisy::ui.alert color="info" variant="soft">Info (soft)</x-daisy::ui.alert>
        <x-daisy::ui.alert color="info" variant="outline">Info (outline)</x-daisy::ui.alert>
        <x-daisy::ui.alert color="info" variant="dash">Info (dash)</x-daisy::ui.alert>

        <!-- Orientation -->
        <x-daisy::ui.alert color="info" :vertical="true">
            <x-slot:icon>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-info h-6 w-6 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </x-slot:icon>
            we use cookies for no reason.
            <x-slot:actions>
                <x-daisy::ui.button size="sm">Deny</x-daisy::ui.button>
                <x-daisy::ui.button size="sm" color="primary">Accept</x-daisy::ui.button>
            </x-slot:actions>
        </x-daisy::ui.alert>
        <x-daisy::ui.alert color="info" horizontalAt="sm">
            <x-slot:icon>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-info h-6 w-6 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </x-slot:icon>
            <div>
                <h3 class="font-bold">New message!</h3>
                <div class="text-xs">You have 1 unread message</div>
            </div>
            <x-slot:actions>
                <x-daisy::ui.button size="sm">See</x-daisy::ui.button>
            </x-slot:actions>
        </x-daisy::ui.alert>
    </div>
</section>



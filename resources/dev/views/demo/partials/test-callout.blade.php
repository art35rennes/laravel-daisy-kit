<!-- Callout -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Callout</h2>
    <div class="space-y-4">
        <!-- Basique -->
        <x-daisy::ui.callout :icon="svg('bi-info-circle')->class('h-5 w-5')" heading="Maintenance programmée">
            Nos serveurs seront indisponibles dimanche de 2h à 5h UTC.
            <x-slot:actions>
                <x-daisy::ui.button size="sm" variant="ghost">En savoir plus</x-daisy::ui.button>
            </x-slot:actions>
        </x-daisy::ui.callout>

        <!-- Icône dans le heading -->
        <x-daisy::ui.callout :icon="svg('bi-newspaper')->class('h-5 w-5')" :iconInHeading="true" heading="Mise à jour des politiques" />

        <!-- Variants -->
        <div class="space-y-2">
            <x-daisy::ui.callout variant="secondary" :icon="svg('bi-info-circle')->class('h-5 w-5')" heading="Compte créé" />
            <x-daisy::ui.callout variant="success" :icon="svg('bi-check-circle')->class('h-5 w-5')" heading="Compte vérifié" />
            <x-daisy::ui.callout variant="warning" :icon="svg('bi-exclamation-triangle')->class('h-5 w-5')" heading="Vérifiez votre compte" />
            <x-daisy::ui.callout variant="danger" :icon="svg('bi-x-circle')->class('h-5 w-5')" heading="Une erreur est survenue" />
        </div>

        <!-- Inline actions -->
        <x-daisy::ui.callout :icon="svg('bi-box')->class('h-5 w-5')" variant="secondary" :inline="true" heading="Votre colis est retardé">
            <x-slot:actions>
                <x-daisy::ui.button size="sm">Suivre →</x-daisy::ui.button>
                <x-daisy::ui.button size="sm" variant="ghost">Reprogrammer</x-daisy::ui.button>
            </x-slot:actions>
        </x-daisy::ui.callout>

        <!-- Dismiss (controls) -->
        <div x-data="{ open: true }" x-show="open">
            <x-daisy::ui.callout :icon="svg('bi-bell')->class('h-5 w-5')" variant="secondary" heading="Réunion à venir">
                10:00
                <x-slot:controls>
                    <x-daisy::ui.button size="sm" variant="ghost" x-on:click.prevent="open=false">✕</x-daisy::ui.button>
                </x-slot:controls>
            </x-daisy::ui.callout>
        </div>
    </div>
</section>



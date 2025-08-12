<!-- Dock (à la demande) -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Dock (à la demande)</h2>
    <div class="flex gap-2">
        <button id="showDockBtn" class="btn btn-primary btn-sm">Afficher le dock</button>
        <button id="hideDockBtn" class="btn btn-ghost btn-sm">Masquer le dock</button>
    </div>
    <x-daisy::ui.dock id="onDemandDock" as="nav" label="Bottom navigation" mobile position="bottom" size="sm" class="hidden z-50 bg-neutral text-neutral-content">
        <button class="dock-item">
            <x-heroicon-o-home class="size-5" />
            <span class="dock-label">Accueil</span>
        </button>
        <button class="dock-item dock-active">
            <x-heroicon-o-inbox class="size-5" />
            <span class="dock-label">Inbox</span>
        </button>
        <button id="closeDockBtn" class="dock-item">
            <x-heroicon-o-x-mark class="size-5" />
            <span class="dock-label">Fermer</span>
        </button>
    </x-daisy::ui.dock>
</section>



<!-- Dock (à la demande) -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Dock (à la demande)</h2>
    <div class="flex gap-2">
        <button id="showDockBtn" class="btn btn-primary btn-sm">Afficher le dock</button>
        <button id="hideDockBtn" class="btn btn-ghost btn-sm">Masquer le dock</button>
    </div>
    <x-daisy::ui.dock id="onDemandDock" as="nav" label="Bottom navigation" mobile position="bottom" size="sm" class="hidden z-50 bg-neutral text-neutral-content">
        <button class="dock-item">
            <x-bi-house class="size-5" />
            <span class="dock-label">Accueil</span>
        </button>
        <button class="dock-item dock-active">
            <x-bi-inbox class="size-5" />
            <span class="dock-label">Inbox</span>
        </button>
        <button id="closeDockBtn" class="dock-item">
            <x-bi-x class="size-5" />
            <span class="dock-label">Fermer</span>
        </button>
    </x-daisy::ui.dock>
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const dock = document.getElementById('onDemandDock');
        const show = document.getElementById('showDockBtn');
        const hide = document.getElementById('hideDockBtn');
        const close = document.getElementById('closeDockBtn');
        if (show) show.addEventListener('click', () => dock?.classList.remove('hidden'));
        if (hide) hide.addEventListener('click', () => dock?.classList.add('hidden'));
        if (close) close.addEventListener('click', () => dock?.classList.add('hidden'));
        dock?.addEventListener('click', (e) => {
          const it = e.target.closest('.dock-item');
          if (!it || !dock.contains(it)) return;
          dock.querySelectorAll('.dock-item').forEach((b) => b.classList.remove('dock-active'));
          it.classList.add('dock-active');
        });
      });
    </script>
</section>



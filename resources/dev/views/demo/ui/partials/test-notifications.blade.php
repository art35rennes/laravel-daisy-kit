<!-- Notifications (déclenchables) -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Notifications (déclenchables)</h2>
    <div class="flex flex-wrap gap-2">
        <button class="btn btn-info btn-sm" onclick="window.triggerToast('info','Information','Nouvelle mise à jour disponible')">Info</button>
        <button class="btn btn-success btn-sm" onclick="window.triggerToast('success','Succès','Sauvegardé avec succès')">Succès</button>
        <button class="btn btn-warning btn-sm" onclick="window.triggerToast('warning','Attention','Connexion instable')">Attention</button>
        <button class="btn btn-error btn-sm" onclick="window.triggerToast('error','Erreur','Échec de l\'opération')">Erreur</button>
    </div>
    <x-daisy::ui.toast id="toastContainer" position="end" vertical="bottom" class="z-50"></x-daisy::ui.toast>

    <script>
    // Définit window.triggerToast localement pour la page de démo
    (function() {
        const container = document.getElementById('toastContainer');
        function triggerToast(color, title, message, timeout = 3000) {
            if (!container) return;
            const toast = document.createElement('div');
            toast.className = `alert alert-${color}`;
            toast.innerHTML = `<div class=\"flex-1\">${title ? `<h3 class=\\\"font-medium\\\">${title}</h3>` : ''}<div class=\"text-sm\">${message}</div></div>`;
            container.appendChild(toast);
            window.setTimeout(() => {
                toast.classList.add('opacity-0','transition','duration-300');
                window.setTimeout(() => toast.remove(), 300);
            }, timeout);
        }
        window.triggerToast = window.triggerToast || triggerToast;
    })();
    </script>
</section>



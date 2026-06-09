/**
 * Daisy Kit - Alert dismiss
 *
 * Ferme une alerte au clic sur [data-alert-dismiss], sans handler inline
 * (compatible CSP script-src 'self' des applications hôtes).
 */
export default function init(element) {
    element.addEventListener('click', (event) => {
        const button = event.target.closest('[data-alert-dismiss]');

        if (! button || ! element.contains(button)) {
            return;
        }

        event.preventDefault();
        element.remove();
    });
}

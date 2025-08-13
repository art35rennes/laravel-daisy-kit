# Prompts Cursor - TODO priorisé (dédupliqué)

## Priorité haute


- Remplace tous les icônes du login button par des heroicons (utilise blade-ui-kit/blade-icons).
- Remplace tous les emotes et caractères spéciaux utilisés comme icônes par des heroicons.


- Pour le composant Tooltip dans la démo, ajuste la couleur du texte pour qu'elle corresponde au texte dans le bouton.
- Pour le composant Drawer de la démo, compacte le rendu et ajoute une bordure.

- Corrige le fonctionnement du composant Dock dans la démo.
- Corrige le décalage de la flèche sur le composant Popover.
- Migre les data-attributs des composants vers des props Laravel Blade lorsque c'est possible.
    - Fais l'inventaire des composants concernés et planifie la migration.
    - Commence par : Drawer, Dock, Sidebar, Tooltip, Popover, Stepper, Treeview, File Input, Tabs, Transfer, Scrollspy.
- Corrige le composant Stepper : répare l'exemple 2 de l'index et le JS associé.

- Ajoute une fonctionnalité de recherche dans le composant Treeview.
- Vérifie le chargement dynamique via REST et explicite le lazy loading dans Treeview.

## Priorité moyenne



- Refactore le composant Mockup Phone pour plus de clarté et de réutilisabilité.

- Remplace le calendrier DaisyUI par le composant Cally natif.
- Pour le code editor, gère la min-height et allège la dépendance.
- Pour Scrollspy, matérialise visuellement le tracking de la section active.

## Priorité basse


- Redéveloppe le composant Transfer manuellement et réduis le nombre d'exemples dans la démo.
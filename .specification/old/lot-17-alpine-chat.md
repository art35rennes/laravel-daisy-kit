# Lot 17 : Migration Chat vers Alpine (complexe)

Le chat est un cas “complexe” où Alpine doit apporter une forte valeur (state orchestration), sans sacrifier les tests et la robustesse.

## Objectifs
- Migrer vers Alpine:
  - `chat-widget`
  - `chat-input`
  - `chat-messages`
- Stabiliser une architecture Alpine:
  - composant parent / store
  - sous-composants “présentation”
- Conserver la compat des Browser/Feature tests existants.

## Périmètre
- Composants Blade:
  - `resources/views/components/ui/communication/chat-widget.blade.php`
  - `resources/views/components/ui/communication/chat-input.blade.php`
  - `resources/views/components/ui/communication/chat-messages.blade.php`
- Modules actuels:
  - `resources/js/modules/chat-widget.js`
  - `resources/js/modules/chat-input.js`
  - `resources/js/modules/chat-messages.js`
- Tests:
  - `tests/Browser/CommunicationComponentsTest.php`

## Comportement attendu
- Ouverture/réduction du widget (minimized state) persistable si souhaité.
- Envoi de message (optimistic UI optionnel).
- Scroll anchoring (rester en bas quand on envoie, ne pas sauter quand on scrolle l’historique).
- Upload optionnel (si activé via props).
- Aucun bruit console.

## API Blade
- Garder props existantes (ou les faire évoluer en phase dev si besoin).
- Conserver `data-*` hooks utilisés par les tests (au moins pendant la migration).

## Architecture
### Alpine
Option recommandée:
- `x-data="chatWidget({...})"` au niveau `chat-widget`.
- État partagé exposé via events custom:
  - `chat:send`
  - `chat:received`
  - `chat:toggle`

### Modules JS
- Garder les modules existants en fallback au début, puis suppression Lot 20.

## Plan de migration
1. Implémenter l’état widget (open/minimized) en Alpine.
2. Migrer le flux send message + form handling.
3. Migrer l’affichage messages + scroll.
4. Retirer la dépendance `data-module` progressivement.

## Tests
- Adapter/renforcer `tests/Browser/CommunicationComponentsTest.php`:
  - ouverture/fermeture widget
  - envoi simple
  - aucune erreur console

## Critères d’acceptation
- [ ] Chat widget utilisable sans modules JS obligatoires.
- [ ] Tests de communication verts.
- [ ] UX correcte (scroll, focus input, submit).



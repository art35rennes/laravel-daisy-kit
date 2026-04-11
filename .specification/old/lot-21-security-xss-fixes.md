# Lot 21 : Corrections de sécurité XSS (Laravel way)

Ce lot corrige les vulnérabilités XSS identifiées dans les composants Blade en appliquant les bonnes pratiques Laravel pour l'échappement des données.

## Contexte
- Audit de sécurité révélant plusieurs utilisations non sécurisées de `{!! !!}` avec des données utilisateur
- Risque d'injection XSS si des données non vérifiées sont passées aux composants
- Nécessité de suivre le "Laravel way" : utiliser `{{ }}` par défaut, `@json()` pour JSON, et documenter les cas exceptionnels

## Objectifs
- **Sécurité** : Éliminer les risques XSS dans tous les composants
- **Cohérence** : Appliquer uniformément les pratiques Laravel d'échappement
- **Documentation** : Clarifier les cas où le HTML est intentionnel (WYSIWYG, etc.)
- **Tests** : Vérifier que l'échappement fonctionne correctement

## Non-objectifs
- Modification de l'API publique des composants (backward compatible)
- Refactorisation de la logique métier
- Migration vers Alpine (hors périmètre)

## Périmètre exact

### Composants Blade à corriger
- `resources/views/components/ui/data-display/table.blade.php` (lignes 83, 85, 98)
- `resources/views/components/ui/navigation/tabs.blade.php` (lignes 64, 66, 83)
- `resources/views/components/ui/overlay/popconfirm.blade.php` (lignes 56, 89)
- `resources/views/components/ui/advanced/accordion.blade.php` (ligne 35)
- `resources/views/components/ui/navigation/breadcrumbs.blade.php` (lignes 32, 38)
- `resources/views/components/ui/data-display/timeline.blade.php` (lignes 42, 51, 63)
- `resources/views/components/ui/navigation/steps.blade.php` (ligne 88)
- `resources/views/components/ui/navigation/sidebar-navigation.blade.php` (ligne 83)
- `resources/views/components/ui/advanced/transfer.blade.php` (lignes 158, 160, 178, 180)
- `resources/views/components/ui/layout/footer-layout.blade.php` (ligne 166)
- `resources/views/components/ui/overlay/tooltip.blade.php` (ligne 37)
- `resources/views/components/ui/feedback/alert.blade.php` (ligne 73 - partiel, déjà utilise `e()` pour text)
- `resources/views/components/ui/data-display/status.blade.php` (ligne 16 - construction manuelle d'attribut)

### JSON dans scripts
- `resources/views/components/ui/advanced/onboarding.blade.php` (ligne 84)
- `resources/views/components/ui/inputs/select.blade.php` (ligne 60)
- `resources/views/components/ui/advanced/code-editor.blade.php` (ligne 54)
- `resources/views/components/ui/media/media-gallery.blade.php` (ligne 74)
- `resources/views/components/ui/media/lightbox.blade.php` (ligne 98)

### Composants avec HTML intentionnel (documentation uniquement)
- `resources/views/components/ui/advanced/wysiwyg.blade.php` (ligne 42 - Trix Editor nécessite HTML)
- `resources/views/components/ui/advanced/stepper.blade.php` (ligne 116 - slot Blade)

## Comportement attendu

### Échappement par défaut
- Tous les contenus utilisateur doivent être échappés avec `{{ }}`
- Les attributs HTML doivent utiliser `e()` ou `{{ }}` selon le contexte
- Les données JSON dans les scripts doivent utiliser `@json()` (échappement automatique)

### Cas exceptionnels documentés
- **WYSIWYG** : Le composant `wysiwyg` accepte du HTML intentionnel (Trix Editor). Documenter que le contenu doit être nettoyé côté serveur avant d'être passé au composant.
- **Slots Blade** : Les slots peuvent contenir du HTML si explicitement documenté. Utiliser `{!! $slot !!}` uniquement si le slot est garanti sûr.

### Rétrocompatibilité
- Les props existantes restent inchangées
- Le comportement visuel reste identique (échappement invisible pour l'utilisateur)
- Les tests existants doivent continuer à passer

## Architecture

### Principe Laravel : échappement par défaut
```blade
{{-- ✅ Correct : échappement automatique --}}
<div>{{ $userContent }}</div>

{{-- ✅ Correct : JSON dans script tag --}}
<script type="application/json">@json($config)</script>

{{-- ❌ Incorrect : HTML non échappé (sauf cas documentés) --}}
<div>{!! $userContent !!}</div>
```

### Cas spéciaux

#### 1. HTML intentionnel (WYSIWYG)
```blade
{{-- Documenter que $value doit être nettoyé côté serveur --}}
<trix-editor>{!! $value ?? $slot !!}</trix-editor>
```

#### 2. Icônes SVG (déjà sécurisées)
```blade
{{-- Les icônes Blade UI Kit sont déjà sécurisées --}}
{!! $iconHtml !!} {{-- OK si provenant de Blade UI Kit --}}
```

#### 3. Attributs HTML dynamiques
```blade
{{-- ✅ Correct : utiliser e() pour les attributs --}}
<div aria-label="{{ e($label) }}"></div>

{{-- ✅ Correct : Laravel échappe automatiquement dans {{ }} --}}
<div aria-label="{{ $label }}"></div>
```

## Plan de migration

### Étape 1 : Correction des composants critiques (XSS direct)
1. **Table** : Remplacer `{!! $cell !!}` par `{{ $cell }}` dans headers, rows, footer
2. **Tabs** : Remplacer `{!! $label !!}` et `{!! $tab['content'] !!}` par `{{ }}`
3. **Popconfirm** : Remplacer `{!! $message !!}` par `{{ $message }}`
4. **Accordion** : Remplacer `{!! $item['content'] !!}` par `{{ $item['content'] }}`

### Étape 2 : Correction des composants de navigation
1. **Breadcrumbs** : Garder `{!! $item['icon'] !!}` (icônes SVG sécurisées), échapper `{{ $item['label'] }}` (déjà fait)
2. **Timeline** : Échapper `startHtml`, `endHtml`, `content` avec `{{ }}` (garder `icon` si SVG)
3. **Steps** : Échapper le contenu de l'icône si texte, garder si SVG
4. **Sidebar navigation** : Vérifier l'échappement dans `$renderItems`

### Étape 3 : Correction JSON dans scripts
1. **Onboarding** : Remplacer `{!! json_encode($config, ...) !!}` par `@json($config)`
2. **Select** : Remplacer `json_encode($default, ...)` par `@json($default)`
3. **Code editor** : Remplacer `{{ json_encode($options) }}` par `@json($options)`
4. **Media gallery / Lightbox** : Remplacer `{{ json_encode($imgs) }}` par `@json($imgs)`

### Étape 4 : Documentation et cas exceptionnels
1. Documenter dans PHPDoc que `wysiwyg` accepte du HTML et nécessite un nettoyage côté serveur
2. Vérifier que les slots Blade utilisant `{!! !!}` sont documentés comme acceptant du HTML
3. Ajouter des commentaires dans le code pour les cas exceptionnels

### Étape 5 : Tests de sécurité
1. Créer des tests Feature vérifiant l'échappement
2. Tester avec des payloads XSS typiques (`<script>`, `onerror=`, etc.)
3. Vérifier que le HTML est bien échappé dans le rendu

## Plan de tests

### Feature tests (rendu)
**Fichier** : `tests/Feature/SecurityXssTest.php` (nouveau)

```php
it('escapes user content in table cells', function () {
    $xssPayload = '<script>alert("XSS")</script>';
    $html = Blade::render('<x-daisy::ui.data-display.table :rows="[['.$xssPayload.']]" />');
    
    expect($html)
        ->not->toContain('<script>')
        ->toContain('&lt;script&gt;');
});

it('escapes user content in tabs labels', function () {
    $xssPayload = '<img src=x onerror=alert(1)>';
    $html = Blade::render('<x-daisy::ui.navigation.tabs :items="[['label' => \''.$xssPayload.'\']]" />');
    
    expect($html)
        ->not->toContain('onerror=')
        ->toContain('&lt;img');
});

it('escapes JSON in script tags using @json', function () {
    $config = ['userInput' => '<script>alert(1)</script>'];
    $html = Blade::render('@json($config)', ['config' => $config]);
    
    // @json échappe automatiquement
    expect($html)
        ->not->toContain('<script>alert(1)</script>')
        ->toContain('\\u003Cscript\\u003E'); // Échappement JSON
});
```

### Tests existants à vérifier
- `tests/Feature/ComponentRenderingTest.php` : Vérifier que les rendus restent cohérents
- Tous les Browser tests : Vérifier qu'il n'y a pas de régression visuelle

## Critères d'acceptation

### Sécurité
- [ ] Tous les contenus utilisateur sont échappés avec `{{ }}` ou `e()`
- [ ] Tous les JSON dans scripts utilisent `@json()` au lieu de `json_encode()`
- [ ] Les tests XSS passent (payloads malveillants échappés)
- [ ] Aucune régression de sécurité identifiée

### Qualité du code
- [ ] Code formaté avec Pint
- [ ] PHPDoc mis à jour pour les cas exceptionnels (WYSIWYG)
- [ ] Commentaires ajoutés pour les cas où `{!! !!}` est intentionnel

### Tests
- [ ] Tests Feature de sécurité créés et verts
- [ ] Tests existants (Feature + Browser) restent verts
- [ ] Aucune régression visuelle dans les démos/docs

### Documentation
- [ ] Cas exceptionnels documentés (WYSIWYG, slots HTML)
- [ ] Guide de sécurité ajouté si nécessaire

## Risques & mitigations

### Risque 1 : Casser le rendu HTML intentionnel
**Mitigation** : Identifier et documenter tous les cas où le HTML est intentionnel (WYSIWYG, icônes SVG). Utiliser des tests pour vérifier que le rendu reste correct.

### Risque 2 : Régressions visuelles
**Mitigation** : Vérifier tous les Browser tests et les pages de démo après chaque correction. Utiliser des assertions stables (contenu textuel plutôt que structure HTML exacte).

### Risque 3 : Performance (échappement multiple)
**Mitigation** : Laravel gère efficacement l'échappement. `@json()` est optimisé pour les scripts. Pas de double échappement attendu avec `{{ }}`.

### Risque 4 : Compatibilité avec données existantes
**Mitigation** : Les props restent identiques. Seul le rendu change (échappement). Les données déjà échappées côté serveur ne seront pas double-échappées (Laravel détecte les `HtmlString`).

## Notes techniques

### Laravel échappement automatique
- `{{ }}` échappe automatiquement avec `htmlspecialchars()`
- `@json()` échappe pour JSON (échappement Unicode, guillemets, etc.)
- `{!! !!}` ne doit être utilisé que pour du HTML de confiance (icônes Blade UI Kit, HTML nettoyé côté serveur)

### Détection des HtmlString
Laravel ne double-échappe pas les instances de `Illuminate\Support\HtmlString`. Si un composant reçoit déjà du HTML échappé, il ne sera pas ré-échappé.

### Pattern recommandé
```php
// ✅ Bon : échapper côté Blade
{{ $userContent }}

// ✅ Bon : HTML de confiance (documenté)
{!! $trustedHtml !!}

// ✅ Bon : JSON dans script
<script type="application/json">@json($data)</script>

// ❌ Mauvais : données utilisateur non échappées
{!! $userContent !!}
```


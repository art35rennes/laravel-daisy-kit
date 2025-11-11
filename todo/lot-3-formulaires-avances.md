# Lot 3 : Templates de formulaires avancés

## Vue d'ensemble
Créer trois templates de formulaires avancés utilisant les composants UI existants (stepper, tabs) pour offrir des expériences utilisateur améliorées.

## Templates à créer

### 1. form-wizard.blade.php
**Fichier** : `resources/views/templates/form-wizard.blade.php`

**Description** : Formulaire multi-étapes utilisant le composant `stepper` pour guider l'utilisateur à travers un processus complexe.

**Props** :
```php
@props([
    'title' => __('form.wizard'),
    'theme' => null,
    // Form
    'action' => '#',
    'method' => 'POST',
    // Stepper configuration
    'steps' => [], // ['label' => 'Étape 1', 'icon' => 'user', 'disabled' => false]
    'currentStep' => 1,
    'linear' => true, // Empêche de passer à l'étape suivante sans valider
    'allowClickNav' => false, // Empêche la navigation par clic sur les steps
    'showControls' => true,
    'prevText' => __('form.previous'),
    'nextText' => __('form.next'),
    'finishText' => __('form.finish'),
    // Validation
    'validateOnStep' => true, // Valider chaque étape avant de continuer
    'validateOnSubmit' => true, // Valider tout le formulaire à la soumission
])
```

**Fonctionnalités Laravel** :
- Utilise les sessions Laravel pour persister les données entre les étapes (`session()->put()`, `session()->get()`)
- Utilise `old()` pour pré-remplir les champs après validation
- Gère les erreurs de validation via `$errors`
- Utilise `@csrf` pour la protection CSRF
- Peut utiliser `FormRequest` pour la validation finale
- Utilise `session()->flash()` pour les messages de succès

**Composants UI utilisés** :
- `x-daisy::layout.app` ou layout approprié
- `x-daisy::ui.navigation.stepper` (composant principal pour la navigation)
- `x-daisy::ui.partials.form-field` (champs de formulaire)
- `x-daisy::ui.inputs.*` (tous les types d'inputs)
- `x-daisy::ui.inputs.button` (boutons de navigation)
- `x-daisy::ui.feedback.alert` (messages d'erreur/succès)
- `x-daisy::ui.feedback.loading` (indicateur de chargement)

**Structure** :
- En-tête avec titre
- Composant `stepper` avec les étapes
- Contenu de chaque étape dans des slots nommés (`step_1`, `step_2`, etc.)
- Contrôles de navigation (Précédent, Suivant, Terminer)
- Messages de validation par étape

**Exemple d'utilisation** :

```blade
<x-daisy::templates.form-wizard
    title="Inscription"
    :steps="[
        ['label' => 'Informations', 'icon' => 'user'],
        ['label' => 'Contact', 'icon' => 'envelope'],
        ['label' => 'Confirmation', 'icon' => 'check'],
    ]"
    action="{{ route('register') }}"
    :currentStep="session('wizard_step', 1)"
>
    <x-slot:step_1>
        <x-daisy::ui.partials.form-field name="name" label="Nom" required>
            <x-daisy::ui.inputs.input name="name" :value="old('name', session('wizard_data.name'))" />
        </x-daisy::ui.partials.form-field>
    </x-slot:step_1>

    <x-slot:step_2>
        <x-daisy::ui.partials.form-field name="email" label="Email" required>
            <x-daisy::ui.inputs.input name="email" type="email" :value="old('email', session('wizard_data.email'))" />
        </x-daisy::ui.partials.form-field>
    </x-slot:step_2>

    <x-slot:step_3>
        <p>Confirmez vos informations...</p>
    </x-slot:step_3>
</x-daisy::templates.form-wizard>
```

**Logique backend recommandée** :
```php
// Dans le contrôleur
public function storeWizard(Request $request) {
    $step = $request->input('step', 1);
    
    // Sauvegarder les données dans la session
    $wizardData = session('wizard_data', []);
    $wizardData = array_merge($wizardData, $request->except(['_token', 'step']));
    session(['wizard_data' => $wizardData, 'wizard_step' => $step]);
    
    // Valider l'étape actuelle
    $validator = $this->validateStep($step, $request);
    if ($validator->fails()) {
        return back()->withErrors($validator)->withInput();
    }
    
    // Si dernière étape, traiter les données
    if ($step === count($this->steps)) {
        // Créer l'enregistrement
        $user = User::create($wizardData);
        session()->forget(['wizard_data', 'wizard_step']);
        return redirect()->route('dashboard')->with('success', 'Inscription réussie !');
    }
    
    // Passer à l'étape suivante
    return redirect()->back()->with('step', $step + 1);
}
```

**Traductions nécessaires** (à créer `resources/lang/fr/form.php`) :
- `wizard" : "Assistant"
- `previous" : "Précédent"
- `next" : "Suivant"
- `finish" : "Terminer"
- `step" : "Étape"
- `of" : "sur"
- `complete_step_first" : "Veuillez compléter cette étape avant de continuer"

---

### 2. form-inline.blade.php
**Fichier** : `resources/views/templates/form-inline.blade.php`

**Description** : Formulaire compact inline, idéal pour les recherches, filtres rapides, ou formulaires dans des tableaux.

**Props** :
```php
@props([
    'action' => '#',
    'method' => 'GET', // GET par défaut pour les recherches/filtres
    'inline' => true, // Layout inline (champs sur une ligne)
    'compact' => true, // Taille compacte des inputs
    'showLabels' => false, // Masquer les labels (placeholders uniquement)
    'submitText' => __('form.search'),
    'resetText' => __('form.reset'),
    'showReset' => true,
    'size' => 'sm', // xs, sm, md, lg
])
```

**Fonctionnalités Laravel** :
- Utilise `method="GET"` par défaut pour les recherches (pas de CSRF nécessaire)
- Utilise `old()` pour pré-remplir les valeurs
- Utilise `request()->query()` pour récupérer les paramètres GET
- Gère les erreurs de validation si `method="POST"`

**Composants UI utilisés** :
- `x-daisy::ui.partials.form-field` (avec `showLabels` false)
- `x-daisy::ui.inputs.input` (taille compacte)
- `x-daisy::ui.inputs.select` (taille compacte)
- `x-daisy::ui.inputs.button` (taille compacte)
- `x-daisy::ui.advanced.join` (pour grouper les champs et boutons)

**Structure** :
- Layout flex horizontal
- Champs alignés sur une ligne
- Boutons de soumission et reset à droite
- Responsive : passe en vertical sur mobile

**Exemple d'utilisation** :

```blade
<x-daisy::templates.form-inline
    action="{{ route('users.search') }}"
    method="GET"
    submitText="Rechercher"
>
    <x-daisy::ui.partials.form-field name="q" :showLabels="false" class="flex-1">
        <x-daisy::ui.inputs.input
            name="q"
            placeholder="Rechercher un utilisateur..."
            :value="request()->query('q')"
            size="sm"
        />
    </x-daisy::ui.partials.form-field>

    <x-daisy::ui.partials.form-field name="role" :showLabels="false">
        <x-daisy::ui.inputs.select name="role" size="sm">
            <option value="">Tous les rôles</option>
            <option value="admin" {{ request()->query('role') === 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="user" {{ request()->query('role') === 'user' ? 'selected' : '' }}>Utilisateur</option>
        </x-daisy::ui.inputs.select>
    </x-daisy::ui.partials.form-field>

    <x-slot:actions>
        <x-daisy::ui.inputs.button type="submit" size="sm">Rechercher</x-daisy::ui.inputs.button>
        <x-daisy::ui.inputs.button type="reset" variant="ghost" size="sm">Réinitialiser</x-daisy::ui.inputs.button>
    </x-slot:actions>
</x-daisy::templates.form-inline>
```

**Traductions nécessaires** :
- `search" : "Rechercher"
- `reset" : "Réinitialiser"
- `filter" : "Filtrer"

---

### 3. form-with-tabs.blade.php
**Fichier** : `resources/views/templates/form-with-tabs.blade.php`

**Description** : Formulaire organisé en onglets pour regrouper logiquement les champs (ex: informations générales, adresse, préférences).

**Props** :
```php
@props([
    'title' => __('form.form'),
    'theme' => null,
    // Form
    'action' => '#',
    'method' => 'POST',
    // Tabs configuration
    'tabs' => [], // ['id' => 'general', 'label' => 'Général', 'icon' => 'info']
    'activeTab' => null, // Auto-detect from old('_active_tab') or first tab
    'tabsStyle' => 'box', // box, border, lift
    'tabsPlacement' => 'top', // top, bottom
    // Validation
    'validateAllTabs' => false, // Afficher les erreurs de tous les onglets
    'highlightErrors' => true, // Mettre en évidence les onglets avec erreurs
])
```

**Fonctionnalités Laravel** :
- Utilise `old('_active_tab')` pour restaurer l'onglet actif après validation
- Utilise `old()` pour pré-remplir tous les champs
- Gère les erreurs de validation et les affiche dans les onglets correspondants
- Utilise `@csrf` pour la protection CSRF
- Utilise `@method()` pour PUT/PATCH si nécessaire

**Composants UI utilisés** :
- `x-daisy::layout.app` ou layout approprié
- `x-daisy::ui.navigation.tabs` (composant principal)
- `x-daisy::ui.partials.form-field` (champs de formulaire)
- `x-daisy::ui.inputs.*` (tous les types d'inputs)
- `x-daisy::ui.inputs.button` (boutons de soumission)
- `x-daisy::ui.data-display.badge` (badge d'erreur sur les onglets)
- `x-daisy::ui.feedback.alert` (messages)

**Structure** :
- En-tête avec titre
- Composant `tabs` avec les onglets
- Contenu de chaque onglet dans des slots nommés (`tab_general`, `tab_address`, etc.)
- Champ caché `_active_tab` pour persister l'onglet actif
- Boutons de soumission en bas
- Indicateurs d'erreur sur les onglets (badges)

**Exemple d'utilisation** :

```blade
<x-daisy::templates.form-with-tabs
    title="Modifier le profil"
    action="{{ route('profile.update') }}"
    method="POST"
    :tabs="[
        ['id' => 'general', 'label' => 'Général', 'icon' => 'user'],
        ['id' => 'address', 'label' => 'Adresse', 'icon' => 'map'],
        ['id' => 'preferences', 'label' => 'Préférences', 'icon' => 'gear'],
    ]"
    :activeTab="old('_active_tab', 'general')"
    tabsStyle="box"
>
    <x-slot:tab_general>
        <x-daisy::ui.partials.form-field name="name" label="Nom" required>
            <x-daisy::ui.inputs.input name="name" :value="old('name', $user->name)" />
        </x-daisy::ui.partials.form-field>
        <x-daisy::ui.partials.form-field name="email" label="Email" required>
            <x-daisy::ui.inputs.input name="email" type="email" :value="old('email', $user->email)" />
        </x-daisy::ui.partials.form-field>
    </x-slot:tab_general>

    <x-slot:tab_address>
        <x-daisy::ui.partials.form-field name="street" label="Rue">
            <x-daisy::ui.inputs.input name="street" :value="old('street', $user->address->street ?? '')" />
        </x-daisy::ui.partials.form-field>
        <x-daisy::ui.partials.form-field name="city" label="Ville">
            <x-daisy::ui.inputs.input name="city" :value="old('city', $user->address->city ?? '')" />
        </x-daisy::ui.partials.form-field>
    </x-slot:tab_address>

    <x-slot:tab_preferences>
        <x-daisy::ui.partials.form-field name="language" label="Langue">
            <x-daisy::ui.inputs.select name="language">
                <option value="fr" {{ old('language', $user->language) === 'fr' ? 'selected' : '' }}>Français</option>
                <option value="en" {{ old('language', $user->language) === 'en' ? 'selected' : '' }}>English</option>
            </x-daisy::ui.inputs.select>
        </x-daisy::ui.partials.form-field>
    </x-slot:tab_preferences>

    <x-slot:actions>
        <input type="hidden" name="_active_tab" value="{{ old('_active_tab', 'general') }}" id="active-tab-input">
        <x-daisy::ui.inputs.button type="submit">Enregistrer</x-daisy::ui.inputs.button>
        <x-daisy::ui.inputs.button type="button" variant="ghost" onclick="window.history.back()">Annuler</x-daisy::ui.inputs.button>
    </x-slot:actions>
</x-daisy::templates.form-with-tabs>
```

**Logique JavaScript nécessaire** :
- Sauvegarder l'onglet actif dans le champ caché `_active_tab` lors du changement d'onglet
- Mettre en évidence les onglets avec erreurs (badge avec nombre d'erreurs)

**Traductions nécessaires** :
- `form" : "Formulaire"
- `save" : "Enregistrer"
- `cancel" : "Annuler"
- `errors_in_tab" : ":count erreur(s) dans cet onglet"

---

## Composants/Wrappers nécessaires

### Aucun nouveau composant requis
Tous les templates utilisent exclusivement les composants UI existants :
- `stepper` existe déjà pour le wizard
- `tabs` existe déjà pour le formulaire avec onglets
- Tous les autres composants (inputs, form-field, etc.) existent

**Note** : Le composant `stepper` gère déjà la navigation et la persistance via JavaScript. Pour le wizard, il faudra synchroniser avec les sessions Laravel côté backend.

---

## Tests à prévoir

Pour chaque template :
1. **Test de rendu** : Vérifier le rendu avec les props par défaut
2. **Test de navigation** (wizard/tabs) : Vérifier le changement d'étape/onglet
3. **Test de validation** : Vérifier l'affichage des erreurs
4. **Test de persistance** (wizard) : Vérifier la sauvegarde dans la session
5. **Test responsive** : Vérifier l'affichage sur mobile (form-inline passe en vertical)
6. **Test de soumission** : Vérifier la soumission du formulaire
7. **Test d'erreurs par onglet** (form-with-tabs) : Vérifier les badges d'erreur sur les onglets

---

## Ordre d'implémentation recommandé

1. `form-inline.blade.php` (le plus simple)
2. `form-with-tabs.blade.php` (modéré)
3. `form-wizard.blade.php` (le plus complexe avec gestion de session)

---

## Notes importantes

- **form-wizard** : Nécessite une synchronisation entre le JavaScript du stepper et les sessions Laravel. Le stepper gère la navigation côté client, mais les données doivent être sauvegardées côté serveur à chaque étape.
- **form-inline** : Par défaut en `method="GET"` pour les recherches/filtres (pas de CSRF nécessaire). Peut être changé en `POST` si nécessaire.
- **form-with-tabs** : Utilise un champ caché `_active_tab` pour restaurer l'onglet actif après validation. Le JavaScript doit mettre à jour ce champ lors du changement d'onglet.
- Tous les templates doivent respecter les conventions Laravel : `old()`, `$errors`, `@csrf`, `@method()`
- Les traductions doivent être ajoutées dans `resources/lang/fr/form.php` et `resources/lang/en/form.php`


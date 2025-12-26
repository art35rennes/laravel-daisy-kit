<?php

namespace App\Console\Commands;

use App\Helpers\ComponentScanner;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class GenerateDocsPages extends Command
{
    protected $signature = 'docs:generate-pages {--force : Overwrite existing pages}';

    protected $description = 'Génère une structure de base minimale pour les pages de documentation (à compléter manuellement)';

    public function handle(): int
    {
        try {
            $components = ComponentScanner::readCached()['components'] ?? [];
        } catch (\Throwable) {
            Artisan::call('inventory:components');
            $components = ComponentScanner::readCached()['components'] ?? [];
        }

        if (empty($components)) {
            $this->error('Aucun composant trouvé dans le manifeste.');

            return Command::FAILURE;
        }

        $basePath = resource_path('dev/views/docs/components');
        $force = $this->option('force');
        $created = 0;
        $skipped = 0;

        foreach ($components as $component) {
            $category = $component['category'] ?? 'misc';
            $name = $component['name'] ?? '';

            if (empty($name)) {
                continue;
            }

            $categoryPath = $basePath.'/'.$category;
            $filePath = $categoryPath.'/'.$name.'.blade.php';

            if (! $force && File::exists($filePath)) {
                $skipped++;

                continue;
            }

            File::ensureDirectoryExists($categoryPath);

            $content = $this->generatePageContent($category, $name, $component);

            File::put($filePath, $content);
            $created++;
        }

        $this->info("✓ {$created} pages créées");
        if ($skipped > 0) {
            $this->info("  {$skipped} pages déjà existantes (utilisez --force pour les écraser)");
        }

        return Command::SUCCESS;
    }

    private function generatePageContent(string $category, string $name, array $component): string
    {
        $title = $this->labelize($name);
        $props = $component['props'] ?? [];
        $jsModule = $component['jsModule'] ?? null;
        $description = $this->getComponentDescription($name, $category);

        $sections = [
            ['id' => 'intro', 'label' => 'Introduction'],
            ['id' => 'base', 'label' => 'Exemple de base'],
        ];

        // Ajouter section variantes si le composant a des props de style/couleur/taille
        $hasVariants = ! empty(array_intersect($props, ['variant', 'color', 'size', 'style']));
        if ($hasVariants) {
            $sections[] = ['id' => 'variants', 'label' => 'Variantes'];
        }

        // Ajouter section API si des props existent
        if (! empty($props)) {
            $sections[] = ['id' => 'api', 'label' => 'API'];
        }

        // Convertir sections en PHP array
        $sectionsPhp = "[\n";
        foreach ($sections as $section) {
            $sectionsPhp .= "            ['id' => '{$section['id']}', 'label' => '{$section['label']}'],\n";
        }
        $sectionsPhp .= '        ]';

        $propsPhp = '[]';
        if (! empty($props)) {
            $propsPhp = "DocsHelper::getComponentProps('{$category}', '{$name}')";
        }

        $jsModuleAttr = '';
        if ($jsModule) {
            $jsModuleAttr = "\n            jsModule=\"{$jsModule}\"";
        }

        return <<<BLADE
@php
    use App\Helpers\DocsHelper;
    \$prefix = config('daisy-kit.docs.prefix', 'docs');
    \$sections = {$sectionsPhp};
    \$props = {$propsPhp};
@endphp

<x-daisy::docs.page 
    title="{$title}" 
    category="{$category}" 
    name="{$name}"
    type="component"
    :sections="\$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="{$title}" 
            subtitle="{$description}"{$jsModuleAttr}
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="{$name}" code="<!-- À compléter -->">
        <x-slot:preview>
            <!-- Ajouter l'exemple de base ici -->
        </x-slot:preview>
    </x-daisy::docs.sections.example>

    @if(!empty(\$props))
        <x-daisy::docs.sections.api :props="\$props" />
    @endif
</x-daisy::docs.page>

BLADE;
    }

    private function getComponentDescription(string $name, string $category): string
    {
        $descriptions = [
            'button' => 'Un composant d\'action compatible daisyUI. Utilisez les props pour contrôler le style, la taille et l\'état.',
            'input' => 'Champ de saisie de texte compatible daisyUI. Supporte différents types et styles.',
            'textarea' => 'Zone de texte multiligne compatible daisyUI.',
            'select' => 'Liste déroulante compatible daisyUI.',
            'checkbox' => 'Case à cocher compatible daisyUI.',
            'radio' => 'Bouton radio compatible daisyUI.',
            'range' => 'Curseur de sélection de valeur compatible daisyUI.',
            'toggle' => 'Interrupteur compatible daisyUI.',
            'file-input' => 'Champ de téléchargement de fichier compatible daisyUI.',
            'color-picker' => 'Sélecteur de couleur avec support JavaScript.',
            'breadcrumbs' => 'Fil d\'Ariane pour la navigation hiérarchique.',
            'menu' => 'Menu de navigation vertical ou horizontal.',
            'pagination' => 'Pagination pour naviguer entre les pages.',
            'navbar' => 'Barre de navigation en haut de page.',
            'sidebar' => 'Barre latérale de navigation.',
            'tabs' => 'Onglets pour organiser le contenu.',
            'steps' => 'Indicateur d\'étapes dans un processus.',
            'stepper' => 'Assistant pas à pas avec navigation.',
            'card' => 'Carte pour afficher du contenu groupé.',
            'hero' => 'Section hero pour les pages d\'accueil.',
            'footer' => 'Pied de page du site.',
            'divider' => 'Séparateur visuel horizontal ou vertical.',
            'list' => 'Liste d\'éléments avec styles daisyUI.',
            'stack' => 'Empilement d\'éléments superposés.',
            'badge' => 'Badge pour afficher des informations.',
            'avatar' => 'Avatar pour afficher une image de profil.',
            'kbd' => 'Affichage de raccourcis clavier.',
            'table' => 'Tableau de données avec fonctionnalités avancées.',
            'stat' => 'Statistique avec titre, valeur et description.',
            'progress' => 'Barre de progression linéaire.',
            'radial-progress' => 'Barre de progression circulaire.',
            'status' => 'Indicateur de statut visuel.',
            'timeline' => 'Chronologie d\'événements.',
            'modal' => 'Fenêtre modale pour afficher du contenu.',
            'drawer' => 'Tiroir latéral pour la navigation.',
            'dropdown' => 'Menu déroulant.',
            'popover' => 'Popover pour afficher des informations contextuelles.',
            'popconfirm' => 'Confirmation via popover.',
            'tooltip' => 'Info-bulle au survol.',
            'carousel' => 'Carrousel d\'images ou de contenu.',
            'lightbox' => 'Galerie d\'images avec lightbox.',
            'media-gallery' => 'Galerie multimédia interactive.',
            'embed' => 'Intégration de contenu externe (vidéo, carte, etc.).',
            'leaflet' => 'Carte interactive avec Leaflet.js.',
            'alert' => 'Alerte pour informer l\'utilisateur.',
            'toast' => 'Notification toast.',
            'loading' => 'Indicateur de chargement.',
            'skeleton' => 'Placeholder de chargement.',
            'callout' => 'Encadré d\'information.',
            'accordion' => 'Accordéon pour afficher/masquer du contenu.',
            'calendar' => 'Calendrier pour sélectionner des dates.',
            'calendar-full' => 'Calendrier complet avec gestion d\'événements.',
            'calendar-cally' => 'Calendrier utilisant le composant web Cally.',
            'calendar-native' => 'Calendrier natif du navigateur.',
            'chart' => 'Graphiques avec Chart.js.',
            'chat-bubble' => 'Bulle de conversation.',
            'code-editor' => 'Éditeur de code avec coloration syntaxique.',
            'collapse' => 'Contenu repliable.',
            'countdown' => 'Compte à rebours animé.',
            'diff' => 'Comparaison côte à côte de deux éléments.',
            'fieldset' => 'Groupe de champs de formulaire.',
            'filter' => 'Filtre avec boutons radio.',
            'icon' => 'Icône depuis blade-icons.',
            'join' => 'Groupe d\'éléments joints.',
            'label' => 'Étiquette pour les champs de formulaire.',
            'link' => 'Lien stylisé.',
            'login-button' => 'Bouton de connexion OAuth.',
            'mask' => 'Masque pour les images.',
            'onboarding' => 'Assistant de démarrage.',
            'rating' => 'Système de notation.',
            'scroll-status' => 'Indicateur de progression du défilement.',
            'scrollspy' => 'Navigation automatique basée sur le défilement.',
            'swap' => 'Échange entre deux éléments.',
            'theme-controller' => 'Contrôleur de thème daisyUI.',
            'transfer' => 'Transfert d\'éléments entre deux listes.',
            'tree-view' => 'Vue arborescente hiérarchique.',
            'validator' => 'Validation de formulaire visuelle.',
            'wysiwyg' => 'Éditeur WYSIWYG riche.',
        ];

        return $descriptions[$name] ?? 'Composant compatible daisyUI v5 et Tailwind CSS v4.';
    }

    private function labelize(string $slug): string
    {
        // Essayer d'abord la traduction
        $translationKey = "daisy::components.{$slug}";
        if (__($translationKey) !== $translationKey) {
            return __($translationKey);
        }

        // Sinon, formater le slug
        $slug = str_replace(['-', '_'], ' ', $slug);
        $slug = preg_replace('/\s+/', ' ', $slug ?? '') ?? '';

        return mb_convert_case(trim($slug), MB_CASE_TITLE, 'UTF-8');
    }
}

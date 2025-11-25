<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateDocsPages extends Command
{
    protected $signature = 'docs:generate-pages {--force : Overwrite existing pages}';

    protected $description = 'Génère toutes les pages de documentation à partir du manifeste components.json';

    public function handle(): int
    {
        $manifestPath = resource_path('dev/data/components.json');
        if (! File::exists($manifestPath)) {
            $this->error('Le fichier components.json n\'existe pas. Exécutez d\'abord inventory:components.');

            return Command::FAILURE;
        }

        $json = File::get($manifestPath);
        $data = json_decode($json, true);
        $components = $data['components'] ?? [];

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
        $viewPath = $component['view'] ?? "daisy::components.ui.{$category}.{$name}";
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

        $componentTag = str_replace('daisy::components.ui.', 'daisy::ui.', $viewPath);

        $baseExample = $this->generateBaseExample($componentTag, $name, $category);
        $baseExampleCode = $this->cleanCodeIndentation($baseExample);
        $variantsExample = $hasVariants ? $this->generateVariantsExample($componentTag, $name, $category, $props) : '';

        $jsNote = $jsModule ? "\n        <div class=\"alert alert-info mt-4\">\n            <span>Ce composant nécessite le module JavaScript <code>{$jsModule}</code>.</span>\n        </div>" : '';

        return <<<BLADE
@php
    use App\Helpers\DocsHelper;
    \$prefix = config('daisy-kit.docs.prefix', 'docs');
    \$navItems = DocsHelper::getNavigationItems(\$prefix);
    \$sections = {$sectionsPhp};
    \$props = DocsHelper::getComponentProps('{$category}', '{$name}');
@endphp

<x-daisy::layout.docs title="{$title}" :sidebarItems="\$navItems" :sections="\$sections" :currentRoute="request()->path()">
    <x-slot:navbar>
        <div class="join">
            <a href="/{{\$prefix}}" class="btn btn-sm join-item btn-ghost">Docs</a>
            <a href="{{ route('demo') }}" class="btn btn-sm join-item btn-ghost">Démo</a>
            <a href="/{{\$prefix}}/templates" class="btn btn-sm join-item btn-ghost">Template</a>
        </div>
    </x-slot:navbar>

    <section id="intro">
        <h1>{$title}</h1>
        <p>{$description}</p>{$jsNote}
    </section>

    <section id="base" class="mt-10">
        <h2>Exemple de base</h2>
        <div class="tabs tabs-box">
            <input type="radio" name="base-example-{$name}" class="tab" aria-label="Preview" checked />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                <div class="not-prose">
                    {$baseExample}
                </div>
            </div>
            <input type="radio" name="base-example-{$name}" class="tab" aria-label="Code" />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                @php
                    \$baseCode = {$this->escapeBladeForPhp($baseExampleCode)};
                @endphp
                <x-daisy::ui.advanced.code-editor 
                    language="blade" 
                    :value="\$baseCode"
                    :readonly="true"
                    :showToolbar="false"
                    :showFoldAll="false"
                    :showUnfoldAll="false"
                    :showFormat="false"
                    :showCopy="true"
                    height="200px"
                />
            </div>
        </div>
    </section>
{$variantsExample}
    @if(!empty(\$props))
    <section id="api" class="mt-10">
        <h2>API</h2>
        <div class="overflow-x-auto">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Prop</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(\$props as \$prop)
                        <tr>
                            <td><code>{{ \$prop }}</code></td>
                            <td class="opacity-70">Voir les commentaires dans le composant Blade pour les valeurs et défauts.</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    @endif
</x-daisy::layout.docs>

BLADE;
    }

    private function generateBaseExample(string $componentTag, string $name, string $category): string
    {
        // Exemples de base selon le type de composant avec données réalistes
        return match ($category) {
            'inputs' => match ($name) {
                'button' => '<x-'.$componentTag.'>Envoyer</x-'.$componentTag.'>',
                'input' => '<x-'.$componentTag.' type="text" name="email" placeholder="votre@email.com" />',
                'textarea' => '<x-'.$componentTag.' name="message" placeholder="Votre message..." rows="4"></x-'.$componentTag.'>',
                'select' => '<x-'.$componentTag.' name="country">'."\n".'    <option value="">Choisir un pays</option>'."\n".'    <option value="fr">France</option>'."\n".'    <option value="be">Belgique</option>'."\n".'    <option value="ch">Suisse</option>'."\n".'</x-'.$componentTag.'>',
                'checkbox' => '<x-'.$componentTag.' name="terms" label="J\'accepte les conditions" />',
                'radio' => '<x-'.$componentTag.' name="gender" value="male" label="Homme" />'."\n".'<x-'.$componentTag.' name="gender" value="female" label="Femme" />',
                'range' => '<x-'.$componentTag.' name="volume" min="0" max="100" value="50" />',
                'toggle' => '<x-'.$componentTag.' name="notifications" label="Activer les notifications" />',
                'file-input' => '<x-'.$componentTag.' name="avatar" accept="image/*" />',
                'color-picker' => '<x-'.$componentTag.' name="theme-color" value="#3b82f6" />',
                'sign' => '<x-'.$componentTag.' width="400" height="200" showActions="true" />',
                default => '<x-'.$componentTag.' />',
            },
            'navigation' => match ($name) {
                'breadcrumbs' => '@php'."\n".'$items = ['."\n".'    ["label" => "Accueil", "href" => "/"],'."\n".'    ["label" => "Produits", "href" => "/products"],'."\n".'    ["label" => "Détails"]'."\n".'];'."\n".'@endphp'."\n".'<x-'.$componentTag.' :items="$items" />',
                'menu' => '<x-'.$componentTag.'>'."\n".'    <li><a href="/dashboard">Tableau de bord</a></li>'."\n".'    <li><a href="/users">Utilisateurs</a></li>'."\n".'    <li><a href="/settings">Paramètres</a></li>'."\n".'</x-'.$componentTag.'>',
                'pagination' => '<x-'.$componentTag.' :total="42" :current="3" />',
                'navbar' => '<x-'.$componentTag.'>'."\n".'    <x-slot:start>'."\n".'        <a href="/" class="text-xl font-bold">Mon Site</a>'."\n".'    </x-slot:start>'."\n".'    <x-slot:end>'."\n".'        <x-daisy::ui.inputs.button>Connexion</x-daisy::ui.inputs.button>'."\n".'    </x-slot:end>'."\n".'</x-'.$componentTag.'>',
                'sidebar' => '@php'."\n".'$items = ['."\n".'    ["label" => "Dashboard", "href" => "/", "icon" => "house"],'."\n".'    ["label" => "Utilisateurs", "href" => "/users", "icon" => "people"],'."\n".'    ["label" => "Paramètres", "href" => "/settings", "icon" => "gear"]'."\n".'];'."\n".'@endphp'."\n".'<x-'.$componentTag.' :items="$items" />',
                'tabs' => '@php'."\n".'$items = ['."\n".'    ["label" => "Profil", "active" => true],'."\n".'    ["label" => "Sécurité"],'."\n".'    ["label" => "Notifications"]'."\n".'];'."\n".'@endphp'."\n".'<x-'.$componentTag.' :items="$items" />',
                'steps' => '@php'."\n".'$items = ['."\n".'    ["label" => "Informations"],'."\n".'    ["label" => "Paiement", "current" => true],'."\n".'    ["label" => "Confirmation"]'."\n".'];'."\n".'@endphp'."\n".'<x-'.$componentTag.' :items="$items" />',
                'stepper' => '@php'."\n".'$items = ['."\n".'    ["title" => "Étape 1", "content" => "Contenu de l\'étape 1"],'."\n".'    ["title" => "Étape 2", "content" => "Contenu de l\'étape 2"]'."\n".'];'."\n".'@endphp'."\n".'<x-'.$componentTag.' :items="$items" />',
                'sidebar-navigation' => '@php'."\n".'$items = ['."\n".'    ["label" => "Introduction", "href" => "/docs"],'."\n".'    ["label" => "Composants", "children" => ['."\n".'        ["label" => "Boutons", "href" => "/docs/inputs/button"],'."\n".'        ["label" => "Formulaires", "href" => "/docs/inputs/input"]'."\n".'    ]],'."\n".'    ["label" => "Templates", "href" => "/docs/templates"]'."\n".'];'."\n".'@endphp'."\n".'<x-'.$componentTag.' :items="$items" current="/docs/inputs/button" />',
                'table-of-contents' => '@php'."\n".'$sections = ['."\n".'    ["id" => "intro", "label" => "Introduction"],'."\n".'    ["id" => "base", "label" => "Exemple de base"],'."\n".'    ["id" => "api", "label" => "API"]'."\n".'];'."\n".'@endphp'."\n".'<x-'.$componentTag.' :sections="$sections" />',
                default => '<x-'.$componentTag.' />',
            },
            'layout' => match ($name) {
                'card' => '<x-'.$componentTag.' title="Produit Premium" imageUrl="https://picsum.photos/400/300">'."\n".'    <p>Description du produit avec toutes ses caractéristiques.</p>'."\n".'    <x-slot:actions>'."\n".'        <x-daisy::ui.inputs.button>Acheter</x-daisy::ui.inputs.button>'."\n".'    </x-slot:actions>'."\n".'</x-'.$componentTag.'>',
                'hero' => '<x-'.$componentTag.' imageUrl="https://picsum.photos/1920/1080">'."\n".'    <div class="hero-content">'."\n".'        <h1 class="text-5xl font-bold">Bienvenue</h1>'."\n".'        <p class="py-6">Découvrez nos services exceptionnels</p>'."\n".'        <x-daisy::ui.inputs.button>Commencer</x-daisy::ui.inputs.button>'."\n".'    </div>'."\n".'</x-'.$componentTag.'>',
                'footer' => '<x-'.$componentTag.'>'."\n".'    <nav>'."\n".'        <h6 class="footer-title">Services</h6>'."\n".'        <a href="/about" class="link link-hover">À propos</a>'."\n".'        <a href="/contact" class="link link-hover">Contact</a>'."\n".'    </nav>'."\n".'    <aside>'."\n".'        <p>© 2024 Mon Entreprise. Tous droits réservés.</p>'."\n".'    </aside>'."\n".'</x-'.$componentTag.'>',
                'divider' => '<x-'.$componentTag.'>OU</x-'.$componentTag.'>',
                'list' => '<x-'.$componentTag.'>'."\n".'    <li class="list-row">'."\n".'        <span>Jean Dupont</span>'."\n".'        <span>jean@example.com</span>'."\n".'    </li>'."\n".'    <li class="list-row">'."\n".'        <span>Marie Martin</span>'."\n".'        <span>marie@example.com</span>'."\n".'    </li>'."\n".'</x-'.$componentTag.'>',
                'list-row' => '<x-'.$componentTag.'>'."\n".'    <span>Nom</span>'."\n".'    <span>Valeur</span>'."\n".'</x-'.$componentTag.'>',
                'stack' => '<x-'.$componentTag.'>'."\n".'    <div class="bg-primary text-primary-content p-4 rounded">Carte 1</div>'."\n".'    <div class="bg-secondary text-secondary-content p-4 rounded">Carte 2</div>'."\n".'</x-'.$componentTag.'>',
                'crud-layout' => '<x-'.$componentTag.'>'."\n".'    <x-daisy::ui.layout.crud-section title="Informations générales" description="Détails de base">'."\n".'        <x-daisy::ui.inputs.input name="name" placeholder="Nom" />'."\n".'        <x-daisy::ui.inputs.input name="email" placeholder="Email" />'."\n".'    </x-daisy::ui.layout.crud-section>'."\n".'    <x-slot:actions>'."\n".'        <x-daisy::ui.inputs.button>Enregistrer</x-daisy::ui.inputs.button>'."\n".'    </x-slot:actions>'."\n".'</x-'.$componentTag.'>',
                'crud-section' => '<x-'.$componentTag.' title="Informations" description="Détails du formulaire">'."\n".'    <x-daisy::ui.inputs.input name="name" placeholder="Nom" />'."\n".'    <x-daisy::ui.inputs.input name="email" placeholder="Email" />'."\n".'</x-'.$componentTag.'>',
                'footer-layout' => '@php'."\n".'$columns = ['."\n".'    ["title" => "Produits", "links" => [["label" => "Fonctionnalités", "href" => "/features"], ["label" => "Tarifs", "href" => "/pricing"]]],'."\n".'    ["title" => "Support", "links" => [["label" => "Documentation", "href" => "/docs"], ["label" => "Contact", "href" => "/contact"]]]'."\n".'];'."\n".'$socialLinks = [["icon" => "bi-twitter", "href" => "#", "label" => "Twitter"], ["icon" => "bi-github", "href" => "#", "label" => "GitHub"]];'."\n".'@endphp'."\n".'<x-'.$componentTag.' :columns="$columns" brandText="Mon Entreprise" copyrightText="Tous droits réservés" :socialLinks="$socialLinks" />',
                'grid-layout' => '<x-'.$componentTag.'>'."\n".'    <div class="col-12 col-md-6 col-lg-4">'."\n".'        <x-daisy::ui.layout.card title="Colonne 1">Contenu 1</x-daisy::ui.layout.card>'."\n".'    </div>'."\n".'    <div class="col-12 col-md-6 col-lg-4">'."\n".'        <x-daisy::ui.layout.card title="Colonne 2">Contenu 2</x-daisy::ui.layout.card>'."\n".'    </div>'."\n".'    <div class="col-12 col-md-6 col-lg-4">'."\n".'        <x-daisy::ui.layout.card title="Colonne 3">Contenu 3</x-daisy::ui.layout.card>'."\n".'    </div>'."\n".'</x-'.$componentTag.'>',
                default => '<x-'.$componentTag.' />',
            },
            'data-display' => match ($name) {
                'badge' => '<x-'.$componentTag.' color="success">Nouveau</x-'.$componentTag.'>',
                'avatar' => '<x-'.$componentTag.' src="https://i.pravatar.cc/150?img=12" alt="Jean Dupont" />',
                'kbd' => '@php $keys = ["Ctrl", "K"]; @endphp'."\n".'<x-'.$componentTag.' :keys="$keys" />',
                'table' => '@php'."\n".'$headers = ["Nom", "Email", "Rôle"];'."\n".'$rows = ['."\n".'    ["Jean Dupont", "jean@example.com", "Admin"],'."\n".'    ["Marie Martin", "marie@example.com", "Utilisateur"]'."\n".'];'."\n".'@endphp'."\n".'<x-'.$componentTag.' :headers="$headers" :rows="$rows" />',
                'stat' => '<x-'.$componentTag.' title="Ventes" value="1,234" desc="+20% ce mois" />',
                'progress' => '<x-'.$componentTag.' value="75" max="100" />',
                'radial-progress' => '<x-'.$componentTag.' value="85" />',
                'status' => '<x-'.$componentTag.' color="success" label="En ligne" />',
                'timeline' => '@php'."\n".'$items = ['."\n".'    ["label" => "Commande créée", "time" => "10:00"],'."\n".'    ["label" => "En préparation", "time" => "11:30", "current" => true],'."\n".'    ["label" => "Expédiée", "time" => null]'."\n".'];'."\n".'@endphp'."\n".'<x-'.$componentTag.' :items="$items" />',
                'file-preview' => '<x-'.$componentTag.' url="https://picsum.photos/400/300" name="image.jpg" type="image" />',
                default => '<x-'.$componentTag.' />',
            },
            'overlay' => match ($name) {
                'modal' => '<x-'.$componentTag.' id="confirm-modal" title="Confirmer la suppression">'."\n".'    <p>Êtes-vous sûr de vouloir supprimer cet élément ? Cette action est irréversible.</p>'."\n".'    <x-slot:actions>'."\n".'        <x-daisy::ui.inputs.button variant="ghost">Annuler</x-daisy::ui.inputs.button>'."\n".'        <x-daisy::ui.inputs.button color="error">Supprimer</x-daisy::ui.inputs.button>'."\n".'    </x-slot:actions>'."\n".'</x-'.$componentTag.'>',
                'drawer' => '<x-'.$componentTag.' id="nav-drawer">'."\n".'    <x-slot:content>'."\n".'        <h1>Contenu principal</h1>'."\n".'    </x-slot:content>'."\n".'    <x-slot:side>'."\n".'        <ul class="menu">'."\n".'            <li><a>Accueil</a></li>'."\n".'            <li><a>Profil</a></li>'."\n".'        </ul>'."\n".'    </x-slot:side>'."\n".'</x-'.$componentTag.'>',
                'dropdown' => '<x-'.$componentTag.' label="Menu">'."\n".'    <ul class="menu">'."\n".'        <li><a>Profil</a></li>'."\n".'        <li><a>Paramètres</a></li>'."\n".'        <li><a>Déconnexion</a></li>'."\n".'    </ul>'."\n".'</x-'.$componentTag.'>',
                'popover' => '<x-'.$componentTag.' title="Informations">'."\n".'    <x-slot:trigger>'."\n".'        <x-daisy::ui.inputs.button>Plus d\'infos</x-daisy::ui.inputs.button>'."\n".'    </x-slot:trigger>'."\n".'    <p>Contenu informatif affiché dans le popover.</p>'."\n".'</x-'.$componentTag.'>',
                'popconfirm' => '<x-'.$componentTag.' message="Voulez-vous vraiment supprimer cet élément ?">'."\n".'    <x-slot:trigger>'."\n".'        <x-daisy::ui.inputs.button color="error">Supprimer</x-daisy::ui.inputs.button>'."\n".'    </x-slot:trigger>'."\n".'</x-'.$componentTag.'>',
                'tooltip' => '<x-'.$componentTag.' text="Cliquez pour en savoir plus">'."\n".'    <x-daisy::ui.inputs.button>Survolez-moi</x-daisy::ui.inputs.button>'."\n".'</x-'.$componentTag.'>',
                default => '<x-'.$componentTag.' />',
            },
            'media' => match ($name) {
                'carousel' => '<x-'.$componentTag.'>'."\n".'    <div class="carousel-item">'."\n".'        <img src="https://picsum.photos/800/400?random=1" alt="Slide 1" />'."\n".'    </div>'."\n".'    <div class="carousel-item">'."\n".'        <img src="https://picsum.photos/800/400?random=2" alt="Slide 2" />'."\n".'    </div>'."\n".'</x-'.$componentTag.'>',
                'lightbox' => '<x-'.$componentTag.' src="https://picsum.photos/1200/800" alt="Photo de paysage" />',
                'media-gallery' => '<x-'.$componentTag.' src="https://picsum.photos/800/600" alt="Galerie photo" />',
                'embed' => '<x-'.$componentTag.' src="https://www.youtube.com/embed/dQw4w9WgXcQ" />',
                'leaflet' => '<x-'.$componentTag.' :lat="48.8566" :lng="2.3522" zoom="13" />',
                default => '<x-'.$componentTag.' />',
            },
            'feedback' => match ($name) {
                'alert' => '<x-'.$componentTag.' color="success" title="Succès">'."\n".'    Votre demande a été traitée avec succès.'."\n".'</x-'.$componentTag.'>',
                'toast' => '<x-'.$componentTag.'>'."\n".'    <div class="alert alert-success">'."\n".'        <span>Message envoyé avec succès !</span>'."\n".'    </div>'."\n".'</x-'.$componentTag.'>',
                'loading' => '<x-'.$componentTag.' shape="spinner" />',
                'skeleton' => '<x-'.$componentTag.' width="300" height="20" />',
                'callout' => '<x-'.$componentTag.' heading="Note importante" text="Veuillez lire attentivement les instructions avant de continuer." />',
                'empty-state' => '<x-'.$componentTag.' icon="bi-inbox" title="Aucun élément" message="Il n\'y a rien à afficher pour le moment." />',
                'loading-message' => '<x-'.$componentTag.' shape="spinner" message="Chargement en cours..." size="lg" />',
                default => '<x-'.$componentTag.' />',
            },
            'utilities' => match ($name) {
                'copyable' => '<p>Vous pouvez copier cette <x-'.$componentTag.'>valeur</x-'.$componentTag.'> directement dans votre presse-papier en cliquant dessus.</p>',
                'mockup-browser' => '<x-'.$componentTag.' url="https://example.com">'."\n".'    <div class="p-4">Contenu de la page</div>'."\n".'</x-'.$componentTag.'>',
                'mockup-code' => '<x-'.$componentTag.'>'."\n".'    <pre data-prefix="$"><code>php artisan serve</code></pre>'."\n".'</x-'.$componentTag.'>',
                'mockup-phone' => '<x-'.$componentTag.'>'."\n".'    <div class="mockup-phone-display">'."\n".'        <div class="p-4">Application mobile</div>'."\n".'    </div>'."\n".'</x-'.$componentTag.'>',
                'mockup-window' => '<x-'.$componentTag.'>'."\n".'    <div class="p-4">Contenu de la fenêtre</div>'."\n".'</x-'.$componentTag.'>',
                'indicator' => '<x-'.$componentTag.' label="3" color="error">'."\n".'    <x-daisy::ui.inputs.button>Notifications</x-daisy::ui.inputs.button>'."\n".'</x-'.$componentTag.'>',
                'dock' => '<x-'.$componentTag.'>'."\n".'    <button><x-daisy::ui.advanced.icon name="house" /> Accueil</button>'."\n".'    <button><x-daisy::ui.advanced.icon name="search" /> Recherche</button>'."\n".'</x-'.$componentTag.'>',
                default => '<x-'.$componentTag.' />',
            },
            'advanced' => match ($name) {
                'accordion' => '@php'."\n".'$items = ['."\n".'    ["title" => "Section 1", "content" => "Contenu de la première section"],'."\n".'    ["title" => "Section 2", "content" => "Contenu de la deuxième section"]'."\n".'];'."\n".'@endphp'."\n".'<x-'.$componentTag.' :items="$items" />',
                'calendar' => '<x-'.$componentTag.' name="date" value="2024-01-15" />',
                'calendar-cally' => '<x-'.$componentTag.' name="event-date" value="2024-12-25" />',
                'calendar-full' => '@php'."\n".'$events = ['."\n".'    ["title" => "Réunion", "start" => "2024-01-15 10:00"],'."\n".'    ["title" => "Déjeuner", "start" => "2024-01-15 12:30"]'."\n".'];'."\n".'@endphp'."\n".'<x-'.$componentTag.' :events="$events" />',
                'calendar-native' => '<x-'.$componentTag.' name="birthday" value="1990-05-15" />',
                'chart' => '@php'."\n".'$labels = ["Jan", "Fév", "Mar"];'."\n".'$datasets = [["label" => "Ventes", "data" => [100, 200, 150]]];'."\n".'@endphp'."\n".'<x-'.$componentTag.' type="line" :labels="$labels" :datasets="$datasets" />',
                'chat-bubble' => '<x-'.$componentTag.' align="start" name="Alice" time="14:30">'."\n".'    Bonjour, comment allez-vous ?'."\n".'</x-'.$componentTag.'>',
                'code-editor' => '<x-'.$componentTag.' language="php" value="<?php echo \"Hello\"; ?>" />',
                'collapse' => '<x-'.$componentTag.' title="Cliquez pour développer">'."\n".'    <p>Contenu masqué qui s\'affiche au clic.</p>'."\n".'</x-'.$componentTag.'>',
                'countdown' => '<x-'.$componentTag.' :values="[\"days\" => 5, \"hours\" => 12, \"min\" => 30, \"sec\" => 45]" />',
                'diff' => '<x-'.$componentTag.'>'."\n".'    <div class="diff-item-1">Version avant</div>'."\n".'    <div class="diff-item-2">Version après</div>'."\n".'</x-'.$componentTag.'>',
                'fieldset' => '<x-'.$componentTag.' legend="Informations personnelles">'."\n".'    <x-daisy::ui.inputs.input name="name" placeholder="Nom" />'."\n".'    <x-daisy::ui.inputs.input name="email" placeholder="Email" />'."\n".'</x-'.$componentTag.'>',
                'filter' => '@php'."\n".'$items = ['."\n".'    ["label" => "Tous", "value" => "all"],'."\n".'    ["label" => "Actifs", "value" => "active"],'."\n".'    ["label" => "Archivés", "value" => "archived"]'."\n".'];'."\n".'@endphp'."\n".'<x-'.$componentTag.' name="status" :items="$items" />',
                'icon' => '<x-'.$componentTag.' name="heart" size="lg" />',
                'join' => '<x-'.$componentTag.'>'."\n".'    <x-daisy::ui.inputs.button>Gauche</x-daisy::ui.inputs.button>'."\n".'    <x-daisy::ui.inputs.button>Droite</x-daisy::ui.inputs.button>'."\n".'</x-'.$componentTag.'>',
                'label' => '<x-'.$componentTag.' for="email" value="Adresse email" />',
                'link' => '<x-'.$componentTag.' href="/about" color="primary">En savoir plus</x-'.$componentTag.'>',
                'login-button' => '<x-'.$componentTag.' provider="github" label="Se connecter avec GitHub" />',
                'mask' => '<x-'.$componentTag.' shape="squircle" src="https://picsum.photos/200/200" alt="Image" />',
                'onboarding' => '<x-'.$componentTag.' :steps="[["title" => "Bienvenue", "content" => "Première étape"]]" />',
                'rating' => '<x-'.$componentTag.' name="rating" :count="5" :value="4" />',
                'scroll-status' => '<x-'.$componentTag.' />',
                'scrollspy' => '<x-'.$componentTag.' :items="[["label" => "Section 1", "href" => "#section1"]]" />',
                'swap' => '<x-'.$componentTag.'>'."\n".'    <div class="swap-on">🌙</div>'."\n".'    <div class="swap-off">☀️</div>'."\n".'</x-'.$componentTag.'>',
                'theme-controller' => '<x-'.$componentTag.' :themes="[\"light\", \"dark\"]" value="light" />',
                'transfer' => '@php'."\n".'$source = [["id" => 1, "label" => "Item 1"], ["id" => 2, "label" => "Item 2"]];'."\n".'@endphp'."\n".'<x-'.$componentTag.' :source="$source" />',
                'tree-view' => '@php'."\n".'$data = [["id" => 1, "label" => "Dossier", "children" => [["id" => 2, "label" => "Fichier"]]]];'."\n".'@endphp'."\n".'<x-'.$componentTag.' :data="$data" />',
                'validator' => '<x-'.$componentTag.' state="error" message="Ce champ est requis" />',
                'wysiwyg' => '<x-'.$componentTag.' name="content" value="<p>Contenu riche</p>" />',
                default => '<x-'.$componentTag.' />',
            },
            'changelog' => match ($name) {
                'changelog-change-item' => '<x-'.$componentTag.' type="added" description="Nouvelle fonctionnalité permettant de filtrer les résultats" />',
                'changelog-header' => '<x-'.$componentTag.' title="Changelog" currentVersion="1.0.0" />',
                'changelog-toolbar' => '<x-'.$componentTag.' showSearch="true" showFilters="true" />',
                'changelog-version-item' => '@php'."\n".'$items = ['."\n".'    ["type" => "added", "description" => "Nouvelle fonctionnalité"],'."\n".'    ["type" => "fixed", "description" => "Correction d\'un bug"],'."\n".'    ["type" => "changed", "description" => "Amélioration des performances"]'."\n".'];'."\n".'@endphp'."\n".'<x-'.$componentTag.' version="1.0.0" date="2024-01-15" :items="$items" />',
                default => '<x-'.$componentTag.' />',
            },
            'communication' => match ($name) {
                'chat-header' => '@php'."\n".'$conversation = ['."\n".'    "id" => 1,'."\n".'    "name" => "Alice Martin",'."\n".'    "avatar" => "https://i.pravatar.cc/150?img=12",'."\n".'    "isOnline" => true'."\n".'];'."\n".'@endphp'."\n".'<x-'.$componentTag.' :conversation="$conversation" />',
                'chat-input' => '<x-'.$componentTag.' placeholder="Tapez votre message..." />',
                'chat-messages' => '@php'."\n".'$messages = ['."\n".'    ["id" => 1, "user_id" => 2, "content" => "Bonjour !", "created_at" => "2024-01-15 14:30:00", "user_name" => "Alice", "user_avatar" => "https://i.pravatar.cc/150?img=12"],'."\n".'    ["id" => 2, "user_id" => 1, "content" => "Salut, comment ça va ?", "created_at" => "2024-01-15 14:31:00", "user_name" => "Vous"]'."\n".'];'."\n".'@endphp'."\n".'<x-'.$componentTag.' :messages="$messages" currentUserId="1" />',
                'chat-sidebar' => '@php'."\n".'$conversations = ['."\n".'    ["id" => 1, "name" => "Alice Martin", "avatar" => "https://i.pravatar.cc/150?img=12", "lastMessage" => "Dernier message", "unreadCount" => 2, "isOnline" => true],'."\n".'    ["id" => 2, "name" => "Bob Dupont", "avatar" => "https://i.pravatar.cc/150?img=13", "lastMessage" => "Salut !", "unreadCount" => 0, "isOnline" => false]'."\n".'];'."\n".'@endphp'."\n".'<x-'.$componentTag.' :conversations="$conversations" />',
                'chat-widget' => '@php'."\n".'$conversation = ["id" => 1, "name" => "Support", "avatar" => "https://i.pravatar.cc/150?img=12", "isOnline" => true];'."\n".'$messages = [["id" => 1, "user_id" => 2, "content" => "Bonjour, comment puis-je vous aider ?", "created_at" => "2024-01-15 14:30:00", "user_name" => "Support"]];'."\n".'@endphp'."\n".'<x-'.$componentTag.' :conversation="$conversation" :messages="$messages" currentUserId="1" position="bottom-right" />',
                'conversation-view' => '@php'."\n".'$conversation = ["id" => 1, "name" => "Alice", "avatar" => "https://i.pravatar.cc/150?img=12", "isOnline" => true];'."\n".'$messages = [["id" => 1, "user_id" => 2, "content" => "Bonjour !", "created_at" => "2024-01-15 14:30:00", "user_name" => "Alice"]];'."\n".'@endphp'."\n".'<x-'.$componentTag.' :conversation="$conversation" :messages="$messages" currentUserId="1" />',
                'notification-bell' => '@php'."\n".'$notifications = ['."\n".'    ["id" => 1, "type" => "info", "data" => ["message" => "Nouveau message", "priority" => "normal", "user" => ["name" => "Alice"]], "read_at" => null, "created_at" => "2024-01-15 10:00:00"]'."\n".'];'."\n".'@endphp'."\n".'<x-'.$componentTag.' :notifications="$notifications" unreadCount="1" />',
                'notification-filters' => '@php'."\n".'$types = ["info", "warning", "error"];'."\n".'@endphp'."\n".'<x-'.$componentTag.' :types="$types" currentFilter="all" />',
                'notification-item' => '@php'."\n".'$notification = ['."\n".'    "id" => 1,'."\n".'    "type" => "info",'."\n".'    "data" => ["message" => "Vous avez reçu un nouveau message", "priority" => "normal", "user" => ["name" => "Alice", "avatar" => "https://i.pravatar.cc/150?img=12"]],'."\n".'    "read_at" => null,'."\n".'    "created_at" => "2024-01-15 10:00:00"'."\n".'];'."\n".'@endphp'."\n".'<x-'.$componentTag.' :notification="$notification" showActions="true" />',
                'notification-list' => '@php'."\n".'$notifications = ['."\n".'    ["id" => 1, "type" => "info", "data" => ["message" => "Message 1"], "read_at" => null, "created_at" => "2024-01-15 10:00:00"],'."\n".'    ["id" => 2, "type" => "warning", "data" => ["message" => "Message 2"], "read_at" => "2024-01-15 09:00:00", "created_at" => "2024-01-15 08:00:00"]'."\n".'];'."\n".'@endphp'."\n".'<x-'.$componentTag.' :notifications="$notifications" groupByDate="true" />',
                default => '<x-'.$componentTag.' />',
            },
            'errors' => match ($name) {
                'error-actions' => '<x-'.$componentTag.' homeUrl="/" backUrl="/previous" />',
                'error-content' => '<x-'.$componentTag.' statusCode="404" title="Page non trouvée" message="La page demandée n\'existe pas." />',
                'error-header' => '<x-'.$componentTag.' statusCode="500" />',
                'loading-state-content' => '<x-'.$componentTag.' type="spinner" message="Chargement en cours..." />',
                default => '<x-'.$componentTag.' />',
            },
            default => '<x-'.$componentTag.' />',
        };
    }

    private function generateVariantsExample(string $componentTag, string $name, string $category, array $props): string
    {
        $hasColor = in_array('color', $props);
        $hasVariant = in_array('variant', $props);
        $hasSize = in_array('size', $props);

        if (! $hasColor && ! $hasVariant && ! $hasSize) {
            return '';
        }

        $examples = [];
        $examplesCode = [];

        // Générer des exemples adaptés selon le type de composant
        $isInput = in_array($category, ['inputs']) && ! in_array($name, ['button']);

        if ($hasColor) {
            if ($isInput) {
                $examples[] = '<x-'.$componentTag.' color="primary" placeholder="Primary" />';
                $examplesCode[] = '<x-'.$componentTag.' color="primary" placeholder="Primary" />';
                $examples[] = '<x-'.$componentTag.' color="secondary" placeholder="Secondary" />';
                $examplesCode[] = '<x-'.$componentTag.' color="secondary" placeholder="Secondary" />';
            } else {
                $examples[] = '<x-'.$componentTag.' color="primary">Primary</x-'.$componentTag.'>';
                $examplesCode[] = '<x-'.$componentTag.' color="primary">Primary</x-'.$componentTag.'>';
                $examples[] = '<x-'.$componentTag.' color="secondary">Secondary</x-'.$componentTag.'>';
                $examplesCode[] = '<x-'.$componentTag.' color="secondary">Secondary</x-'.$componentTag.'>';
            }
        }

        if ($hasVariant) {
            if ($isInput) {
                $examples[] = '<x-'.$componentTag.' variant="outline" placeholder="Outline" />';
                $examplesCode[] = '<x-'.$componentTag.' variant="outline" placeholder="Outline" />';
                $examples[] = '<x-'.$componentTag.' variant="ghost" placeholder="Ghost" />';
                $examplesCode[] = '<x-'.$componentTag.' variant="ghost" placeholder="Ghost" />';
            } else {
                $examples[] = '<x-'.$componentTag.' variant="outline">Outline</x-'.$componentTag.'>';
                $examplesCode[] = '<x-'.$componentTag.' variant="outline">Outline</x-'.$componentTag.'>';
                $examples[] = '<x-'.$componentTag.' variant="ghost">Ghost</x-'.$componentTag.'>';
                $examplesCode[] = '<x-'.$componentTag.' variant="ghost">Ghost</x-'.$componentTag.'>';
            }
        }

        if ($hasSize) {
            if ($isInput) {
                $examples[] = '<x-'.$componentTag.' size="sm" placeholder="Small" />';
                $examplesCode[] = '<x-'.$componentTag.' size="sm" placeholder="Small" />';
                $examples[] = '<x-'.$componentTag.' size="lg" placeholder="Large" />';
                $examplesCode[] = '<x-'.$componentTag.' size="lg" placeholder="Large" />';
            } else {
                $examples[] = '<x-'.$componentTag.' size="sm">Small</x-'.$componentTag.'>';
                $examplesCode[] = '<x-'.$componentTag.' size="sm">Small</x-'.$componentTag.'>';
                $examples[] = '<x-'.$componentTag.' size="lg">Large</x-'.$componentTag.'>';
                $examplesCode[] = '<x-'.$componentTag.' size="lg">Large</x-'.$componentTag.'>';
            }
        }

        if (empty($examples)) {
            return '';
        }

        // Ajouter l'indentation appropriée à chaque exemple
        $indentedExamples = array_map(function ($example) {
            return '                    '.$example;
        }, $examples);
        $examplesHtml = implode("\n", $indentedExamples);
        $examplesCodeHtml = implode("\n", $examplesCode);
        $examplesCodeHtml = $this->cleanCodeIndentation($examplesCodeHtml);
        $examplesCodeHtml = $this->escapeBlade($examplesCodeHtml);

        return <<<BLADE

    <section id="variants" class="mt-10">
        <h2>Variantes</h2>
        <div class="tabs tabs-box">
            <input type="radio" name="variants-example-{$name}" class="tab" aria-label="Preview" checked />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                <div class="not-prose flex flex-wrap items-center gap-3">
{$examplesHtml}
                </div>
            </div>
            <input type="radio" name="variants-example-{$name}" class="tab" aria-label="Code" />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                @php
                    \$variantsCode = {$this->escapeBladeForPhp($examplesCodeHtml)};
                @endphp
                <x-daisy::ui.advanced.code-editor 
                    language="blade" 
                    :value="\$variantsCode"
                    :readonly="true"
                    :showToolbar="false"
                    :showFoldAll="false"
                    :showUnfoldAll="false"
                    :showFormat="false"
                    :showCopy="true"
                    height="200px"
                />
            </div>
        </div>
    </section>
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

    private function escapeBlade(string $content): string
    {
        return htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Échappe le contenu pour l'utiliser dans une chaîne PHP (pour variable)
     */
    private function escapeBladeForPhp(string $content): string
    {
        // Échapper les guillemets simples et doubles, et les backslashes pour utilisation dans une chaîne PHP
        return var_export($content, true);
    }

    private function escapeHtml(string $content): string
    {
        return htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Échappe le HTML pour l'utiliser dans une chaîne Blade (échappe les guillemets)
     * Le contenu sera rendu avec {!! !!} par le composant tabs, donc on échappe juste les guillemets
     */
    private function escapeHtmlForBlade(string $content): string
    {
        // Échapper les guillemets doubles et simples pour utilisation dans une chaîne PHP
        return addslashes($content);
    }

    /**
     * Nettoie l'indentation du code en supprimant les espaces inutiles au début
     * tout en préservant l'indentation relative des éléments imbriqués
     */
    private function cleanCodeIndentation(string $code): string
    {
        $lines = explode("\n", $code);
        if (empty($lines)) {
            return $code;
        }

        // Trouver le nombre minimum d'espaces/tabs au début des lignes non vides
        $minIndent = null;
        foreach ($lines as $line) {
            $trimmed = ltrim($line);
            if ($trimmed === '') {
                continue; // Ignorer les lignes vides
            }
            $indent = strlen($line) - strlen($trimmed);
            if ($minIndent === null || $indent < $minIndent) {
                $minIndent = $indent;
            }
        }

        // Si aucune indentation trouvée, retourner tel quel
        if ($minIndent === null || $minIndent === 0) {
            return trim($code);
        }

        // Retirer l'indentation minimale de toutes les lignes
        $cleaned = [];
        foreach ($lines as $line) {
            if (trim($line) === '') {
                $cleaned[] = '';
            } else {
                $cleaned[] = substr($line, $minIndent);
            }
        }

        $result = implode("\n", $cleaned);

        return trim($result);
    }
}

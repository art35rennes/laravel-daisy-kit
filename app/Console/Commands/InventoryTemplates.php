<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

class InventoryTemplates extends Command
{
    protected $signature = 'inventory:templates';

    protected $description = 'Génère l\'inventaire complet des templates avec catégories et routes';

    private array $categoryMap = [
        'auth' => [
            'label' => 'Authentification',
            'description' => 'Templates pour les pages d\'authentification et de gestion de compte.',
            'icon' => 'lock',
        ],
        'form' => [
            'label' => 'Formulaires',
            'description' => 'Templates de formulaires avec différentes organisations et structures.',
            'icon' => 'file-text',
        ],
        'profile' => [
            'label' => 'Profil utilisateur',
            'description' => 'Templates pour la gestion et l\'affichage des profils utilisateurs.',
            'icon' => 'user',
        ],
        'layout' => [
            'label' => 'Layouts',
            'description' => 'Templates de structure de page et de navigation.',
            'icon' => 'layout',
        ],
        'communication' => [
            'label' => 'Communication',
            'description' => 'Templates pour les fonctionnalités de communication (chat, notifications).',
            'icon' => 'message',
        ],
        'changelog' => [
            'label' => 'Documentation',
            'description' => 'Templates de documentation et changelog pour afficher l\'historique des versions.',
            'icon' => 'file-text',
        ],
        'errors' => [
            'label' => 'Erreurs et états',
            'description' => 'Templates pour la gestion des erreurs HTTP et des états de l\'application.',
            'icon' => 'alert',
        ],
    ];

    public function handle(): int
    {
        $this->info('Génération de l\'inventaire des templates...');

        // S'assurer que les routes web sont chargées
        if (file_exists(base_path('routes/web.php'))) {
            require base_path('routes/web.php');
        }

        $templatesPath = resource_path('views/templates');
        $templates = [];
        $categories = [];

        $files = [];

        // Scanner uniquement les templates dans resources/views/templates/ (source canonique)
        if (File::isDirectory($templatesPath)) {
            $pattern = str_replace('\\', '/', $templatesPath).'/**/*.blade.php';
            $templateFiles = glob($pattern, GLOB_BRACE) ?: [];
            $rootPattern = str_replace('\\', '/', $templatesPath).'/*.blade.php';
            $rootFiles = glob($rootPattern, GLOB_BRACE) ?: [];
            $files = array_merge($files, $templateFiles, $rootFiles);
        }

        if (empty($files)) {
            $this->error('Aucun template trouvé dans templates/');

            return Command::FAILURE;
        }

        $files = array_map(fn ($path) => new \SplFileInfo($path), array_unique($files));

        foreach ($files as $file) {
            // Ignorer les partials
            if (str_contains($file->getPathname(), '/partials/')) {
                continue;
            }

            $filePath = $file->getPathname();

            $relativePath = str_replace([$templatesPath, '\\'], ['', '/'], $filePath);
            $relativePath = ltrim($relativePath, '/');
            $pathParts = explode('/', $relativePath);
            $filename = str_replace('.blade.php', '', array_pop($pathParts));

            // Déterminer la catégorie depuis le chemin ou le nom du fichier
            if (! empty($pathParts)) {
                $category = $pathParts[0];
            } else {
                // Templates à la racine : déterminer la catégorie depuis le nom
                $category = $this->detectCategoryFromName($filename);
            }

            // Construire le nom du template (sans extension)
            $name = $filename;

            // Construire le chemin de vue (toujours templates.*)
            $viewPath = 'daisy::templates.'.str_replace('/', '.', $relativePath);
            $viewPath = str_replace('.blade.php', '', $viewPath);

            // Déterminer le type (réutilisable vs exemple)
            $type = $this->detectTemplateType($category, $name);

            // Chercher la route correspondante
            // Pour les templates à la racine avec préfixe (form-*, profile-*), enlever le préfixe du nom de route
            $routeName = $this->findRouteName($category, $name, $pathParts);

            // Lire les props pour extraire des informations
            $content = File::get($file->getPathname());
            $description = $this->extractDescription($content, $name, $category);
            $tags = $this->generateTags($name, $category);

            $templateData = [
                'name' => $name,
                'category' => $category,
                'label' => $this->labelize($name),
                'description' => $description,
                'route' => $routeName,
                'view' => $viewPath,
                'type' => $type,
                'tags' => $tags,
            ];

            // Ajouter le champ component uniquement pour les templates réutilisables
            if ($type === 'reusable') {
                $templateData['component'] = $viewPath;
            }

            $templates[] = $templateData;

            // Collecter les catégories uniques
            if (! isset($categories[$category])) {
                $categories[$category] = $this->categoryMap[$category] ?? [
                    'id' => $category,
                    'label' => $this->labelize($category),
                    'description' => '',
                    'icon' => null,
                ];
            }
        }

        // Créer le dossier de destination
        $devDataPath = resource_path('dev/data');
        File::ensureDirectoryExists($devDataPath);

        // Générer le manifeste JSON
        $manifest = [
            'generated_at' => now()->toIso8601String(),
            'templates' => $templates,
            'categories' => array_values($categories),
        ];

        File::put($devDataPath.'/templates.json', json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        $this->info('✓ '.count($templates).' templates inventoriés');
        $this->info('✓ Manifeste généré: resources/dev/data/templates.json');

        return Command::SUCCESS;
    }

    private function findRouteName(string $category, string $name, array $pathParts): ?string
    {
        // Si le template est à la racine avec un préfixe (form-*, profile-*), enlever le préfixe
        $routeName = $name;
        if (str_starts_with($name, $category.'-')) {
            $routeName = str_replace($category.'-', '', $name);
        }

        // Patterns de noms de routes possibles (ordre de priorité)
        $patterns = [
            "templates.{$category}.{$routeName}", // Format standard : templates.profile.edit (sans préfixe)
            "templates.{$category}.{$name}", // Format avec préfixe : templates.profile.profile-edit (compatibilité)
        ];

        // Cas spéciaux pour les layouts (ancien format pour compatibilité)
        if ($category === 'layout') {
            $patterns[] = "templates.layouts.{$name}"; // Nouveau format : templates.layouts.navbar
            $patterns[] = "layouts.{$name}"; // Ancien format : layouts.navbar (compatibilité)
        }

        // Cas spéciaux pour la documentation/changelog
        if ($category === 'changelog') {
            $patterns[] = "templates.documentation.{$name}"; // Format : templates.documentation.changelog
        }

        // Vérifier les routes enregistrées directement
        $routes = Route::getRoutes();
        foreach ($patterns as $pattern) {
            try {
                // Essayer Route::has() d'abord
                if (Route::has($pattern)) {
                    return $pattern;
                }
            } catch (\Exception $e) {
                // Ignorer les erreurs
            }

            try {
                // Vérifier aussi directement dans la collection de routes
                $route = $routes->getByName($pattern);
                if ($route) {
                    return $pattern;
                }
            } catch (\Exception $e) {
                // Ignorer les erreurs
            }
        }

        return null;
    }

    private function extractDescription(string $content, string $name, string $category): string
    {
        // Chercher un commentaire de description au début du fichier
        if (preg_match('/@description\s+(.+)/i', $content, $matches)) {
            return trim($matches[1]);
        }

        // Description par défaut basée sur le nom et la catégorie
        $defaults = [
            'auth' => [
                'login-simple' => 'Page de connexion simple et centrée.',
                'login-split' => 'Page de connexion en deux colonnes avec illustration/témoignage.',
                'register-simple' => 'Page d\'inscription simple et centrée.',
                'register-split' => 'Page d\'inscription en deux colonnes avec illustration/témoignage.',
                'forgot-password' => 'Page de demande de réinitialisation de mot de passe.',
                'reset-password' => 'Formulaire d\'envoi de lien de réinitialisation.',
                'two-factor' => 'Page d\'authentification à deux facteurs (2FA).',
                'verify-email' => 'Confirmation d\'email avec bouton de renvoi.',
                'resend-verification' => 'Formulaire pour renvoyer l\'email de vérification.',
            ],
            'form' => [
                'form-inline' => 'Formulaire avec champs en ligne (horizontal).',
                'form-with-tabs' => 'Formulaire organisé en onglets pour une meilleure organisation.',
                'form-wizard' => 'Formulaire en plusieurs étapes avec progression visuelle.',
            ],
            'profile' => [
                'profile-view' => 'Page de visualisation de profil utilisateur avec statistiques et badges.',
                'profile-edit' => 'Page d\'édition de profil utilisateur avec formulaire complet.',
                'profile-settings' => 'Page de paramètres de profil avec onglets pour différentes sections.',
            ],
            'layout' => [
                'navbar' => 'Barre de navigation en haut de page avec menu horizontal et actions.',
                'sidebar' => 'Barre latérale de navigation avec menu vertical et sous-menus.',
                'navbar-sidebar' => 'Combinaison navbar et sidebar pour applications complexes.',
                'grid-layout' => 'Layout en grille responsive pour affichage de contenu structuré.',
                'crud-layout' => 'Layout complet pour interfaces CRUD avec tableaux et formulaires.',
                'footer' => 'Template avec footer en bas de page, inclut colonnes de navigation, copyright et réseaux sociaux.',
                'grid' => 'Template avec grille 12 colonnes responsive (classes Bootstrap-like) pour structurer le contenu.',
                'navbar-footer' => 'Template combiné avec navbar en haut et footer en bas, idéal pour pages complètes.',
                'navbar-grid-footer' => 'Template complet avec navbar, grille responsive et footer, solution tout-en-un pour pages structurées.',
            ],
            'communication' => [
                'chat' => 'Interface de chat en temps réel avec sidebar des conversations et zone de messages.',
                'notification-center' => 'Centre de notifications avec filtres, marquage lu/non lu et actions.',
            ],
            'changelog' => [
                'changelog' => 'Page de changelog organisée par versions avec filtres, recherche et navigation.',
            ],
            'errors' => [
                'error' => 'Template généralisé pour toutes les pages d\'erreur HTTP (404, 500, 403, etc.).',
                'maintenance' => 'Page de maintenance affichée quand l\'application est en mode maintenance.',
                'empty-state' => 'Template pour afficher un état vide (aucune donnée, aucun résultat).',
                'loading-state' => 'Template pour afficher un état de chargement avec indicateur visuel.',
            ],
        ];

        return $defaults[$category][$name] ?? 'Template '.$this->labelize($name).'.';
    }

    private function generateTags(string $name, string $category): array
    {
        $tags = [$category];

        // Tags spécifiques basés sur le nom
        if (str_contains($name, 'login') || str_contains($name, 'auth')) {
            $tags[] = 'authentication';
        }
        if (str_contains($name, 'register') || str_contains($name, 'signup')) {
            $tags[] = 'signup';
        }
        if (str_contains($name, 'password')) {
            $tags[] = 'password';
        }
        if (str_contains($name, 'form')) {
            $tags[] = 'form';
        }
        if (str_contains($name, 'wizard') || str_contains($name, 'step')) {
            $tags[] = 'wizard';
            $tags[] = 'multi-step';
        }
        if (str_contains($name, 'tab')) {
            $tags[] = 'tabs';
        }
        if (str_contains($name, 'profile')) {
            $tags[] = 'user';
        }
        if (str_contains($name, 'chat') || str_contains($name, 'notification')) {
            $tags[] = 'communication';
            $tags[] = 'realtime';
        }
        if (str_contains($name, 'changelog')) {
            $tags[] = 'documentation';
            $tags[] = 'version';
        }
        if (str_contains($name, 'error') || str_contains($name, 'maintenance') || str_contains($name, 'empty-state') || str_contains($name, 'loading-state')) {
            $tags[] = 'error';
            $tags[] = 'state';
        }

        return array_unique($tags);
    }

    private function detectCategoryFromName(string $filename): string
    {
        // Détecter la catégorie depuis le préfixe du nom
        if (str_starts_with($filename, 'form-')) {
            return 'form';
        }
        if (str_starts_with($filename, 'profile-')) {
            return 'profile';
        }
        if (str_starts_with($filename, 'auth-') || in_array($filename, ['login', 'register', 'forgot-password', 'reset-password', 'two-factor', 'verify-email', 'resend-verification'])) {
            return 'auth';
        }
        if (in_array($filename, ['chat', 'notification-center'])) {
            return 'communication';
        }
        if ($filename === 'changelog') {
            return 'changelog';
        }
        if (in_array($filename, ['error', 'maintenance', 'empty-state', 'loading-state'])) {
            return 'errors';
        }

        return 'misc';
    }

    private function labelize(string $slug): string
    {
        $slug = str_replace(['-', '_'], ' ', $slug);
        $slug = preg_replace('/\s+/', ' ', $slug ?? '') ?? '';

        return mb_convert_case(trim($slug), MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * Détermine si un template est réutilisable ou un exemple.
     */
    private function detectTemplateType(string $category, string $name): string
    {
        // Templates réutilisables : auth/*, error, empty-state, loading-state, maintenance
        if ($category === 'auth') {
            return 'reusable';
        }

        if (in_array($name, ['error', 'empty-state', 'loading-state', 'maintenance'])) {
            return 'reusable';
        }

        // Tous les autres sont des exemples
        return 'example';
    }
}

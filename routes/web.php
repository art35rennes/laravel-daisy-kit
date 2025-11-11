<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // Si la documentation publique est activée, rediriger la racine vers la page Docs
    if (config('daisy-kit.docs.enabled')) {
        $prefix = (string) config('daisy-kit.docs.prefix', 'docs');

        return redirect('/'.ltrim($prefix, '/'));
    }

    return view('welcome');
});

// Page de démo réservée au dev (non publiée) via le namespace daisy-dev
Route::get('/demo', function () {
    return view('daisy-dev::demo.ui.index');
})->name('demo');

// Endpoint REST simple pour Calendar Full (démo)
Route::get('/demo/api/calendar-events', function (\Illuminate\Http\Request $request) {
    $start = new DateTime((string) $request->query('start', date('Y-m-01')));
    $end = new DateTime((string) $request->query('end', date('Y-m-t')));
    // Génère quelques évènements factices dans la plage demandée
    $events = [];
    $cur = clone $start;
    while ($cur < $end) {
        $day = (int) $cur->format('j');
        if (in_array($day, [1, 7, 12, 14, 28], true)) {
            $iso = $cur->format('Y-m-d');
            if ($day === 1) {
                $events[] = ['id' => "a-$iso", 'title' => 'All Day Event', 'start' => $iso, 'allDay' => true];
            }
            if ($day === 12) {
                $events[] = ['id' => "m1-$iso", 'title' => 'Meeting', 'start' => "$iso 10:30", 'end' => "$iso 12:30"];
            }
            if ($day === 14) {
                $events[] = ['id' => "b-$iso", 'title' => 'Birthday Party', 'start' => "$iso 07:00"];
            }
            if ($day === 28) {
                $events[] = ['id' => "g-$iso", 'title' => 'Click for Google', 'start' => $iso, 'url' => 'https://google.com'];
            }
            if ($day === 7) {
                $events[] = ['id' => "long-$iso", 'title' => 'Long Event', 'start' => $iso, 'end' => $cur->modify('+7 day')->format('Y-m-d')];
            }
        }
        $cur->modify('+1 day');
    }

    return response()->json($events);
})->name('demo.calendar.events');

// Endpoint REST pour lazy-loading du TreeView en démo
Route::get('/demo/api/tree-children', function (\Illuminate\Http\Request $request) {
    $node = (string) $request->query('node', '');
    // Exemple simple: renvoie des enfants factices selon l'id du nœud
    $data = match ($node) {
        'b' => [
            ['id' => 'b1', 'label' => 'Fichier B1'],
            // Exemple de nœud lazy imbriqué (avec disabled supporté)
            ['id' => 'b2', 'label' => 'Dossier B2 (lazy, disabled)', 'lazy' => true, 'disabled' => true],
            ['id' => 'b3', 'label' => 'Fichier B3'],
        ],
        // Quand on ouvre B2, on renvoie ses enfants (B2-2 devient lazy)
        'b2' => [
            ['id' => 'b2-1', 'label' => 'Fichier B2-1'],
            ['id' => 'b2-2', 'label' => 'Dossier B2-2 (lazy)', 'lazy' => true],
        ],
        // Les enfants de B2-2 ne sont renvoyés que lorsqu'on ouvre B2-2
        'b2-2' => [
            ['id' => 'b2-2-1', 'label' => 'Fichier B2-2-1'],
            ['id' => 'b2-2-2', 'label' => 'Fichier B2-2-2'],
        ],
        'root' => [
            ['id' => 'r1', 'label' => 'Fichier Racine 1'],
            ['id' => 'r2', 'label' => 'Fichier Racine 2'],
        ],
        default => [
            ['id' => $node.'-1', 'label' => 'Fichier '.$node.'-1'],
            ['id' => $node.'-2', 'label' => 'Fichier '.$node.'-2'],
        ],
    };

    return response()->json($data);
})->name('demo.tree.children');

// Endpoint REST pour recherche dans le TreeView (démo)
Route::get('/demo/api/tree-search', function (\Illuminate\Http\Request $request) {
    $q = strtolower((string) $request->query('q', ''));
    // Données de démo correspondant à demoTreeSingle
    $data = [
        ['id' => 'root', 'label' => 'Racine', 'children' => [
            ['id' => 'a', 'label' => 'Dossier A', 'children' => [
                ['id' => 'a1', 'label' => 'Fichier A1'],
                ['id' => 'a2', 'label' => 'Fichier A2'],
            ]],
            ['id' => 'b', 'label' => 'Dossier B (lazy)', 'children' => [
                ['id' => 'b1', 'label' => 'Fichier B1'],
                ['id' => 'b2', 'label' => 'Dossier B2 (lazy, disabled)', 'children' => [
                    ['id' => 'b2-1', 'label' => 'Fichier B2-1'],
                    ['id' => 'b2-2', 'label' => 'Dossier B2-2 (lazy)', 'children' => [
                        ['id' => 'b2-2-1', 'label' => 'Fichier B2-2-1'],
                        ['id' => 'b2-2-2', 'label' => 'Fichier B2-2-2'],
                    ]],
                ]],
            ]],
            ['id' => 'c', 'label' => 'Fichier C'],
        ]],
    ];
    $paths = [];
    $cur = [];
    $walk = function ($nodes) use (&$walk, &$paths, &$cur, $q) {
        foreach ($nodes as $n) {
            $cur[] = $n['id'];
            $label = strtolower((string) ($n['label'] ?? (string) $n['id']));
            if ($q !== '' && (str_contains($label, $q) || str_contains(strtolower((string) $n['id']), $q))) {
                // On retourne uniquement le chemin jusqu'au nœud trouvé (sans forcer l'extension des descendants)
                $paths[] = $cur; // copie implicite
            }
            $children = $n['children'] ?? [];
            if (! empty($children)) {
                $walk($children);
            }
            array_pop($cur);
        }
    };
    $walk($data);
    // Limite pour éviter les réponses trop volumineuses côté démo
    $paths = array_slice($paths, 0, 50);

    return response()->json(['paths' => $paths]);
})->name('demo.tree.search');

// Endpoint REST pour autocomplete des selects (démo)
Route::get('/demo/api/select-options', function (\Illuminate\Http\Request $request) {
    $q = strtolower((string) $request->query('q', ''));
    // Données factices - mélange simple + contacts (avatar/sous-titre)
    $pool = [
        ['value' => 'alpha', 'label' => 'Alpha'],
        ['value' => 'alpine', 'label' => 'Alpine'],
        ['value' => 'beta', 'label' => 'Beta'],
        ['value' => 'bravo', 'label' => 'Bravo'],
        ['value' => 'charlie', 'label' => 'Charlie'],
        ['value' => 'delta', 'label' => 'Delta'],
        ['value' => 'echo', 'label' => 'Echo'],
        ['value' => 'foxtrot', 'label' => 'Foxtrot'],
        ['value' => 'golf', 'label' => 'Golf'],
        ['value' => 'hotel', 'label' => 'Hotel'],
        ['value' => 'india', 'label' => 'India'],
        ['value' => 'juliet', 'label' => 'Juliet'],
        ['value' => 'kilo', 'label' => 'Kilo'],
        ['value' => 'lima', 'label' => 'Lima'],
        ['value' => 'mike', 'label' => 'Mike'],
        ['value' => 'november', 'label' => 'November'],
        ['value' => 'oscar', 'label' => 'Oscar'],
        ['value' => 'papa', 'label' => 'Papa'],
        ['value' => 'quebec', 'label' => 'Quebec'],
        ['value' => 'romeo', 'label' => 'Romeo'],
        ['value' => 'sierra', 'label' => 'Sierra'],
        ['value' => 'tango', 'label' => 'Tango'],
        ['value' => 'uniform', 'label' => 'Uniform'],
        ['value' => 'victor', 'label' => 'Victor'],
        ['value' => 'whiskey', 'label' => 'Whiskey'],
        ['value' => 'xray', 'label' => 'X-Ray'],
        ['value' => 'yankee', 'label' => 'Yankee'],
        ['value' => 'zulu', 'label' => 'Zulu'],
    ];
    $contacts = [
        ['value' => 'c_john', 'label' => 'John Carter', 'subtitle' => 'john.carter@example.com', 'avatar' => '/img/people/people-1.jpg'],
        ['value' => 'c_jane', 'label' => 'Jane Doe', 'subtitle' => 'jane.doe@example.com', 'avatar' => '/img/people/people-2.jpg'],
        ['value' => 'c_alex', 'label' => 'Alex Martin', 'subtitle' => 'alex.martin@example.com', 'avatar' => '/img/people/people-3.jpg'],
        ['value' => 'c_sara', 'label' => 'Sara Kim', 'subtitle' => 'sara.kim@example.com', 'avatar' => '/img/people/people-4.jpg'],
        ['value' => 'c_luc', 'label' => 'Luc Bernard', 'subtitle' => 'luc.bernard@example.com', 'avatar' => '/img/people/people-5.jpg'],
    ];

    // Réponse groupée si la requête commence par "@"
    if (str_starts_with((string) $request->query('q', ''), '@')) {
        $groups = [
            ['title' => 'Contacts', 'items' => $contacts],
            ['title' => 'Mots', 'items' => array_slice($pool, 0, 8)],
        ];

        return response()->json([
            'groups' => $groups,
            'meta' => ['more' => 0],
        ]);
    }

    // Réponse items + meta.more
    $items = [];
    foreach (array_merge($contacts, $pool) as $item) {
        $label = strtolower((string) ($item['label'] ?? ''));
        $value = strtolower((string) ($item['value'] ?? ''));
        $subtitle = strtolower((string) ($item['subtitle'] ?? ''));
        if ($q === '' || str_contains($label, $q) || str_contains($value, $q) || ($subtitle !== '' && str_contains($subtitle, $q))) {
            $items[] = $item;
        }
    }
    $limit = 10;
    $more = max(0, count($items) - $limit);
    $items = array_slice($items, 0, $limit);

    return response()->json([
        'items' => $items,
        'meta' => ['more' => $more],
    ]);
})->name('demo.select.options');

// Routes pour les templates
Route::prefix('templates')->name('templates.')->group(function () {
    // Templates d'authentification
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::view('/login-simple', 'daisy::templates.auth.login-simple')->name('login-simple');
        Route::view('/login-split', 'daisy::templates.auth.login-split')->name('login-split');
        Route::view('/register-simple', 'daisy::templates.auth.register-simple')->name('register-simple');
        Route::view('/register-split', 'daisy::templates.auth.register-split')->name('register-split');
        Route::view('/forgot-password', 'daisy::templates.auth.forgot-password')->name('forgot-password');
        Route::view('/reset-password', 'daisy::templates.auth.reset-password')->name('reset-password');
        Route::view('/two-factor', 'daisy::templates.auth.two-factor')->name('two-factor');
        Route::view('/verify-email', 'daisy::templates.auth.verify-email')->name('verify-email');
        Route::view('/resend-verification', 'daisy::templates.auth.resend-verification')->name('resend-verification');
    });

    // Templates de formulaires
    Route::prefix('form')->name('form.')->group(function () {
        Route::view('/inline', 'daisy-dev::demo.templates.form-inline')->name('inline');

        // Route GET pour afficher le formulaire avec onglets
        Route::get('/with-tabs', function (\Illuminate\Http\Request $request) {
            return view('daisy-dev::demo.templates.form-with-tabs', [
                'activeTab' => $request->query('tab', 'general'),
            ]);
        })->name('with-tabs');

        // Route POST pour traiter le formulaire avec onglets (dummy route pour la démo)
        Route::post('/with-tabs', function (\Illuminate\Http\Request $request) {
            // Validation simple pour la démo
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'nullable|string|max:20',
                'bio' => 'nullable|string|max:1000',
                'street' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'postal_code' => 'nullable|string|max:10',
                'country' => 'nullable|string|max:2',
                'language' => 'nullable|string|max:5',
                'timezone' => 'nullable|string|max:50',
                'notifications' => 'nullable|boolean',
                '_active_tab' => 'nullable|string',
            ]);

            // Simuler un traitement (en production, on sauvegarderait en base de données)
            // Sauvegarder les données dans la session pour la démo
            session()->put('demo_user', $validated);
            session()->flash('success', 'Profil mis à jour avec succès !');

            // Rediriger vers la page avec l'onglet actif préservé
            return redirect()->route('templates.form.with-tabs', [
                'tab' => $request->input('_active_tab', 'general'),
            ])->withInput();
        })->name('with-tabs.store');

        // Route GET pour afficher le wizard
        Route::get('/wizard', function (\Illuminate\Http\Request $request) {
            return view('daisy-dev::demo.templates.form-wizard', [
                'currentStep' => $request->query('step', 1),
            ]);
        })->name('wizard');

        // Route POST pour traiter le wizard (dummy route pour la démo)
        Route::post('/wizard', function (\Illuminate\Http\Request $request) {
            $step = (int) $request->input('step', 1);
            $action = $request->input('wizard_action', 'next');
            $mode = $request->input('wizard_mode', 'accumulation');

            // Validation simple pour la démo selon l'étape
            $rules = [];
            if ($step === 1) {
                $rules = [
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'required|string|max:255',
                    'date_of_birth' => 'nullable|date',
                    'gender' => 'nullable|string|in:male,female,other',
                    'nationality' => 'nullable|string|max:2',
                    'profession' => 'nullable|string|max:255',
                    'education_level' => 'nullable|string|in:bac,bac+2,bac+3,bac+5,doctorat',
                ];
            } elseif ($step === 2) {
                $rules = [
                    'email' => 'required|email|max:255',
                    'phone' => 'nullable|string|max:20',
                    'address' => 'nullable|string|max:500',
                    'postal_code' => 'nullable|string|max:10',
                    'city' => 'nullable|string|max:255',
                    'country' => 'required|string|max:2',
                    'preferred_contact' => 'nullable|string|in:email,phone,sms',
                ];
            } elseif ($step === 3) {
                $rules = [
                    'password' => 'required|string|min:8',
                    'password_confirmation' => 'required|string|same:password',
                    'security_question' => 'required|string|in:mother_maiden_name,first_pet,birth_city,school_name',
                    'security_answer' => 'required|string|max:255',
                    'two_factor' => 'nullable|boolean',
                    'terms' => 'required|accepted',
                ];
            } elseif ($step === 4) {
                $rules = [
                    'confirm' => 'required|accepted',
                ];
            }

            // Validation pour le wizard workflow (création de compte) - détecté par la présence de username ou confirm_account
            if ($request->has('confirm_account') || ($request->has('username') && ! $request->has('project_name'))) {
                if ($step === 1) {
                    $rules = [
                        'email' => 'required|email|max:255',
                    ];
                } elseif ($step === 2) {
                    $rules = [
                        'username' => 'required|string|max:255',
                        'first_name' => 'required|string|max:255',
                        'last_name' => 'required|string|max:255',
                    ];
                } elseif ($step === 3) {
                    $rules = [
                        'language' => 'required|string|in:fr,en,es',
                        'timezone' => 'nullable|string|max:50',
                        'subscription_plan' => 'required|string|in:free,basic,pro,enterprise',
                        'notify_email' => 'nullable|boolean',
                        'notify_push' => 'nullable|boolean',
                        'notify_sms' => 'nullable|boolean',
                        'newsletter' => 'nullable|string|in:never,weekly,monthly,quarterly',
                    ];
                } elseif ($step === 4) {
                    $rules = [
                        'confirm_account' => 'required|accepted',
                    ];
                }
            }
            // Validation pour le wizard vertical (projet)
            elseif ($request->has('project_name')) {
                if ($step === 1) {
                    $rules = [
                        'project_name' => 'required|string|max:255',
                        'project_description' => 'nullable|string|max:1000',
                        'project_type' => 'required|string|in:web,mobile,desktop,api',
                        'start_date' => 'nullable|date',
                        'end_date' => 'nullable|date|after_or_equal:start_date',
                        'project_status' => 'nullable|string|in:planning,active,on_hold,completed',
                        'client_name' => 'nullable|string|max:255',
                    ];
                } elseif ($step === 2) {
                    $rules = [
                        'team_leader' => 'required|string|max:255',
                        'team_size' => 'required|integer|min:1|max:50',
                        'team_skills' => 'nullable|string|max:1000',
                        'budget' => 'nullable|numeric|min:0',
                        'team_location' => 'nullable|string|in:remote,hybrid,onsite',
                        'team_experience' => 'nullable|string|in:junior,mid,senior,expert',
                        'stakeholders' => 'nullable|string|max:1000',
                    ];
                } elseif ($step === 3) {
                    $rules = [
                        'repository_url' => 'nullable|url|max:255',
                        'environment' => 'required|string|in:development,staging,production',
                        'priority' => 'nullable|string|in:low,medium,high,urgent',
                        'technologies' => 'nullable|string|max:1000',
                        'risks' => 'nullable|string|max:1000',
                        'deliverables' => 'nullable|string|max:1000',
                    ];
                } elseif ($step === 4) {
                    $rules = [
                        'confirm_project' => 'required|accepted',
                    ];
                }
            }

            if (! empty($rules)) {
                $validated = $request->validate($rules);
                // Sauvegarder les données dans la session pour la démo
                $wizardData = session('wizard_data', []);
                $wizardData = array_merge($wizardData, $validated);
                session()->put('wizard_data', $wizardData);
            }

            // En mode workflow, enrichir les données pour l'étape suivante
            $enrichedData = [];
            if ($mode === 'workflow' && $action === 'next') {
                // Exemple d'enrichissement : générer des suggestions basées sur les données précédentes
                $wizardData = session('wizard_data', []);

                // Exemple : si on a un type de projet, suggérer des compétences
                if (isset($wizardData['project_type'])) {
                    $suggestions = match ($wizardData['project_type']) {
                        'web' => ['HTML/CSS', 'JavaScript', 'PHP', 'Laravel'],
                        'mobile' => ['React Native', 'Flutter', 'Swift', 'Kotlin'],
                        'desktop' => ['Electron', 'Qt', 'JavaFX', 'WPF'],
                        'api' => ['REST', 'GraphQL', 'gRPC', 'WebSocket'],
                        default => [],
                    };
                    $enrichedData['suggested_skills'] = $suggestions;
                }

                // Exemple : si on a une taille d'équipe, suggérer un budget
                if (isset($wizardData['team_size'])) {
                    $teamSize = (int) $wizardData['team_size'];
                    $suggestedBudget = $teamSize * 50000; // 50k par personne
                    $enrichedData['suggested_budget'] = $suggestedBudget;
                }

                // Enrichissement pour le wizard création de compte
                if (isset($wizardData['email']) && ! isset($wizardData['project_type'])) {
                    // Exemple : si on a un email, suggérer un nom d'utilisateur
                    $emailParts = explode('@', $wizardData['email']);
                    $enrichedData['suggested_username'] = $emailParts[0] ?? '';
                }

                session()->put('wizard_enriched_data', $enrichedData);
            }

            // Gérer la navigation
            if ($action === 'prev') {
                $newStep = max(1, $step - 1);
            } elseif ($action === 'next') {
                $newStep = min(4, $step + 1);
            } else {
                // finish
                session()->flash('success', 'Wizard terminé avec succès !');
                $newStep = 1;
                session()->forget('wizard_data');
                session()->forget('wizard_enriched_data');
            }

            session()->put('wizard_step', $newStep);

            // Rediriger vers la page avec l'étape préservée
            return redirect()->route('templates.form.wizard', [
                'step' => $newStep,
            ])->withInput();
        })->name('wizard.store');
    });

    // Templates de profil
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::view('/view', 'daisy::templates.profile-view')->name('view');
        Route::view('/edit', 'daisy::templates.profile-edit')->name('edit');
        Route::view('/settings', 'daisy::templates.profile-settings')->name('settings');
    });

    // Templates de layouts
    Route::prefix('layouts')->name('layouts.')->group(function () {
        Route::view('/navbar', 'daisy-dev::demo.templates.test-navbar')->name('navbar');
        Route::view('/sidebar', 'daisy-dev::demo.templates.test-sidebar')->name('sidebar');
        Route::view('/navbar-sidebar', 'daisy-dev::demo.templates.test-navbar-sidebar')->name('navbar-sidebar');
        Route::view('/grid-layout', 'daisy-dev::demo.templates.test-grid-layout')->name('grid-layout');
        Route::view('/crud-layout', 'daisy-dev::demo.templates.test-crud-layout')->name('crud-layout');
    });
});

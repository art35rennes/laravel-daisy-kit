@php
    // Données d'exemple pour la démonstration
    $users = [
        ['id' => 1, 'name' => 'Jean Dupont', 'email' => 'jean@example.com', 'role' => 'admin'],
        ['id' => 2, 'name' => 'Marie Martin', 'email' => 'marie@example.com', 'role' => 'user'],
        ['id' => 3, 'name' => 'Pierre Durand', 'email' => 'pierre@example.com', 'role' => 'user'],
    ];
    
    $query = request()->query('q', '');
    $roleFilter = request()->query('role', '');
    
    // Filtrer les utilisateurs pour la démo
    $filteredUsers = collect($users)->filter(function ($user) use ($query, $roleFilter) {
        $matchesQuery = empty($query) || stripos($user['name'], $query) !== false || stripos($user['email'], $query) !== false;
        $matchesRole = empty($roleFilter) || $user['role'] === $roleFilter;
        return $matchesQuery && $matchesRole;
    });
@endphp

<x-daisy::layout.app title="Form Inline - Exemple" :container="true">
    <div class="max-w-6xl mx-auto py-8 space-y-8">
        <div class="text-center">
            <h1 class="text-3xl font-bold mb-2">Form Inline</h1>
            <p class="text-base-content/70">Formulaire compact inline pour recherches et filtres</p>
        </div>

        {{-- Exemple 1: Recherche d'utilisateurs --}}
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                <h2 class="card-title mb-4">Exemple 1 : Recherche d'utilisateurs</h2>
                
                <x-daisy::form.inline
                    action="{{ route('templates.form.inline') }}"
                    method="GET"
                    submitText="Rechercher"
                    size="sm"
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
                </x-daisy::form.inline>

                @if(!empty($query) || !empty($roleFilter))
                    <div class="mt-4">
                        <h3 class="font-semibold mb-2">Résultats ({{ $filteredUsers->count() }})</h3>
                        <div class="space-y-2">
                            @foreach($filteredUsers as $user)
                                <div class="card bg-base-200">
                                    <div class="card-body p-4">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h4 class="font-semibold">{{ $user['name'] }}</h4>
                                                <p class="text-sm text-base-content/70">{{ $user['email'] }}</p>
                                            </div>
                                            <span class="badge badge-primary">{{ $user['role'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Exemple 2: Filtres de produits --}}
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                <h2 class="card-title mb-4">Exemple 2 : Filtres de produits</h2>
                
                <x-daisy::form.inline
                    action="#"
                    method="GET"
                    submitText="Filtrer"
                    resetText="Réinitialiser"
                    size="md"
                >
                    <x-daisy::ui.partials.form-field name="category" :showLabels="false">
                        <x-daisy::ui.inputs.select name="category" size="md">
                            <option value="">Toutes les catégories</option>
                            <option value="electronics">Électronique</option>
                            <option value="clothing">Vêtements</option>
                            <option value="books">Livres</option>
                        </x-daisy::ui.inputs.select>
                    </x-daisy::ui.partials.form-field>

                    <x-daisy::ui.partials.form-field name="price_min" :showLabels="false">
                        <x-daisy::ui.inputs.input
                            name="price_min"
                            type="number"
                            placeholder="Prix min"
                            size="md"
                        />
                    </x-daisy::ui.partials.form-field>

                    <x-daisy::ui.partials.form-field name="price_max" :showLabels="false">
                        <x-daisy::ui.inputs.input
                            name="price_max"
                            type="number"
                            placeholder="Prix max"
                            size="md"
                        />
                    </x-daisy::ui.partials.form-field>
                </x-daisy::form.inline>
            </div>
        </div>

        {{-- Exemple 3: Formulaire POST avec validation --}}
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                <h2 class="card-title mb-4">Exemple 3 : Formulaire POST (avec CSRF)</h2>
                
                <x-daisy::form.inline
                    action="#"
                    method="POST"
                    submitText="Envoyer"
                    size="sm"
                >
                    <x-daisy::ui.partials.form-field name="email" :showLabels="false" class="flex-1">
                        <x-daisy::ui.inputs.input
                            name="email"
                            type="email"
                            placeholder="Votre email"
                            size="sm"
                        />
                    </x-daisy::ui.partials.form-field>
                </x-daisy::form.inline>
            </div>
        </div>
    </div>
</x-daisy::layout.app>


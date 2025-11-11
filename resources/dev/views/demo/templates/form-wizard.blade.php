@php
    // Données d'exemple pour la démonstration
    $wizardData = session('wizard_data', []);
    $wizardEnrichedData = session('wizard_enriched_data', []);
    $currentStep = session('wizard_step', request()->query('step', 1));
@endphp

{{-- Message de succès --}}
@if(session('success'))
    <x-daisy::ui.feedback.alert color="success" class="mb-6">
        {{ session('success') }}
    </x-daisy::ui.feedback.alert>
@endif

{{-- Exemple de wizard en mode accumulation (par défaut) --}}
<div class="mb-4">
    <p class="text-base-content/70 text-sm">
        <strong>Mode accumulation :</strong> Les données sont stockées localement et soumises uniquement à la fin du wizard.
    </p>
</div>

<x-daisy::form.wizard
    title="Inscription - Assistant"
    action="{{ route('templates.form.wizard.store') }}"
    method="POST"
    :steps="[
        ['label' => 'Informations', 'icon' => 'person'],
        ['label' => 'Contact', 'icon' => 'envelope'],
        ['label' => 'Sécurité', 'icon' => 'lock'],
        ['label' => 'Confirmation', 'icon' => 'check-circle'],
    ]"
    :currentStep="$currentStep"
    :linear="true"
    :allowClickNav="false"
    :showControls="true"
    prevText="Précédent"
    nextText="Suivant"
    finishText="Terminer l'inscription"
    :validateOnStep="true"
    :validateOnSubmit="true"
>
    <x-slot:step_1>
        <div class="space-y-4">
            <h3 class="text-lg font-semibold mb-4">Informations personnelles</h3>
            
            <x-daisy::ui.partials.form-field name="first_name" label="Prénom" required>
                <x-daisy::ui.inputs.input
                    name="first_name"
                    type="text"
                    :value="old('first_name', $wizardData['first_name'] ?? '')"
                    placeholder="Votre prénom"
                    required
                />
            </x-daisy::ui.partials.form-field>

            <x-daisy::ui.partials.form-field name="last_name" label="Nom" required>
                <x-daisy::ui.inputs.input
                    name="last_name"
                    type="text"
                    :value="old('last_name', $wizardData['last_name'] ?? '')"
                    placeholder="Votre nom"
                    required
                />
            </x-daisy::ui.partials.form-field>

            <x-daisy::ui.partials.form-field name="date_of_birth" label="Date de naissance">
                <x-daisy::ui.inputs.input
                    name="date_of_birth"
                    type="date"
                    :value="old('date_of_birth', $wizardData['date_of_birth'] ?? '')"
                />
            </x-daisy::ui.partials.form-field>

            <x-daisy::ui.partials.form-field name="gender" label="Genre">
                <x-daisy::ui.inputs.select name="gender">
                    <option value="">Sélectionner</option>
                    <option value="male" {{ old('gender', $wizardData['gender'] ?? '') === 'male' ? 'selected' : '' }}>Homme</option>
                    <option value="female" {{ old('gender', $wizardData['gender'] ?? '') === 'female' ? 'selected' : '' }}>Femme</option>
                    <option value="other" {{ old('gender', $wizardData['gender'] ?? '') === 'other' ? 'selected' : '' }}>Autre</option>
                </x-daisy::ui.inputs.select>
            </x-daisy::ui.partials.form-field>

            <x-daisy::ui.partials.form-field name="nationality" label="Nationalité">
                <x-daisy::ui.inputs.select name="nationality">
                    <option value="">Sélectionner</option>
                    <option value="FR" {{ old('nationality', $wizardData['nationality'] ?? '') === 'FR' ? 'selected' : '' }}>Française</option>
                    <option value="BE" {{ old('nationality', $wizardData['nationality'] ?? '') === 'BE' ? 'selected' : '' }}>Belge</option>
                    <option value="CH" {{ old('nationality', $wizardData['nationality'] ?? '') === 'CH' ? 'selected' : '' }}>Suisse</option>
                    <option value="CA" {{ old('nationality', $wizardData['nationality'] ?? '') === 'CA' ? 'selected' : '' }}>Canadienne</option>
                    <option value="other" {{ old('nationality', $wizardData['nationality'] ?? '') === 'other' ? 'selected' : '' }}>Autre</option>
                </x-daisy::ui.inputs.select>
            </x-daisy::ui.partials.form-field>

            <x-daisy::ui.partials.form-field name="profession" label="Profession">
                <x-daisy::ui.inputs.input
                    name="profession"
                    type="text"
                    :value="old('profession', $wizardData['profession'] ?? '')"
                    placeholder="Développeur, Designer, Manager..."
                />
            </x-daisy::ui.partials.form-field>

            <x-daisy::ui.partials.form-field name="education_level" label="Niveau d'éducation">
                <x-daisy::ui.inputs.select name="education_level">
                    <option value="">Sélectionner</option>
                    <option value="bac" {{ old('education_level', $wizardData['education_level'] ?? '') === 'bac' ? 'selected' : '' }}>Baccalauréat</option>
                    <option value="bac+2" {{ old('education_level', $wizardData['education_level'] ?? '') === 'bac+2' ? 'selected' : '' }}>Bac+2 (BTS, DUT)</option>
                    <option value="bac+3" {{ old('education_level', $wizardData['education_level'] ?? '') === 'bac+3' ? 'selected' : '' }}>Bac+3 (Licence)</option>
                    <option value="bac+5" {{ old('education_level', $wizardData['education_level'] ?? '') === 'bac+5' ? 'selected' : '' }}>Bac+5 (Master)</option>
                    <option value="doctorat" {{ old('education_level', $wizardData['education_level'] ?? '') === 'doctorat' ? 'selected' : '' }}>Doctorat</option>
                </x-daisy::ui.inputs.select>
            </x-daisy::ui.partials.form-field>
        </div>
    </x-slot:step_1>

    <x-slot:step_2>
        <div class="space-y-4">
            <h3 class="text-lg font-semibold mb-4">Informations de contact</h3>
            
            <x-daisy::ui.partials.form-field name="email" label="Email" required>
                <x-daisy::ui.inputs.input
                    name="email"
                    type="email"
                    :value="old('email', $wizardData['email'] ?? '')"
                    placeholder="email@example.com"
                    required
                />
            </x-daisy::ui.partials.form-field>

            <x-daisy::ui.partials.form-field name="phone" label="Téléphone">
                <x-daisy::ui.inputs.input
                    name="phone"
                    type="tel"
                    :value="old('phone', $wizardData['phone'] ?? '')"
                    placeholder="+33 6 12 34 56 78"
                />
            </x-daisy::ui.partials.form-field>

            <x-daisy::ui.partials.form-field name="address" label="Adresse">
                <x-daisy::ui.inputs.textarea
                    name="address"
                    placeholder="Votre adresse complète"
                    rows="3"
                >
                    {{ old('address', $wizardData['address'] ?? '') }}
                </x-daisy::ui.inputs.textarea>
            </x-daisy::ui.partials.form-field>

            <x-daisy::ui.partials.form-field name="postal_code" label="Code postal">
                <x-daisy::ui.inputs.input
                    name="postal_code"
                    type="text"
                    :value="old('postal_code', $wizardData['postal_code'] ?? '')"
                    placeholder="75001"
                    maxlength="10"
                />
            </x-daisy::ui.partials.form-field>

            <x-daisy::ui.partials.form-field name="city" label="Ville">
                <x-daisy::ui.inputs.input
                    name="city"
                    type="text"
                    :value="old('city', $wizardData['city'] ?? '')"
                    placeholder="Paris"
                />
            </x-daisy::ui.partials.form-field>

            <x-daisy::ui.partials.form-field name="country" label="Pays" required>
                <x-daisy::ui.inputs.select name="country" required>
                    <option value="">Sélectionner</option>
                    <option value="FR" {{ old('country', $wizardData['country'] ?? '') === 'FR' ? 'selected' : '' }}>France</option>
                    <option value="BE" {{ old('country', $wizardData['country'] ?? '') === 'BE' ? 'selected' : '' }}>Belgique</option>
                    <option value="CH" {{ old('country', $wizardData['country'] ?? '') === 'CH' ? 'selected' : '' }}>Suisse</option>
                    <option value="CA" {{ old('country', $wizardData['country'] ?? '') === 'CA' ? 'selected' : '' }}>Canada</option>
                    <option value="other" {{ old('country', $wizardData['country'] ?? '') === 'other' ? 'selected' : '' }}>Autre</option>
                </x-daisy::ui.inputs.select>
            </x-daisy::ui.partials.form-field>

            <x-daisy::ui.partials.form-field name="preferred_contact" label="Moyen de contact préféré">
                <x-daisy::ui.inputs.select name="preferred_contact">
                    <option value="">Sélectionner</option>
                    <option value="email" {{ old('preferred_contact', $wizardData['preferred_contact'] ?? '') === 'email' ? 'selected' : '' }}>Email</option>
                    <option value="phone" {{ old('preferred_contact', $wizardData['preferred_contact'] ?? '') === 'phone' ? 'selected' : '' }}>Téléphone</option>
                    <option value="sms" {{ old('preferred_contact', $wizardData['preferred_contact'] ?? '') === 'sms' ? 'selected' : '' }}>SMS</option>
                </x-daisy::ui.inputs.select>
            </x-daisy::ui.partials.form-field>
        </div>
    </x-slot:step_2>

    <x-slot:step_3>
        <div class="space-y-4">
            <h3 class="text-lg font-semibold mb-4">Sécurité du compte</h3>
            
            <x-daisy::ui.partials.form-field name="password" label="Mot de passe" required>
                <x-daisy::ui.inputs.input
                    name="password"
                    type="password"
                    placeholder="••••••••"
                    required
                />
                <x-slot:hint>Minimum 8 caractères, avec majuscules, minuscules et chiffres</x-slot:hint>
            </x-daisy::ui.partials.form-field>

            <x-daisy::ui.partials.form-field name="password_confirmation" label="Confirmation du mot de passe" required>
                <x-daisy::ui.inputs.input
                    name="password_confirmation"
                    type="password"
                    placeholder="••••••••"
                    required
                />
            </x-daisy::ui.partials.form-field>

            <x-daisy::ui.partials.form-field name="security_question" label="Question de sécurité" required>
                <x-daisy::ui.inputs.select name="security_question" required>
                    <option value="">Sélectionner une question</option>
                    <option value="mother_maiden_name" {{ old('security_question', $wizardData['security_question'] ?? '') === 'mother_maiden_name' ? 'selected' : '' }}>Nom de jeune fille de votre mère</option>
                    <option value="first_pet" {{ old('security_question', $wizardData['security_question'] ?? '') === 'first_pet' ? 'selected' : '' }}>Nom de votre premier animal de compagnie</option>
                    <option value="birth_city" {{ old('security_question', $wizardData['security_question'] ?? '') === 'birth_city' ? 'selected' : '' }}>Ville de naissance</option>
                    <option value="school_name" {{ old('security_question', $wizardData['security_question'] ?? '') === 'school_name' ? 'selected' : '' }}>Nom de votre école primaire</option>
                </x-daisy::ui.inputs.select>
            </x-daisy::ui.partials.form-field>

            <x-daisy::ui.partials.form-field name="security_answer" label="Réponse à la question de sécurité" required>
                <x-daisy::ui.inputs.input
                    name="security_answer"
                    type="text"
                    :value="old('security_answer', $wizardData['security_answer'] ?? '')"
                    placeholder="Votre réponse"
                    required
                />
            </x-daisy::ui.partials.form-field>

            <div class="space-y-2">
                <div class="flex items-start gap-3">
                    <x-daisy::ui.inputs.checkbox name="two_factor" id="two_factor" :checked="old('two_factor', $wizardData['two_factor'] ?? false)" />
                    <label for="two_factor" class="text-sm cursor-pointer">
                        Activer l'authentification à deux facteurs (2FA) pour plus de sécurité
                    </label>
                </div>
            </div>

            <div class="space-y-2">
                <div class="flex items-start gap-3">
                    <x-daisy::ui.inputs.checkbox name="terms" id="terms" :checked="old('terms', $wizardData['terms'] ?? false)" required />
                    <label for="terms" class="text-sm cursor-pointer">
                        J'accepte les <a href="#" class="link link-hover">conditions d'utilisation</a> et la <a href="#" class="link link-hover">politique de confidentialité</a>
                    </label>
                </div>
                @if($errors->has('terms'))
                    <x-daisy::ui.advanced.validator state="error" :message="$errors->first('terms')" :full="false" as="div" />
                @endif
            </div>
        </div>
    </x-slot:step_3>

    <x-slot:step_4>
        <div class="space-y-4">
            <h3 class="text-lg font-semibold mb-4">Confirmation</h3>
            
            <div class="alert alert-info">
                <x-daisy::ui.advanced.icon name="info-circle" size="lg" />
                <div>
                    <h4 class="font-semibold">Vérifiez vos informations</h4>
                    <p class="text-sm">Veuillez vérifier que toutes les informations sont correctes avant de finaliser votre inscription.</p>
                </div>
            </div>

            <div class="card bg-base-200">
                <div class="card-body">
                    <h4 class="font-semibold mb-3">Récapitulatif</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-base-content/70">Nom complet :</span>
                            <span class="font-medium">{{ $wizardData['first_name'] ?? '' }} {{ $wizardData['last_name'] ?? '' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-base-content/70">Email :</span>
                            <span class="font-medium">{{ $wizardData['email'] ?? '' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-base-content/70">Téléphone :</span>
                            <span class="font-medium">{{ $wizardData['phone'] ?? 'Non renseigné' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-base-content/70">Ville :</span>
                            <span class="font-medium">{{ $wizardData['city'] ?? 'Non renseigné' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-base-content/70">Code postal :</span>
                            <span class="font-medium">{{ $wizardData['postal_code'] ?? 'Non renseigné' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-base-content/70">Pays :</span>
                            <span class="font-medium">{{ $wizardData['country'] ?? 'Non renseigné' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-base-content/70">Profession :</span>
                            <span class="font-medium">{{ $wizardData['profession'] ?? 'Non renseigné' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-base-content/70">2FA activé :</span>
                            <span class="font-medium">{{ isset($wizardData['two_factor']) && $wizardData['two_factor'] ? 'Oui' : 'Non' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <x-daisy::ui.inputs.checkbox name="confirm" id="confirm" :checked="false" required />
                <label for="confirm" class="text-sm cursor-pointer">
                    Je confirme que toutes les informations sont correctes
                </label>
            </div>
        </div>
    </x-slot:step_4>
</x-daisy::form.wizard>

{{-- Exemple de wizard vertical --}}
<div class="mt-16 pt-8 border-t border-base-300">
    <h2 class="text-2xl font-semibold mb-6">Exemple de wizard vertical</h2>
    
    <x-daisy::form.wizard
        title="Configuration du projet"
        action="{{ route('templates.form.wizard.store') }}"
        method="POST"
        :steps="[
            ['label' => 'Projet', 'icon' => 'folder'],
            ['label' => 'Équipe', 'icon' => 'people'],
            ['label' => 'Paramètres', 'icon' => 'gear'],
            ['label' => 'Finalisation', 'icon' => 'check-circle'],
        ]"
        :currentStep="$currentStep"
        :vertical="true"
        :linear="true"
        :allowClickNav="true"
        :showControls="true"
        prevText="Précédent"
        nextText="Suivant"
        finishText="Créer le projet"
        :validateOnStep="true"
        :validateOnSubmit="true"
    >
        <x-slot:step_1>
            <div class="space-y-4">
                <h3 class="text-lg font-semibold mb-4">Informations du projet</h3>
                
                <x-daisy::ui.partials.form-field name="project_name" label="Nom du projet" required>
                    <x-daisy::ui.inputs.input
                        name="project_name"
                        type="text"
                        :value="old('project_name', $wizardData['project_name'] ?? '')"
                        placeholder="Mon super projet"
                        required
                    />
                </x-daisy::ui.partials.form-field>

                <x-daisy::ui.partials.form-field name="project_description" label="Description">
                    <x-daisy::ui.inputs.textarea
                        name="project_description"
                        placeholder="Décrivez votre projet en quelques mots..."
                        rows="4"
                    >
                        {{ old('project_description', $wizardData['project_description'] ?? '') }}
                    </x-daisy::ui.inputs.textarea>
                </x-daisy::ui.partials.form-field>

                <x-daisy::ui.partials.form-field name="project_type" label="Type de projet" required>
                    <x-daisy::ui.inputs.select name="project_type" required>
                        <option value="">Sélectionner un type</option>
                        <option value="web" {{ old('project_type', $wizardData['project_type'] ?? '') === 'web' ? 'selected' : '' }}>Application Web</option>
                        <option value="mobile" {{ old('project_type', $wizardData['project_type'] ?? '') === 'mobile' ? 'selected' : '' }}>Application Mobile</option>
                        <option value="desktop" {{ old('project_type', $wizardData['project_type'] ?? '') === 'desktop' ? 'selected' : '' }}>Application Desktop</option>
                        <option value="api" {{ old('project_type', $wizardData['project_type'] ?? '') === 'api' ? 'selected' : '' }}>API</option>
                    </x-daisy::ui.inputs.select>
                </x-daisy::ui.partials.form-field>

                <x-daisy::ui.partials.form-field name="start_date" label="Date de début">
                    <x-daisy::ui.inputs.input
                        name="start_date"
                        type="date"
                        :value="old('start_date', $wizardData['start_date'] ?? '')"
                    />
                </x-daisy::ui.partials.form-field>

                <x-daisy::ui.partials.form-field name="end_date" label="Date de fin prévue">
                    <x-daisy::ui.inputs.input
                        name="end_date"
                        type="date"
                        :value="old('end_date', $wizardData['end_date'] ?? '')"
                    />
                </x-daisy::ui.partials.form-field>

                <x-daisy::ui.partials.form-field name="project_status" label="Statut du projet">
                    <x-daisy::ui.inputs.select name="project_status">
                        <option value="">Sélectionner</option>
                        <option value="planning" {{ old('project_status', $wizardData['project_status'] ?? '') === 'planning' ? 'selected' : '' }}>En planification</option>
                        <option value="active" {{ old('project_status', $wizardData['project_status'] ?? '') === 'active' ? 'selected' : '' }}>Actif</option>
                        <option value="on_hold" {{ old('project_status', $wizardData['project_status'] ?? '') === 'on_hold' ? 'selected' : '' }}>En attente</option>
                        <option value="completed" {{ old('project_status', $wizardData['project_status'] ?? '') === 'completed' ? 'selected' : '' }}>Terminé</option>
                    </x-daisy::ui.inputs.select>
                </x-daisy::ui.partials.form-field>

                <x-daisy::ui.partials.form-field name="client_name" label="Client">
                    <x-daisy::ui.inputs.input
                        name="client_name"
                        type="text"
                        :value="old('client_name', $wizardData['client_name'] ?? '')"
                        placeholder="Nom du client ou entreprise"
                    />
                </x-daisy::ui.partials.form-field>
            </div>
        </x-slot:step_1>

        <x-slot:step_2>
            <div class="space-y-4">
                <h3 class="text-lg font-semibold mb-4">Composition de l'équipe</h3>
                
                <x-daisy::ui.partials.form-field name="team_leader" label="Chef de projet" required>
                    <x-daisy::ui.inputs.input
                        name="team_leader"
                        type="text"
                        :value="old('team_leader', $wizardData['team_leader'] ?? '')"
                        placeholder="Nom du chef de projet"
                        required
                    />
                </x-daisy::ui.partials.form-field>

                <x-daisy::ui.partials.form-field name="team_size" label="Taille de l'équipe" required>
                    <x-daisy::ui.inputs.input
                        name="team_size"
                        type="number"
                        min="1"
                        max="50"
                        :value="old('team_size', $wizardData['team_size'] ?? '')"
                        placeholder="Nombre de membres"
                        required
                    />
                </x-daisy::ui.partials.form-field>

                <x-daisy::ui.partials.form-field name="team_skills" label="Compétences requises">
                    <x-daisy::ui.inputs.textarea
                        name="team_skills"
                        placeholder="Listez les compétences nécessaires (une par ligne)"
                        rows="5"
                    >
                        {{ old('team_skills', $wizardData['team_skills'] ?? '') }}
                    </x-daisy::ui.inputs.textarea>
                </x-daisy::ui.partials.form-field>

                <x-daisy::ui.partials.form-field name="budget" label="Budget estimé (€)">
                    <x-daisy::ui.inputs.input
                        name="budget"
                        type="number"
                        min="0"
                        step="100"
                        :value="old('budget', $wizardData['budget'] ?? '')"
                        placeholder="0"
                    />
                </x-daisy::ui.partials.form-field>

                <x-daisy::ui.partials.form-field name="team_location" label="Localisation de l'équipe">
                    <x-daisy::ui.inputs.select name="team_location">
                        <option value="">Sélectionner</option>
                        <option value="remote" {{ old('team_location', $wizardData['team_location'] ?? '') === 'remote' ? 'selected' : '' }}>Télétravail</option>
                        <option value="hybrid" {{ old('team_location', $wizardData['team_location'] ?? '') === 'hybrid' ? 'selected' : '' }}>Hybride</option>
                        <option value="onsite" {{ old('team_location', $wizardData['team_location'] ?? '') === 'onsite' ? 'selected' : '' }}>Sur site</option>
                    </x-daisy::ui.inputs.select>
                </x-daisy::ui.partials.form-field>

                <x-daisy::ui.partials.form-field name="team_experience" label="Niveau d'expérience requis">
                    <x-daisy::ui.inputs.select name="team_experience">
                        <option value="">Sélectionner</option>
                        <option value="junior" {{ old('team_experience', $wizardData['team_experience'] ?? '') === 'junior' ? 'selected' : '' }}>Junior (0-2 ans)</option>
                        <option value="mid" {{ old('team_experience', $wizardData['team_experience'] ?? '') === 'mid' ? 'selected' : '' }}>Intermédiaire (2-5 ans)</option>
                        <option value="senior" {{ old('team_experience', $wizardData['team_experience'] ?? '') === 'senior' ? 'selected' : '' }}>Senior (5-10 ans)</option>
                        <option value="expert" {{ old('team_experience', $wizardData['team_experience'] ?? '') === 'expert' ? 'selected' : '' }}>Expert (10+ ans)</option>
                    </x-daisy::ui.inputs.select>
                </x-daisy::ui.partials.form-field>

                <x-daisy::ui.partials.form-field name="stakeholders" label="Parties prenantes">
                    <x-daisy::ui.inputs.textarea
                        name="stakeholders"
                        placeholder="Listez les principales parties prenantes du projet"
                        rows="3"
                    >
                        {{ old('stakeholders', $wizardData['stakeholders'] ?? '') }}
                    </x-daisy::ui.inputs.textarea>
                </x-daisy::ui.partials.form-field>
            </div>
        </x-slot:step_2>

        <x-slot:step_3>
            <div class="space-y-4">
                <h3 class="text-lg font-semibold mb-4">Paramètres avancés</h3>
                
                <x-daisy::ui.partials.form-field name="repository_url" label="URL du dépôt Git">
                    <x-daisy::ui.inputs.input
                        name="repository_url"
                        type="url"
                        :value="old('repository_url', $wizardData['repository_url'] ?? '')"
                        placeholder="https://github.com/user/repo"
                    />
                </x-daisy::ui.partials.form-field>

                <x-daisy::ui.partials.form-field name="environment" label="Environnement" required>
                    <x-daisy::ui.inputs.select name="environment" required>
                        <option value="">Sélectionner</option>
                        <option value="development" {{ old('environment', $wizardData['environment'] ?? '') === 'development' ? 'selected' : '' }}>Développement</option>
                        <option value="staging" {{ old('environment', $wizardData['environment'] ?? '') === 'staging' ? 'selected' : '' }}>Staging</option>
                        <option value="production" {{ old('environment', $wizardData['environment'] ?? '') === 'production' ? 'selected' : '' }}>Production</option>
                    </x-daisy::ui.inputs.select>
                </x-daisy::ui.partials.form-field>

                <div class="space-y-3">
                    <x-daisy::ui.partials.form-field name="notifications" label="Notifications">
                        <div class="space-y-2">
                            <div class="flex items-center gap-3">
                                <x-daisy::ui.inputs.checkbox name="notify_email" id="notify_email" :checked="old('notify_email', $wizardData['notify_email'] ?? false)" />
                                <label for="notify_email" class="text-sm cursor-pointer">Notifications par email</label>
                            </div>
                            <div class="flex items-center gap-3">
                                <x-daisy::ui.inputs.checkbox name="notify_slack" id="notify_slack" :checked="old('notify_slack', $wizardData['notify_slack'] ?? false)" />
                                <label for="notify_slack" class="text-sm cursor-pointer">Notifications Slack</label>
                            </div>
                            <div class="flex items-center gap-3">
                                <x-daisy::ui.inputs.checkbox name="notify_webhook" id="notify_webhook" :checked="old('notify_webhook', $wizardData['notify_webhook'] ?? false)" />
                                <label for="notify_webhook" class="text-sm cursor-pointer">Webhooks</label>
                            </div>
                        </div>
                    </x-daisy::ui.partials.form-field>
                </div>

                <x-daisy::ui.partials.form-field name="priority" label="Priorité">
                    <x-daisy::ui.inputs.select name="priority">
                        <option value="low" {{ old('priority', $wizardData['priority'] ?? '') === 'low' ? 'selected' : '' }}>Basse</option>
                        <option value="medium" {{ old('priority', $wizardData['priority'] ?? 'medium') === 'medium' ? 'selected' : '' }}>Moyenne</option>
                        <option value="high" {{ old('priority', $wizardData['priority'] ?? '') === 'high' ? 'selected' : '' }}>Haute</option>
                        <option value="urgent" {{ old('priority', $wizardData['priority'] ?? '') === 'urgent' ? 'selected' : '' }}>Urgente</option>
                    </x-daisy::ui.inputs.select>
                </x-daisy::ui.partials.form-field>

                <x-daisy::ui.partials.form-field name="technologies" label="Technologies utilisées">
                    <x-daisy::ui.inputs.textarea
                        name="technologies"
                        placeholder="Listez les technologies, frameworks et outils utilisés (une par ligne)"
                        rows="4"
                    >
                        {{ old('technologies', $wizardData['technologies'] ?? '') }}
                    </x-daisy::ui.inputs.textarea>
                </x-daisy::ui.partials.form-field>

                <x-daisy::ui.partials.form-field name="risks" label="Risques identifiés">
                    <x-daisy::ui.inputs.textarea
                        name="risks"
                        placeholder="Décrivez les risques potentiels du projet"
                        rows="3"
                    >
                        {{ old('risks', $wizardData['risks'] ?? '') }}
                    </x-daisy::ui.inputs.textarea>
                </x-daisy::ui.partials.form-field>

                <x-daisy::ui.partials.form-field name="deliverables" label="Livrables attendus">
                    <x-daisy::ui.inputs.textarea
                        name="deliverables"
                        placeholder="Listez les principaux livrables du projet"
                        rows="3"
                    >
                        {{ old('deliverables', $wizardData['deliverables'] ?? '') }}
                    </x-daisy::ui.inputs.textarea>
                </x-daisy::ui.partials.form-field>
            </div>
        </x-slot:step_3>

        <x-slot:step_4>
            <div class="space-y-4">
                <h3 class="text-lg font-semibold mb-4">Finalisation</h3>
                
                <div class="alert alert-success">
                    <x-daisy::ui.advanced.icon name="check-circle" size="lg" />
                    <div>
                        <h4 class="font-semibold">Prêt à créer le projet</h4>
                        <p class="text-sm">Vérifiez les informations ci-dessous avant de finaliser la création.</p>
                    </div>
                </div>

                <div class="card bg-base-200">
                    <div class="card-body">
                        <h4 class="font-semibold mb-3">Récapitulatif</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-base-content/70">Nom du projet :</span>
                                <span class="font-medium">{{ $wizardData['project_name'] ?? 'Non renseigné' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-base-content/70">Type :</span>
                                <span class="font-medium">{{ $wizardData['project_type'] ?? 'Non renseigné' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-base-content/70">Chef de projet :</span>
                                <span class="font-medium">{{ $wizardData['team_leader'] ?? 'Non renseigné' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-base-content/70">Taille de l'équipe :</span>
                                <span class="font-medium">{{ $wizardData['team_size'] ?? 'Non renseigné' }} membres</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-base-content/70">Budget :</span>
                                <span class="font-medium">{{ $wizardData['budget'] ?? '0' }} €</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-base-content/70">Environnement :</span>
                                <span class="font-medium">{{ $wizardData['environment'] ?? 'Non renseigné' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-base-content/70">Client :</span>
                                <span class="font-medium">{{ $wizardData['client_name'] ?? 'Non renseigné' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-base-content/70">Localisation équipe :</span>
                                <span class="font-medium">{{ $wizardData['team_location'] ?? 'Non renseigné' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-base-content/70">Priorité :</span>
                                <span class="font-medium">{{ $wizardData['priority'] ?? 'Non renseigné' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <x-daisy::ui.inputs.checkbox name="confirm_project" id="confirm_project" :checked="false" required />
                    <label for="confirm_project" class="text-sm cursor-pointer">
                        Je confirme que toutes les informations sont correctes et que je souhaite créer ce projet
                    </label>
                </div>
            </div>
        </x-slot:step_4>
    </x-daisy::form.wizard>
</div>

{{-- Exemple de wizard en mode workflow --}}
<div class="mt-16 pt-8 border-t border-base-300">
    <h2 class="text-2xl font-semibold mb-6">Exemple de wizard en mode workflow</h2>
    <p class="text-base-content/70 mb-6">Dans ce mode, chaque étape est soumise au serveur pour enrichir l'étape suivante avec des données calculées.</p>
    
    <x-daisy::form.wizard
        title="Création de compte - Workflow"
        action="{{ route('templates.form.wizard.store') }}"
        method="POST"
        :steps="[
            ['label' => 'Email', 'icon' => 'envelope'],
            ['label' => 'Profil', 'icon' => 'person'],
            ['label' => 'Préférences', 'icon' => 'gear'],
            ['label' => 'Confirmation', 'icon' => 'check-circle'],
        ]"
        :currentStep="$currentStep"
        :mode="'workflow'"
        :linear="true"
        :allowClickNav="true"
        :showControls="true"
        prevText="Précédent"
        nextText="Suivant"
        finishText="Créer le compte"
        :validateOnStep="true"
        :validateOnSubmit="true"
    >
        <x-slot:step_1>
            <div class="space-y-4">
                <h3 class="text-lg font-semibold mb-4">Adresse email</h3>
                
                <x-daisy::ui.partials.form-field name="email" label="Email" required>
                    <x-daisy::ui.inputs.input
                        name="email"
                        type="email"
                        :value="old('email', $wizardData['email'] ?? '')"
                        placeholder="email@example.com"
                        required
                    />
                </x-daisy::ui.partials.form-field>

                @if(isset($wizardEnrichedData['suggested_username']))
                    <div class="alert alert-info">
                        <x-daisy::ui.advanced.icon name="info-circle" size="lg" />
                        <div>
                            <p class="text-sm">Nom d'utilisateur suggéré : <strong>{{ $wizardEnrichedData['suggested_username'] }}</strong></p>
                        </div>
                    </div>
                @endif
            </div>
        </x-slot:step_1>

        <x-slot:step_2>
            <div class="space-y-4">
                <h3 class="text-lg font-semibold mb-4">Informations du profil</h3>
                
                <x-daisy::ui.partials.form-field name="username" label="Nom d'utilisateur" required>
                    <x-daisy::ui.inputs.input
                        name="username"
                        type="text"
                        :value="old('username', $wizardEnrichedData['suggested_username'] ?? $wizardData['username'] ?? '')"
                        placeholder="Votre nom d'utilisateur"
                        required
                    />
                </x-daisy::ui.partials.form-field>

                <x-daisy::ui.partials.form-field name="first_name" label="Prénom" required>
                    <x-daisy::ui.inputs.input
                        name="first_name"
                        type="text"
                        :value="old('first_name', $wizardData['first_name'] ?? '')"
                        placeholder="Votre prénom"
                        required
                    />
                </x-daisy::ui.partials.form-field>

                <x-daisy::ui.partials.form-field name="last_name" label="Nom" required>
                    <x-daisy::ui.inputs.input
                        name="last_name"
                        type="text"
                        :value="old('last_name', $wizardData['last_name'] ?? '')"
                        placeholder="Votre nom"
                        required
                    />
                </x-daisy::ui.partials.form-field>
            </div>
        </x-slot:step_2>

        <x-slot:step_3>
            <div class="space-y-4">
                <h3 class="text-lg font-semibold mb-4">Préférences</h3>
                
                <x-daisy::ui.partials.form-field name="language" label="Langue" required>
                    <x-daisy::ui.inputs.select name="language" required>
                        <option value="">Sélectionner</option>
                        <option value="fr" {{ old('language', $wizardData['language'] ?? '') === 'fr' ? 'selected' : '' }}>Français</option>
                        <option value="en" {{ old('language', $wizardData['language'] ?? '') === 'en' ? 'selected' : '' }}>English</option>
                        <option value="es" {{ old('language', $wizardData['language'] ?? '') === 'es' ? 'selected' : '' }}>Español</option>
                    </x-daisy::ui.inputs.select>
                </x-daisy::ui.partials.form-field>

                <x-daisy::ui.partials.form-field name="timezone" label="Fuseau horaire">
                    <x-daisy::ui.inputs.select name="timezone">
                        <option value="">Sélectionner</option>
                        <option value="Europe/Paris" {{ old('timezone', $wizardData['timezone'] ?? '') === 'Europe/Paris' ? 'selected' : '' }}>Europe/Paris</option>
                        <option value="America/New_York" {{ old('timezone', $wizardData['timezone'] ?? '') === 'America/New_York' ? 'selected' : '' }}>America/New_York</option>
                        <option value="Asia/Tokyo" {{ old('timezone', $wizardData['timezone'] ?? '') === 'Asia/Tokyo' ? 'selected' : '' }}>Asia/Tokyo</option>
                    </x-daisy::ui.inputs.select>
                </x-daisy::ui.partials.form-field>

                <x-daisy::ui.partials.form-field name="subscription_plan" label="Plan d'abonnement" required>
                    <x-daisy::ui.inputs.select name="subscription_plan" required>
                        <option value="">Sélectionner</option>
                        <option value="free" {{ old('subscription_plan', $wizardData['subscription_plan'] ?? '') === 'free' ? 'selected' : '' }}>Gratuit</option>
                        <option value="basic" {{ old('subscription_plan', $wizardData['subscription_plan'] ?? '') === 'basic' ? 'selected' : '' }}>Basique (9,99€/mois)</option>
                        <option value="pro" {{ old('subscription_plan', $wizardData['subscription_plan'] ?? '') === 'pro' ? 'selected' : '' }}>Pro (29,99€/mois)</option>
                        <option value="enterprise" {{ old('subscription_plan', $wizardData['subscription_plan'] ?? '') === 'enterprise' ? 'selected' : '' }}>Entreprise (Sur devis)</option>
                    </x-daisy::ui.inputs.select>
                </x-daisy::ui.partials.form-field>

                <div class="space-y-2">
                    <label class="label">Notifications</label>
                    <div class="space-y-2">
                        <div class="flex items-center gap-3">
                            <x-daisy::ui.inputs.checkbox name="notify_email" id="notify_email_wf" :checked="old('notify_email', $wizardData['notify_email'] ?? true)" />
                            <label for="notify_email_wf" class="text-sm cursor-pointer">Notifications par email</label>
                        </div>
                        <div class="flex items-center gap-3">
                            <x-daisy::ui.inputs.checkbox name="notify_push" id="notify_push_wf" :checked="old('notify_push', $wizardData['notify_push'] ?? false)" />
                            <label for="notify_push_wf" class="text-sm cursor-pointer">Notifications push</label>
                        </div>
                        <div class="flex items-center gap-3">
                            <x-daisy::ui.inputs.checkbox name="notify_sms" id="notify_sms_wf" :checked="old('notify_sms', $wizardData['notify_sms'] ?? false)" />
                            <label for="notify_sms_wf" class="text-sm cursor-pointer">Notifications SMS</label>
                        </div>
                    </div>
                </div>

                <x-daisy::ui.partials.form-field name="newsletter" label="Abonnement newsletter">
                    <x-daisy::ui.inputs.select name="newsletter">
                        <option value="never" {{ old('newsletter', $wizardData['newsletter'] ?? '') === 'never' ? 'selected' : '' }}>Jamais</option>
                        <option value="weekly" {{ old('newsletter', $wizardData['newsletter'] ?? '') === 'weekly' ? 'selected' : '' }}>Hebdomadaire</option>
                        <option value="monthly" {{ old('newsletter', $wizardData['newsletter'] ?? '') === 'monthly' ? 'selected' : '' }}>Mensuel</option>
                        <option value="quarterly" {{ old('newsletter', $wizardData['newsletter'] ?? '') === 'quarterly' ? 'selected' : '' }}>Trimestriel</option>
                    </x-daisy::ui.inputs.select>
                </x-daisy::ui.partials.form-field>
            </div>
        </x-slot:step_3>

        <x-slot:step_4>
            <div class="space-y-4">
                <h3 class="text-lg font-semibold mb-4">Confirmation</h3>
                
                <div class="alert alert-success">
                    <x-daisy::ui.advanced.icon name="check-circle" size="lg" />
                    <div>
                        <h4 class="font-semibold">Prêt à créer votre compte</h4>
                        <p class="text-sm">Vérifiez les informations ci-dessous avant de finaliser.</p>
                    </div>
                </div>

                <div class="card bg-base-200">
                    <div class="card-body">
                        <h4 class="font-semibold mb-3">Récapitulatif</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-base-content/70">Email :</span>
                                <span class="font-medium">{{ $wizardData['email'] ?? 'Non renseigné' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-base-content/70">Nom d'utilisateur :</span>
                                <span class="font-medium">{{ $wizardData['username'] ?? 'Non renseigné' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-base-content/70">Nom complet :</span>
                                <span class="font-medium">{{ $wizardData['first_name'] ?? '' }} {{ $wizardData['last_name'] ?? '' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-base-content/70">Langue :</span>
                                <span class="font-medium">{{ $wizardData['language'] ?? 'Non renseigné' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-base-content/70">Fuseau horaire :</span>
                                <span class="font-medium">{{ $wizardData['timezone'] ?? 'Non renseigné' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-base-content/70">Plan d'abonnement :</span>
                                <span class="font-medium">{{ $wizardData['subscription_plan'] ?? 'Non renseigné' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-base-content/70">Newsletter :</span>
                                <span class="font-medium">{{ $wizardData['newsletter'] ?? 'Non renseigné' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <x-daisy::ui.inputs.checkbox name="confirm_account" id="confirm_account_wf" :checked="false" required />
                    <label for="confirm_account_wf" class="text-sm cursor-pointer">
                        Je confirme que toutes les informations sont correctes
                    </label>
                </div>
            </div>
        </x-slot:step_4>
    </x-daisy::form.wizard>
</div>


<x-daisy::layout.navbar-layout title="Wizard multi-étapes">
    <div class="max-w-4xl mx-auto">
        @php
            $step_profile = view('daisy-dev::demo.templates.forms.partials.wizard-step-profile')->render();
            $step_contact = view('daisy-dev::demo.templates.forms.partials.wizard-step-contact')->render();
            $step_preferences = view('daisy-dev::demo.templates.forms.partials.wizard-step-preferences')->render();
            $step_summary = view('daisy-dev::demo.templates.forms.partials.wizard-step-summary')->render();
            $summary = view('daisy-dev::demo.templates.forms.partials.wizard-summary')->render();
        @endphp

        @include('daisy::templates.form.form-wizard', [
            'title' => 'Création de compte',
            'action' => '#',
            'method' => 'POST',
            'steps' => [
                // Icônes Bootstrap : person, envelope, gear, check-circle
                ['key' => 'profile', 'label' => 'Profil', 'icon' => 'person'],
                ['key' => 'contact', 'label' => 'Contact', 'icon' => 'envelope'],
                ['key' => 'preferences', 'label' => 'Préférences', 'icon' => 'gear'],
                ['key' => 'summary', 'label' => 'Résumé', 'icon' => 'check-circle'],
            ],
            'currentStep' => 1,
            'linear' => true,
            'showSummary' => true,
        ])
    </div>
</x-daisy::layout.navbar-layout>

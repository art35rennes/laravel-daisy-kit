@php
    $tab_general = view('daisy-dev::demo.templates.forms.partials.tab-general')->render();
    $tab_security = view('daisy-dev::demo.templates.forms.partials.tab-security')->render();
    $tab_notifications = view('daisy-dev::demo.templates.forms.partials.tab-notifications')->render();
    $actions = view('daisy-dev::demo.templates.forms.partials.tabs-actions')->render();
@endphp

<x-daisy::layout.navbar-layout title="Formulaire à onglets">
    <div class="max-w-4xl mx-auto">
        @include('daisy::templates.form.form-with-tabs', [
            'title' => 'Modifier le profil',
            'action' => '#',
            'method' => 'POST',
            'tabs' => [
                ['id' => 'general', 'label' => 'Général'],
                ['id' => 'security', 'label' => 'Sécurité'],
                ['id' => 'notifications', 'label' => 'Notifications'],
            ],
            'tabsStyle' => 'box',
            'showErrorBadges' => true,
            'fieldToTabMap' => [
                'name' => 'general',
                'email' => 'general',
                'password' => 'security',
                'password_confirmation' => 'security',
                'newsletter' => 'notifications',
            ],
        ])
    </div>
</x-daisy::layout.navbar-layout>

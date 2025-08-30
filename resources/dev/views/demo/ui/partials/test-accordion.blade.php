<!-- Accordion -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Accordion</h2>
    <div class="grid md:grid-cols-2 gap-6">
        <!-- Arrow style -->
        <x-daisy::ui.accordion :items="[
            ['title' => 'Comment créer un compte ?', 'content' => 'Cliquez sur S\'inscrire...','checked' => true],
            ['title' => 'Mot de passe oublié ?', 'content' => 'Utilisez le lien mot de passe oublié.'],
            ['title' => 'Mettre à jour le profil ?', 'content' => 'Allez dans Mon compte > Éditer'],
        ]" :arrow="true" />

        <!-- Plus style + états forcés -->
        <x-daisy::ui.accordion :items="[
            ['title' => 'Section ouverte forcée', 'content' => 'Toujours ouverte', 'open' => true],
            ['title' => 'Section fermée forcée', 'content' => 'Toujours fermée', 'close' => true],
            ['title' => 'Section normale', 'content' => 'S\'ouvre via radio'],
        ]" :arrow="false" />
    </div>
</section>



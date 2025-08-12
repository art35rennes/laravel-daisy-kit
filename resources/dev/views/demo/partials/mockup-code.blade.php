<!-- Mockup Code -->
<section class="space-y-6 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Mockup Code</h2>
    <div class="space-y-6">
        <!-- Avec préfixe -->
        <x-daisy::ui.mockup-code :lines="[
            ['prefix' => '$', 'text' => 'npm i daisyui']
        ]" class="w-full" />

        <!-- Multi-lignes + couleurs -->
        <x-daisy::ui.mockup-code :lines="[
            ['prefix' => '$', 'text' => 'npm i daisyui'],
            ['prefix' => '>', 'text' => 'installing...', 'class' => 'text-warning'],
            ['prefix' => '>', 'text' => 'Done!', 'class' => 'text-success']
        ]" class="w-full" />

        <!-- Ligne surlignée -->
        <x-daisy::ui.mockup-code :lines="[
            ['prefix' => '1', 'text' => 'npm i daisyui'],
            ['prefix' => '2', 'text' => 'installing...'],
            ['prefix' => '3', 'text' => 'Error!', 'highlight' => 'warning']
        ]" class="w-full" />

        <!-- Longues lignes -->
        <x-daisy::ui.mockup-code :lines="[
            ['prefix' => '~', 'text' => 'Magnam dolore beatae necessitatibus nemopsum itaque sit. Et porro quae qui et et dolore ratione.']
        ]" class="w-full" />

        <!-- Sans préfixe -->
        <x-daisy::ui.mockup-code :lines="[
            ['text' => 'without prefix']
        ]" class="w-full" />

        <!-- Avec couleur de fond -->
        <x-daisy::ui.mockup-code class="bg-primary text-primary-content w-full">
            <pre><code>can be any color!</code></pre>
        </x-daisy::ui.mockup-code>
    </div>
</section>



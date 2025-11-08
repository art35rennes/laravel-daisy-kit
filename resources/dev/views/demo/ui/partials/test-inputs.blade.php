<!-- Inputs -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Inputs</h2>
    <div class="space-y-3">
        <div class="grid md:grid-cols-3 gap-3">
            <div class="form-control">
                <x-daisy::ui.advanced.label for="email" value="Email" />
                <x-daisy::ui.inputs.input id="email" type="email" placeholder="john@doe.dev" />
            </div>
            <div class="form-control">
                <x-daisy::ui.advanced.label for="name" value="Name" />
                <x-daisy::ui.inputs.input id="name" placeholder="Jane Doe" variant="ghost" />
            </div>
            <div class="form-control">
                <x-daisy::ui.advanced.label for="password" value="Password" />
                <x-daisy::ui.inputs.input id="password" type="password" color="primary" />
            </div>
        </div>
        <div class="grid grid-cols-5 gap-3">
            <x-daisy::ui.inputs.input size="xs" placeholder="Xsmall" />
            <x-daisy::ui.inputs.input size="sm" placeholder="Small" />
            <x-daisy::ui.inputs.input size="md" placeholder="Medium" />
            <x-daisy::ui.inputs.input size="lg" placeholder="Large" />
            <x-daisy::ui.inputs.input size="xl" placeholder="Xlarge" />
        </div>
        <div class="grid md:grid-cols-3 gap-3">
            <x-daisy::ui.inputs.input placeholder="Disabled" disabled />
            <x-daisy::ui.inputs.input placeholder="Error" color="error" />
            <x-daisy::ui.inputs.input placeholder="Success" color="success" />
        </div>

        <!-- Input-wrapper avec contenu (préfixe/suffixe) -->
        <label class="input">
            <svg class="h-[1em] opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g stroke-linejoin="round" stroke-linecap="round" stroke-width="2.5" fill="none" stroke="currentColor"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.3-4.3"></path></g></svg>
            <input type="search" class="grow" placeholder="Search" />
            <kbd class="kbd kbd-sm">⌘</kbd>
            <kbd class="kbd kbd-sm">K</kbd>
        </label>
        <label class="input">
            Path
            <input type="text" class="grow" placeholder="src/app/" />
            <span class="badge badge-neutral badge-xs">Optional</span>
        </label>
    </div>
</section>



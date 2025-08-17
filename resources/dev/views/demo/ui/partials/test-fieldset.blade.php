<!-- Fieldset -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Fieldset</h2>
    <div class="space-y-6">
        <!-- Basique -->
        <x-daisy::ui.fieldset legend="Page title">
            <x-daisy::ui.input placeholder="My awesome page" />
            <p class="label">You can edit page title later on from settings</p>
        </x-daisy::ui.fieldset>

        <!-- Background + border + rounded + width + padding -->
        <x-daisy::ui.fieldset legend="Page details" bg="base-200" :bordered="true" width="w-xs" padding="p-4">
            <label class="label">Title</label>
            <x-daisy::ui.input placeholder="My awesome page" />
            <label class="label">Slug</label>
            <x-daisy::ui.input placeholder="my-awesome-page" />
            <label class="label">Author</label>
            <x-daisy::ui.input placeholder="Name" />
        </x-daisy::ui.fieldset>

        <!-- Join items -->
        <x-daisy::ui.fieldset legend="Settings" bg="base-200" :bordered="true" width="w-xs" padding="p-4">
            <div class="join">
                <input type="text" class="input join-item" placeholder="Product name" />
                <button class="btn join-item">save</button>
            </div>
        </x-daisy::ui.fieldset>

        <!-- Login -->
        <x-daisy::ui.fieldset legend="Login" bg="base-200" :bordered="true" width="w-xs" padding="p-4">
            <label class="label">Email</label>
            <input type="email" class="input" placeholder="Email" />
            <label class="label">Password</label>
            <input type="password" class="input" placeholder="Password" />
            <button class="btn btn-neutral mt-4">Login</button>
        </x-daisy::ui.fieldset>
    </div>
</section>



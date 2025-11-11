<x-daisy::layout.navbar-layout title="Template · CRUD Layout">
    <x-slot:navbarStart>
        <a href="{{ route('templates.index') }}" class="btn btn-ghost btn-sm">Templates</a>
    </x-slot:navbarStart>

    <div class="space-y-6">
        <h1 class="text-2xl font-semibold">Template · CRUD Layout</h1>
        <p class="text-base-content/70">Layout à 2 colonnes (catégorie / inputs) pour les formulaires CRUD, responsive avec breakpoint configurable.</p>

        <x-daisy::ui.layout.crud-layout>
            <x-daisy::ui.layout.crud-section
                title="Profile"
                description="This is how others will see you on the site."
            >
                <x-daisy::ui.partials.form-field name="username" label="Username" hint="This is your public display name. It can be your real name or a pseudonym. You can only change this once every 30 days.">
                    <x-daisy::ui.inputs.input name="username" value="calebporzio" />
                </x-daisy::ui.partials.form-field>

                <x-daisy::ui.partials.form-field name="email" label="Primary email" hint="You can manage verified email addresses in your email settings.">
                    <x-daisy::ui.inputs.select name="email">
                        <option>Select primary email...</option>
                        <option>caleb@example.com</option>
                        <option>caleb.work@example.com</option>
                    </x-daisy::ui.inputs.select>
                </x-daisy::ui.partials.form-field>

                <x-daisy::ui.partials.form-field name="bio" label="Bio" hint="You can @mention other users and organizations to link to them.">
                    <x-daisy::ui.inputs.textarea name="bio" placeholder="Tell us a little bit about yourself" rows="4" />
                </x-daisy::ui.partials.form-field>

                <x-slot:actions>
                    <x-daisy::ui.inputs.button class="btn-primary">Save profile</x-daisy::ui.inputs.button>
                </x-slot:actions>
            </x-daisy::ui.layout.crud-section>

            <x-daisy::ui.layout.crud-section
                title="Preferences"
                description="Customize your layout and notification preferences."
                :borderTop="true"
            >
                <x-daisy::ui.partials.form-field name="sidebar" label="Sidebar" hint="Select the items you want to display in the sidebar.">
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <x-daisy::ui.inputs.checkbox name="sidebar_recents" checked />
                            <span class="text-sm">Recents</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-daisy::ui.inputs.checkbox name="sidebar_home" checked />
                            <span class="text-sm">Home</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-daisy::ui.inputs.checkbox name="sidebar_applications" />
                            <span class="text-sm">Applications</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-daisy::ui.inputs.checkbox name="sidebar_desktop" />
                            <span class="text-sm">Desktop</span>
                        </div>
                    </div>
                </x-daisy::ui.partials.form-field>

                <x-daisy::ui.partials.form-field name="notifications" label="Notify me about...">
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <x-daisy::ui.inputs.radio name="notifications" value="all" checked />
                            <span class="text-sm">All new messages</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-daisy::ui.inputs.radio name="notifications" value="direct" />
                            <span class="text-sm">Direct messages and mentions</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-daisy::ui.inputs.radio name="notifications" value="nothing" />
                            <span class="text-sm">Nothing</span>
                        </div>
                    </div>
                </x-daisy::ui.partials.form-field>

                <x-slot:actions>
                    <x-daisy::ui.inputs.button class="btn-primary">Save preferences</x-daisy::ui.inputs.button>
                </x-slot:actions>
            </x-daisy::ui.layout.crud-section>

            <x-slot:actions>
                <x-daisy::ui.inputs.button class="btn-ghost">Cancel</x-daisy::ui.inputs.button>
                <x-daisy::ui.inputs.button class="btn-primary">Save all</x-daisy::ui.inputs.button>
            </x-slot:actions>
        </x-daisy::ui.layout.crud-layout>
    </div>
</x-daisy::layout.navbar-layout>


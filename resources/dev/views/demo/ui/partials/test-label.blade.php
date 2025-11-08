<!-- Label -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Label</h2>
    <div class="space-y-6">
        <div class="flex items-center gap-4">
            <x-daisy::ui.advanced.label for="demo-lbl" value="Label" alt="optionnel" />
        </div>

        <!-- Label intégré dans input (prefix/suffix) -->
        <label class="input">
            <span class="label">https://</span>
            <input type="text" placeholder="URL" />
        </label>
        <label class="input">
            <input type="text" placeholder="domain name" />
            <span class="label">.com</span>
        </label>
        <label class="select w-56">
            <span class="label">Type</span>
            <select>
                <option>Personal</option>
                <option>Business</option>
            </select>
        </label>
        <label class="input w-56">
            <span class="label">Publish date</span>
            <input type="date" />
        </label>

        <!-- Floating label -->
        <div class="grid md:grid-cols-5 gap-3">
            <x-daisy::ui.advanced.label floating span="Extra Small">
                <input type="text" placeholder="Extra Small" class="input input-xs" />
            </x-daisy::ui.advanced.label>
            <x-daisy::ui.advanced.label floating span="Small">
                <input type="text" placeholder="Small" class="input input-sm" />
            </x-daisy::ui.advanced.label>
            <x-daisy::ui.advanced.label floating span="Medium">
                <input type="text" placeholder="Medium" class="input input-md" />
            </x-daisy::ui.advanced.label>
            <x-daisy::ui.advanced.label floating span="Large">
                <input type="text" placeholder="Large" class="input input-lg" />
            </x-daisy::ui.advanced.label>
            <x-daisy::ui.advanced.label floating span="Extra Large">
                <input type="text" placeholder="Extra Large" class="input input-xl" />
            </x-daisy::ui.advanced.label>
        </div>
    </div>
</section>



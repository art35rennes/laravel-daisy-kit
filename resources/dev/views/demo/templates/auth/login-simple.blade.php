<x-daisy::templates.auth.login-simple>
    <x-slot:logo>
        <div class="flex items-center gap-2">
            <span class="font-semibold">DaisyUI</span>
        </div>
    </x-slot:logo>

    <x-slot:socialLogin>
        <x-daisy::ui.advanced.login-button provider="google" href="#" class="flex-1" />
        <x-daisy::ui.advanced.login-button provider="github" href="#" class="flex-1" />
    </x-slot:socialLogin>
</x-daisy::templates.auth.login-simple>



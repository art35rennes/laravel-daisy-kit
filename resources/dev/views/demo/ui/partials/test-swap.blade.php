<!-- Swap -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Swap</h2>
    <div class="flex flex-wrap items-center gap-6">
        <!-- Icônes avec rotation -->
        <x-daisy::ui.advanced.swap :rotate="true">
            <x-slot:on>
                <x-bi-x class="w-6 h-6" />
            </x-slot:on>
            <x-slot:off>
                <x-bi-heart class="w-6 h-6" />
            </x-slot:off>
        </x-daisy::ui.advanced.swap>

        <!-- Texte ON/OFF -->
        <x-daisy::ui.advanced.swap>
            <x-slot:on>ON</x-slot:on>
            <x-slot:off>OFF</x-slot:off>
        </x-daisy::ui.advanced.swap>

        <!-- Flip avec emoji -->
        <x-daisy::ui.advanced.swap :flip="true" class="text-4xl">
            <x-slot:on>😈</x-slot:on>
            <x-slot:off>😇</x-slot:off>
        </x-daisy::ui.advanced.swap>

        <!-- Activation via classe (pas de checkbox) -->
        <x-daisy::ui.advanced.swap :active="true" :useInput="false">
            <x-slot:on>🥳</x-slot:on>
            <x-slot:off>😭</x-slot:off>
        </x-daisy::ui.advanced.swap>
    </div>
</section>



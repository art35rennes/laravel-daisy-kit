<!-- Swap -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Swap</h2>
    <div class="flex flex-wrap items-center gap-6">
        <!-- Icônes avec rotation -->
        <x-daisy::ui.swap :rotate="true">
            <x-slot:on>
                <x-heroicon-o-x-mark class="w-6 h-6" />
            </x-slot:on>
            <x-slot:off>
                <x-heroicon-o-heart class="w-6 h-6" />
            </x-slot:off>
        </x-daisy::ui.swap>

        <!-- Texte ON/OFF -->
        <x-daisy::ui.swap>
            <x-slot:on>ON</x-slot:on>
            <x-slot:off>OFF</x-slot:off>
        </x-daisy::ui.swap>

        <!-- Flip avec emoji -->
        <x-daisy::ui.swap :flip="true" class="text-4xl">
            <x-slot:on>😈</x-slot:on>
            <x-slot:off>😇</x-slot:off>
        </x-daisy::ui.swap>

        <!-- Activation via classe (pas de checkbox) -->
        <x-daisy::ui.swap :active="true" :useInput="false">
            <x-slot:on>🥳</x-slot:on>
            <x-slot:off>😭</x-slot:off>
        </x-daisy::ui.swap>
    </div>
</section>



<!-- Stat -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Stat</h2>
    <div class="space-y-4">
        <!-- Simple -->
        <div class="stats shadow">
            <x-daisy::ui.stat title="Total Page Views" value="89,400" desc="21% more than last month" />
        </div>

        <!-- Avec icônes / image -->
        <div class="stats shadow">
            <x-daisy::ui.stat>
                <x-slot:figure>
                    <x-bi-heart class="inline-block h-8 w-8 stroke-current text-primary" />
                </x-slot:figure>
                <x-slot:title>Total Likes</x-slot:title>
                <x-slot:value><span class="text-primary">25.6K</span></x-slot:value>
                <x-slot:desc>21% more than last month</x-slot:desc>
            </x-daisy::ui.stat>
            <x-daisy::ui.stat>
                <x-slot:figure>
                    <x-bi-lightning-fill class="inline-block h-8 w-8 stroke-current text-secondary" />
                </x-slot:figure>
                <x-slot:title>Page Views</x-slot:title>
                <x-slot:value><span class="text-secondary">2.6M</span></x-slot:value>
                <x-slot:desc>21% more than last month</x-slot:desc>
            </x-daisy::ui.stat>
            <x-daisy::ui.stat>
                <x-slot:figure>
                    <div class="avatar avatar-online">
                        <div class="w-16 rounded-full">
                            <img src="{{ Vite::asset('resources/dev/img/people/dummy-100x100-Rosa.jpg') }}" />
                        </div>
                    </div>
                </x-slot:figure>
                <x-slot:value>86%</x-slot:value>
                <x-slot:title>Tasks done</x-slot:title>
                <x-slot:desc><span class="text-secondary">31 tasks remaining</span></x-slot:desc>
            </x-daisy::ui.stat>
        </div>

        <!-- Responsive vertical/horizontal -->
        <div class="stats stats-vertical lg:stats-horizontal shadow">
            <x-daisy::ui.stat title="Downloads" value="31K" desc="Jan 1st - Feb 1st" />
            <x-daisy::ui.stat title="New Users" value="4,200" desc="↗︎ 400 (22%)" />
            <x-daisy::ui.stat title="New Registers" value="1,200" desc="↘︎ 90 (14%)" />
        </div>

        <!-- Avec boutons (actions) -->
        <div class="stats bg-base-100 border-base-300 border">
            <x-daisy::ui.stat title="Account balance" value="$89,400">
                <x-slot:actions>
                    <button class="btn btn-xs btn-success">Add funds</button>
                </x-slot:actions>
            </x-daisy::ui.stat>
            <x-daisy::ui.stat title="Current balance" value="$89,400">
                <x-slot:actions>
                    <button class="btn btn-xs">Withdrawal</button>
                    <button class="btn btn-xs">Deposit</button>
                </x-slot:actions>
            </x-daisy::ui.stat>
        </div>
    </div>
</section>



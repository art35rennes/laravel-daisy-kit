<!-- Indicator -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Indicator</h2>
    <div class="flex flex-wrap items-center gap-8">
        <!-- Status indicator -->
        <x-daisy::ui.indicator type="status" statusColor="success">
            <div class="bg-base-300 grid h-32 w-32 place-items-center rounded-box">content</div>
        </x-daisy::ui.indicator>

        <!-- Badge indicator -->
        <x-daisy::ui.indicator label="New" color="primary">
            <div class="bg-base-300 grid h-32 w-32 place-items-center rounded-box">content</div>
        </x-daisy::ui.indicator>

        <!-- Sur bouton -->
        <x-daisy::ui.indicator label="12" color="secondary">
            <button class="btn">inbox</button>
        </x-daisy::ui.indicator>

        <!-- Avatar avec badge (image fiable) -->
        <div class="avatar indicator">
            <span class="indicator-item indicator-bottom indicator-end badge badge-secondary">Justice</span>
            <div class="h-20 w-20 rounded-lg">
                <img alt="User avatar" src="/img/people/dummy-100x100-Rosa.jpg" />
            </div>
        </div>

        <!-- Positionnements -->
        <div class="indicator">
            <span class="indicator-item indicator-top indicator-start badge">↖︎</span>
            <span class="indicator-item indicator-top indicator-center badge">↑</span>
            <span class="indicator-item indicator-top indicator-end badge">↗︎</span>
            <span class="indicator-item indicator-middle indicator-start badge">←</span>
            <span class="indicator-item indicator-middle indicator-center badge">●</span>
            <span class="indicator-item indicator-middle indicator-end badge">→</span>
            <span class="indicator-item indicator-bottom indicator-start badge">↙︎</span>
            <span class="indicator-item indicator-bottom indicator-center badge">↓</span>
            <span class="indicator-item indicator-bottom indicator-end badge">↘︎</span>
            <div class="bg-base-300 grid h-32 w-60 place-items-center rounded-box">Box</div>
        </div>
    </div>
</section>



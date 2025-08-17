<!-- Countdown -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Countdown</h2>
    <div class="space-y-3">
        <x-daisy::ui.countdown :values="['days' => 15, 'hours' => 10, 'min' => 24, 'sec' => 39]" size="lg" />
        <x-daisy::ui.countdown :values="['h' => 10, 'm' => 24, 's' => 59]" mode="inline-colon" size="md" />
    </div>
</section>



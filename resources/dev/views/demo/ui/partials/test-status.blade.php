<!-- Status -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Status</h2>
    <div class="space-y-3">
        <div class="flex items-center gap-6">
            <div class="flex items-center gap-2">
                <x-daisy::ui.data-display.status color="success" label="online" />
                <span>En ligne</span>
            </div>
            <div class="flex items-center gap-2">
                <x-daisy::ui.data-display.status color="warning" label="busy" />
                <span>Occupé</span>
            </div>
            <div class="flex items-center gap-2">
                <x-daisy::ui.data-display.status color="error" label="offline" />
                <span>Hors ligne</span>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <x-daisy::ui.data-display.status size="xs" />
            <x-daisy::ui.data-display.status size="sm" />
            <x-daisy::ui.data-display.status size="md" />
            <x-daisy::ui.data-display.status size="lg" />
            <x-daisy::ui.data-display.status size="xl" />
        </div>
        <div class="flex items-center gap-6">
            <div class="inline-grid *:[grid-area:1/1]">
                <x-daisy::ui.data-display.status color="error" class="animate-ping" as="div" />
                <x-daisy::ui.data-display.status color="error" as="div" />
            </div>
            <span>Server is down</span>
        </div>
        <div class="flex items-center gap-3">
            <x-daisy::ui.data-display.status color="info" class="animate-bounce" />
            <span>Unread messages</span>
        </div>
    </div>
</section>



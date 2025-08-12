<!-- Loading -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Loading</h2>
    <div class="flex flex-wrap items-center gap-4">
        <!-- Spinner: xs..xl -->
        <x-daisy::ui.loading shape="spinner" size="xs" />
        <x-daisy::ui.loading shape="spinner" size="sm" />
        <x-daisy::ui.loading shape="spinner" size="md" />
        <x-daisy::ui.loading shape="spinner" size="lg" />
        <x-daisy::ui.loading shape="spinner" size="xl" />

        <!-- Dots/Ring/Ball/Bars/Infinity avec couleurs -->
        <x-daisy::ui.loading shape="ring" size="sm" color="primary" />
        <x-daisy::ui.loading shape="dots" size="sm" color="info" />
        <x-daisy::ui.loading shape="ball" size="md" color="success" />
        <x-daisy::ui.loading shape="bars" size="lg" color="warning" />
        <x-daisy::ui.loading shape="infinity" size="lg" color="error" />
    </div>
</section>



<!-- Collapse -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Collapse</h2>
    <div class="space-y-4">
        <div class="grid md:grid-cols-2 gap-6">
            <!-- Checkbox method -->
            <x-daisy::ui.collapse title="Checkbox (arrow)" :open="true" :bordered="true" :bg="true">Contenu du collapse</x-daisy::ui.collapse>
            <x-daisy::ui.collapse :arrow="false" title="Checkbox (plus)" :bordered="true" :bg="true">Contenu avec +</x-daisy::ui.collapse>
        </div>

        <!-- Focus method -->
        <div class="grid md:grid-cols-2 gap-6">
            <x-daisy::ui.collapse title="Focus (arrow)" method="focus" :bordered="true" :bg="true" />
            <x-daisy::ui.collapse title="Focus (plus)" method="focus" :arrow="false" :bordered="true" :bg="true" />
        </div>

        <!-- Details method -->
        <div class="grid md:grid-cols-2 gap-6">
            <x-daisy::ui.collapse title="Details (arrow)" method="details" :bordered="true" :bg="true" />
            <x-daisy::ui.collapse title="Details (plus)" method="details" :arrow="false" :bordered="true" :bg="true" />
        </div>

        <!-- Forced state (checkbox only) -->
        <div class="grid md:grid-cols-2 gap-6">
            <x-daisy::ui.collapse title="Forced open" :bordered="true" :bg="true" force="open" />
            <x-daisy::ui.collapse title="Forced close" :bordered="true" :bg="true" force="close" />
        </div>
    </div>
</section>



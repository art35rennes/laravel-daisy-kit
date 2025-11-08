<!-- Avatars -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Avatars</h2>
    <div class="space-y-3">
        <div class="flex flex-wrap items-center gap-4">
            <x-daisy::ui.data-display.avatar src="/img/people/dummy-100x100-Rosa.jpg" alt="Avatar" />
            <x-daisy::ui.data-display.avatar placeholder="JS" />
            <x-daisy::ui.data-display.avatar size="sm" placeholder="SM" />
            <x-daisy::ui.data-display.avatar size="lg" placeholder="LG" />
            <x-daisy::ui.data-display.avatar size="xl" placeholder="XL" />
            <x-daisy::ui.data-display.avatar size="xxl" placeholder="XXL" />
        </div>
        <div class="flex flex-wrap items-center gap-4">
            <x-daisy::ui.data-display.avatar rounded="md" placeholder="MD" />
            <x-daisy::ui.data-display.avatar rounded="xl" placeholder="XL" />
            <x-daisy::ui.data-display.avatar rounded="none" placeholder="--" />
        </div>
        <div class="flex flex-wrap items-center gap-4">
            <x-daisy::ui.data-display.avatar src="/img/food/dummy-100x100-Kiwi.jpg" status="online" />
            <x-daisy::ui.data-display.avatar src="/img/object/dummy-100x100-Commodore64.jpg" status="offline" />
            <div class="avatar-group -space-x-4 rtl:space-x-reverse">
                <x-daisy::ui.data-display.avatar src="/img/divers/dummy-100x100-Stripes.jpg" />
                <x-daisy::ui.data-display.avatar src="/img/business/dummy-100x100-Dollar.jpg" />
                <x-daisy::ui.data-display.avatar src="/img/people/dummy-100x100-Rosa.jpg" />
                <x-daisy::ui.data-display.avatar placeholder="+99" />
            </div>
        </div>
    </div>
</section>



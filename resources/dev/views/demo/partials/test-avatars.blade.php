<!-- Avatars -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Avatars</h2>
    <div class="space-y-3">
        <div class="flex flex-wrap items-center gap-4">
            <x-daisy::ui.avatar src="{{ Vite::asset('resources/dev/img/people/dummy-100x100-Rosa.jpg') }}" alt="Avatar" />
            <x-daisy::ui.avatar placeholder="JS" />
            <x-daisy::ui.avatar size="sm" placeholder="SM" />
            <x-daisy::ui.avatar size="lg" placeholder="LG" />
            <x-daisy::ui.avatar size="xl" placeholder="XL" />
            <x-daisy::ui.avatar size="xxl" placeholder="XXL" />
        </div>
        <div class="flex flex-wrap items-center gap-4">
            <x-daisy::ui.avatar rounded="md" placeholder="MD" />
            <x-daisy::ui.avatar rounded="xl" placeholder="XL" />
            <x-daisy::ui.avatar rounded="none" placeholder="--" />
        </div>
        <div class="flex flex-wrap items-center gap-4">
            <x-daisy::ui.avatar src="{{ Vite::asset('resources/dev/img/food/dummy-100x100-Kiwi.jpg') }}" status="online" />
            <x-daisy::ui.avatar src="{{ Vite::asset('resources/dev/img/object/dummy-100x100-Commodore64.jpg') }}" status="offline" />
            <div class="avatar-group -space-x-4 rtl:space-x-reverse">
                <x-daisy::ui.avatar src="{{ Vite::asset('resources/dev/img/divers/dummy-100x100-Stripes.jpg') }}" />
                <x-daisy::ui.avatar src="{{ Vite::asset('resources/dev/img/business/dummy-100x100-Dollar.jpg') }}" />
                <x-daisy::ui.avatar src="{{ Vite::asset('resources/dev/img/people/dummy-100x100-Rosa.jpg') }}" />
                <x-daisy::ui.avatar placeholder="+99" />
            </div>
        </div>
    </div>
</section>



<!-- Avatars -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Avatars</h2>
    <div class="space-y-3">
        <div class="flex flex-wrap items-center gap-4">
            <x-daisy::ui.avatar src="https://i.pravatar.cc/100?img=3" alt="Avatar" />
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
            <x-daisy::ui.avatar src="https://i.pravatar.cc/100?img=9" status="online" />
            <x-daisy::ui.avatar src="https://i.pravatar.cc/100?img=10" status="offline" />
            <div class="avatar-group -space-x-4 rtl:space-x-reverse">
                <x-daisy::ui.avatar src="https://i.pravatar.cc/100?img=11" />
                <x-daisy::ui.avatar src="https://i.pravatar.cc/100?img=12" />
                <x-daisy::ui.avatar src="https://i.pravatar.cc/100?img=13" />
                <x-daisy::ui.avatar placeholder="+99" />
            </div>
        </div>
    </div>
</section>



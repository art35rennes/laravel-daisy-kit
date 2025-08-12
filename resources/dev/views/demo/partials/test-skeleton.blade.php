<!-- Skeleton -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Skeleton</h2>
    <div class="flex items-center gap-4">
        <x-daisy::ui.skeleton width="w-24" height="h-6" />
        <x-daisy::ui.skeleton width="w-36" height="h-6" />
        <x-daisy::ui.skeleton width="w-16" height="h-16" rounded="full" />
    </div>

    <!-- Circle with content -->
    <div class="flex w-52 flex-col gap-4">
        <div class="flex items-center gap-4">
            <x-daisy::ui.skeleton width="w-16" height="h-16" class="shrink-0" rounded="full" />
            <div class="flex flex-col gap-4 w-full">
                <x-daisy::ui.skeleton width="w-20" height="h-4" />
                <x-daisy::ui.skeleton width="w-28" height="h-4" />
            </div>
        </div>
        <x-daisy::ui.skeleton height="h-32" class="w-full" />
    </div>

    <!-- Rectangle with content -->
    <div class="flex w-52 flex-col gap-4">
        <x-daisy::ui.skeleton height="h-32" class="w-full" />
        <x-daisy::ui.skeleton width="w-28" height="h-4" />
        <x-daisy::ui.skeleton height="h-4" class="w-full" />
        <x-daisy::ui.skeleton height="h-4" class="w-full" />
    </div>
</section>



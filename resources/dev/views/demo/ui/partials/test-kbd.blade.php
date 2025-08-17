<!-- Kbd -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Kbd</h2>
    <div class="space-y-3">
        <div class="flex items-center gap-2">
            <x-daisy::ui.kbd size="sm">⌘</x-daisy::ui.kbd>
            <x-daisy::ui.kbd>K</x-daisy::ui.kbd>
            <x-daisy::ui.kbd size="lg">F</x-daisy::ui.kbd>
            <x-daisy::ui.kbd size="xl">ESC</x-daisy::ui.kbd>
        </div>
        <p>Press <x-daisy::ui.kbd size="sm">F</x-daisy::ui.kbd> to pay respects.</p>
        <div>
            <x-daisy::ui.kbd :keys="['ctrl','shift','del']" />
        </div>
        <div class="my-1 flex w-full justify-center gap-1">
            @foreach(str_split('qwertyuiop') as $k)
                <x-daisy::ui.kbd>{{ $k }}</x-daisy::ui.kbd>
            @endforeach
        </div>
        <div class="my-1 flex w-full justify-center gap-1">
            @foreach(str_split('asdfghjkl') as $k)
                <x-daisy::ui.kbd>{{ $k }}</x-daisy::ui.kbd>
            @endforeach
        </div>
        <div class="my-1 flex w-full justify-center gap-1">
            @foreach(str_split('zxcvbnm/') as $k)
                <x-daisy::ui.kbd>{{ $k }}</x-daisy::ui.kbd>
            @endforeach
        </div>
    </div>
</section>



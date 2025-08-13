<x-daisy::layout.navbar-layout title="Navbar Template">
    <x-slot:brand>
        <a class="btn btn-ghost text-xl">DaisyKit</a>
    </x-slot:brand>
    <x-slot:nav>
        <x-daisy::ui.menu :vertical="false" class="px-1">
            <li><a>Overview</a></li>
            <li>
                <details>
                    <summary>Docs</summary>
                    <ul class="p-2">
                        <li><a>Guide</a></li>
                        <li><a>API</a></li>
                    </ul>
                </details>
            </li>
            <li><a>Blog</a></li>
        </x-daisy::ui.menu>
    </x-slot:nav>
    <x-slot:actions>
        <label class="input input-bordered flex items-center gap-2">
            <x-bi-search class="w-5 h-5" />
            <input type="text" class="grow" placeholder="{{ __('daisy::layout.search') }}" />
        </label>
        <button class="btn btn-ghost btn-circle">
            <x-bi-bell class="h-6 w-6" />
        </button>
        <a class="btn" href="{{ route('templates.index') }}">Templates</a>
    </x-slot:actions>

    <div class="grid gap-4 md:grid-cols-2">
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <div class="card-title">Card</div>
                <p>Contenu principal.</p>
            </div>
        </div>
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <div class="card-title">Card</div>
                <p>Contenu principal.</p>
            </div>
        </div>
    </div>
</x-daisy::layout.navbar-layout>



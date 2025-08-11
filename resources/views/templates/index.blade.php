<x-daisy::layout.app title="Templates" :container="true">
    <div class="prose">
        <h1>Templates</h1>
        <p>Exemples de layouts avancés.</p>
        <ul>
            <li><a class="link" href="{{ route('layouts.navbar') }}">Navbar</a></li>
            <li><a class="link" href="{{ route('layouts.sidebar') }}">Sidebar</a></li>
            <li><a class="link" href="{{ route('layouts.navbar-sidebar') }}">Navbar + Sidebar</a></li>
            <li><a class="link" href="{{ route('demo') }}">Tous les composants (Demo)</a></li>
        </ul>
    </div>
</x-daisy::layout.app>



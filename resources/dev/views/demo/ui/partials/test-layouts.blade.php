{{-- Layouts de navigation --}}
@php
    $prefix = config('daisy-kit.docs.prefix', 'docs');
@endphp
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Layouts de navigation</h2>
    <p class="text-sm text-base-content/70">Layouts prêts à l'emploi pour structurer vos applications.</p>
    
    <div class="grid gap-6 md:grid-cols-3">
        <!-- Layout Navbar -->
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                <h3 class="card-title text-base">Navbar Layout</h3>
                <p class="text-sm">Barre de navigation en haut de page</p>
                <div class="card-actions justify-end">
                    @if (Route::has('daisy.docs.templates'))
                        <a href="{{ route('daisy.docs.templates') }}#layout" class="btn btn-primary btn-sm">Voir</a>
                    @else
                        <a href="/{{ $prefix }}/templates#layout" class="btn btn-primary btn-sm">Voir</a>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Layout Sidebar -->
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                <h3 class="card-title text-base">Sidebar Layout</h3>
                <p class="text-sm">Barre latérale de navigation</p>
                <div class="card-actions justify-end">
                    @if (Route::has('daisy.docs.templates'))
                        <a href="{{ route('daisy.docs.templates') }}#layout" class="btn btn-primary btn-sm">Voir</a>
                    @else
                        <a href="/{{ $prefix }}/templates#layout" class="btn btn-primary btn-sm">Voir</a>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Layout Navbar + Sidebar -->
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                <h3 class="card-title text-base">Navbar + Sidebar</h3>
                <p class="text-sm">Combinaison navbar et sidebar</p>
                <div class="card-actions justify-end">
                    @if (Route::has('daisy.docs.templates'))
                        <a href="{{ route('daisy.docs.templates') }}#layout" class="btn btn-primary btn-sm">Voir</a>
                    @else
                        <a href="/{{ $prefix }}/templates#layout" class="btn btn-primary btn-sm">Voir</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="bg-base-100 p-4 rounded-lg">
        <h4 class="font-medium mb-2">Exemple d'utilisation</h4>
        <pre class="text-xs bg-base-300 p-3 rounded overflow-x-auto"><code>&lt;x-daisy::layout.sidebar-layout 
    title="Mon Dashboard" 
    brand="MonApp" 
    :sections="[
        ['label' => 'Navigation', 'items' => [
            ['label' => 'Dashboard', 'href' => '#', 'icon' => 'speedometer2'],
            ['label' => 'Utilisateurs', 'href' => '#', 'icon' => 'people'],
        ]]
    ]"&gt;
    {{-- Contenu --}}
&lt;/x-daisy::layout.sidebar-layout&gt;</code></pre>
    </div>
</section>

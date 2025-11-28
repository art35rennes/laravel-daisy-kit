<!-- Copyable -->
<section class="space-y-6 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Copyable (Greffon)</h2>
    <p class="text-sm text-base-content/70">Greffon JavaScript qui s'applique automatiquement aux éléments avec la classe <code>copyable</code>. Le composant Blade est un helper optionnel qui ajoute simplement la classe et les attributs data-*.</p>
    
    <!-- Utilisation directe du greffon (sans composant Blade) -->
    <div class="space-y-4">
        <h3 class="text-md font-medium">Utilisation directe du greffon (sans composant Blade)</h3>
        <p class="text-sm text-base-content/70">Vous pouvez utiliser directement la classe <code>copyable</code> sur n'importe quel élément HTML :</p>
        <div class="space-y-2">
            <span class="copyable">Texte simple avec classe copyable</span><br>
            <code class="copyable copyable-underline">code.copyable { ... }</code><br>
            <div class="copyable" data-copy-value="valeur-copiee">Texte affiché différent de la valeur</div>
            <p class="copyable" data-copy-html="true">
                <strong>Paragraphe</strong> avec <em>HTML</em> à copier
            </p>
        </div>
    </div>
    
    <!-- Exemple basique -->
    <div class="space-y-4">
        <h3 class="text-md font-medium">Exemple basique</h3>
        <x-daisy::ui.utilities.copyable>
            Cliquez pour copier ce texte
        </x-daisy::ui.utilities.copyable>
    </div>
    
    <!-- Avec ligne pointillée -->
    <div class="space-y-4">
        <h3 class="text-md font-medium">Avec ligne pointillée (underline)</h3>
        <x-daisy::ui.utilities.copyable underline>
            Texte avec ligne pointillée en dessous
        </x-daisy::ui.utilities.copyable>
    </div>
    
    <!-- Avec valeur explicite -->
    <div class="space-y-4">
        <h3 class="text-md font-medium">Avec valeur explicite (value)</h3>
        <x-daisy::ui.utilities.copyable value="Cette valeur sera copiée, pas le texte affiché">
            Texte affiché à l'écran
        </x-daisy::ui.utilities.copyable>
    </div>
    
    <!-- Mode option : display vs value (comme option HTML) -->
    <div class="space-y-4">
        <h3 class="text-md font-medium">Mode option : display vs value</h3>
        <p class="text-sm text-base-content/70">Permet de distinguer le texte affiché de la valeur copiée, comme dans un &lt;option value="..."&gt;Texte&lt;/option&gt;</p>
        <div class="space-y-2">
            <x-daisy::ui.utilities.copyable 
                value="user@example.com" 
                display="👤 Contact utilisateur"
                underline
            />
            
            <x-daisy::ui.utilities.copyable 
                value="https://example.com/api/endpoint" 
                display="🔗 Endpoint API"
                underline
            />
            
            <x-daisy::ui.utilities.copyable 
                value="SELECT * FROM users WHERE active = 1" 
                display="💾 Requête SQL"
                underline
            />
        </div>
    </div>
    
    <!-- Copie HTML -->
    <div class="space-y-4">
        <h3 class="text-md font-medium">Copie HTML (pour éléments complexes)</h3>
        <x-daisy::ui.utilities.copyable copyHtml>
            <div class="bg-base-100 p-4 rounded-box card-border">
                <h4 class="font-bold text-lg mb-2">Exemple de template</h4>
                <p class="text-sm text-base-content/70">Ce contenu HTML sera copié avec sa structure.</p>
                <code class="block mt-2 p-2 bg-base-200 rounded text-xs">&lt;div&gt;Code HTML&lt;/div&gt;</code>
            </div>
        </x-daisy::ui.utilities.copyable>
    </div>
    
    <!-- Position de l'icône -->
    <div class="space-y-4">
        <h3 class="text-md font-medium">Position de l'icône</h3>
        <div class="space-y-2">
            <x-daisy::ui.utilities.copyable iconPosition="left">
                Icône à gauche
            </x-daisy::ui.utilities.copyable>
            
            <x-daisy::ui.utilities.copyable iconPosition="right">
                Icône à droite (par défaut)
            </x-daisy::ui.utilities.copyable>
            
            <x-daisy::ui.utilities.copyable iconPosition="inline">
                Icône inline dans le texte
            </x-daisy::ui.utilities.copyable>
        </div>
    </div>
    
    <!-- Taille de l'icône -->
    <div class="space-y-4">
        <h3 class="text-md font-medium">Taille de l'icône</h3>
        <div class="space-y-2">
            <x-daisy::ui.utilities.copyable iconSize="xs">
                Très petite (xs)
            </x-daisy::ui.utilities.copyable>
            
            <x-daisy::ui.utilities.copyable iconSize="sm">
                Petite (sm)
            </x-daisy::ui.utilities.copyable>
            
            <x-daisy::ui.utilities.copyable iconSize="md">
                Moyenne (md)
            </x-daisy::ui.utilities.copyable>
            
            <x-daisy::ui.utilities.copyable iconSize="lg">
                Grande (lg)
            </x-daisy::ui.utilities.copyable>
        </div>
    </div>
    
    <!-- Exemple avec code -->
    <div class="space-y-4">
        <h3 class="text-md font-medium">Exemple avec code (usage typique)</h3>
        <x-daisy::ui.utilities.copyable underline value='<x-daisy::ui.utilities.copyable>Texte</x-daisy::ui.utilities.copyable>'>
            <code class="text-sm">&lt;x-daisy::ui.utilities.copyable&gt;Texte&lt;/x-daisy::ui.utilities.copyable&gt;</code>
        </x-daisy::ui.utilities.copyable>
    </div>
    
    <!-- Message personnalisé -->
    <div class="space-y-4">
        <h3 class="text-md font-medium">Message de succès personnalisé</h3>
        <x-daisy::ui.utilities.copyable successMessage="✅ Copié avec succès!">
            Cliquez pour copier
        </x-daisy::ui.utilities.copyable>
    </div>
</section>


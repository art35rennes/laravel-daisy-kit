<div class="space-y-4">
    <h3 class="font-medium">WYSIWYG (Trix)</h3>

    <div class="grid md:grid-cols-2 gap-6">
        <div class="space-y-2 p-4 border border-base-300 rounded-box bg-base-100">
            <div class="label"><span class="label-text">Éditeur basique</span></div>
            <x-daisy::ui.wysiwyg name="content_basic" placeholder="Saisissez votre texte riche..." height="16rem" />
            <div class="text-xs opacity-70">Éditeur WYSIWYG standard avec toutes les fonctionnalités de base de Trix.</div>
        </div>

        <div class="space-y-2 p-4 border border-base-300 rounded-box bg-base-100">
            <div class="label"><span class="label-text">Éditeur désactivé</span></div>
            <x-daisy::ui.wysiwyg value="<p>Contenu en lecture seule</p>" :toolbar="false" disabled height="12rem" />
            <div class="text-xs opacity-70">Mode lecture seule avec barre d'outils masquée.</div>
        </div>
    </div>


    <div class="space-y-2 p-4 border border-base-300 rounded-box bg-base-100">
        <div class="label"><span class="label-text">Avec valeur initiale</span></div>
        <x-daisy::ui.wysiwyg name="content_init" :value="'<h3>Titre</h3><p>Lorem ipsum <strong>dolor</strong> sit amet.</p>'" height="14rem" />
        <div class="text-xs opacity-70">Éditeur pré-rempli avec du contenu HTML existant.</div>
    </div>


    <div class="space-y-2 p-4 border border-base-300 rounded-box bg-base-100">
        <div class="label"><span class="label-text">Pièces jointes activées</span></div>
        <x-daisy::ui.wysiwyg name="content_attach" placeholder="Vous pouvez déposer des images/fichiers ici..." height="14rem" attachments />
        <div class="text-xs opacity-70">Glissez-déposez des fichiers directement dans l'éditeur. La gestion serveur de l'upload est à implémenter (exemples ci-dessous).</div>
    </div>


    @php
        $codeWysiwygBlade = <<<'BLADE'
{{-- Éditeur WYSIWYG basique --}}
<x-daisy::ui.wysiwyg 
    name="content" 
    placeholder="Saisissez votre texte riche..." 
    height="16rem" />

{{-- Éditeur avec valeur initiale --}}
<x-daisy::ui.wysiwyg 
    name="article_body" 
    :value="$article->body ?? ''"
    placeholder="Contenu de l'article..." />

{{-- Éditeur avec pièces jointes activées --}}
<x-daisy::ui.wysiwyg 
    name="content_attach" 
    attachments 
    data-upload-url="{{ route('wysiwyg.upload') }}"
    placeholder="Vous pouvez déposer des images ici..."
    height="20rem" />

{{-- Éditeur en lecture seule --}}
<x-daisy::ui.wysiwyg 
    :value="$content"
    :toolbar="false"
    disabled
    height="12rem" />

    
BLADE;

        $codeWysiwygJs = <<<'JAVASCRIPT'
// Gestionnaire d'upload pour les pièces jointes Trix
document.addEventListener('trix-attachment-add', async (event) => {
    const attachment = event.attachment;
    
    // Vérifier si c'est un fichier local
    if (!attachment.file) return;
    
    // Récupérer l'URL d'upload
    const editor = event.target;
    const wrapper = editor.closest('.trix-wrapper');
    const uploadUrl = wrapper.getAttribute('data-upload-url') || '/wysiwyg/upload';
    
    // Préparer les données
    const formData = new FormData();
    formData.append('file', attachment.file);
    
    try {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', uploadUrl, true);
        
        // Token CSRF Laravel
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (csrfToken) {
            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
        }
        
        // Progression
        xhr.upload.addEventListener('progress', (e) => {
            if (e.lengthComputable) {
                const progress = Math.round((e.loaded / e.total) * 100);
                attachment.setUploadProgress(progress);
            }
        });
        
        // Réponse
        xhr.onload = () => {
            if (xhr.status >= 200 && xhr.status < 300) {
                const response = JSON.parse(xhr.responseText);
                attachment.setAttributes({
                    url: response.url,
                    href: response.url
                });
            } else {
                attachment.remove();
            }
        };
        
        xhr.onerror = () => attachment.remove();
        xhr.send(formData);
        
    } catch (error) {
        attachment.remove();
    }
});


JAVASCRIPT;

        $codeWysiwygPhp = <<<'PHP'
<?php
// routes/web.php
Route::post('/wysiwyg/upload', function (Request $request) {
    $file = $request->file('file');
    
    // Stockage simple
    $path = $file->store('wysiwyg', 'public');
    
    // Retourner l'URL pour Trix
    return response()->json([
        'url' => Storage::disk('public')->url($path)
    ]);
});

// Contrôleur plus complet (optionnel)
class WysiwygController extends Controller
{
    public function upload(Request $request)
    {
        $file = $request->file('file');
        
        // Stockage organisé par date
        $path = $file->store('wysiwyg/' . date('Y/m'), 'public');
        
        return response()->json([
            'url' => Storage::disk('public')->url($path),
            'filename' => $file->getClientOriginalName()
        ]);
    }
}

// Important : créer le lien symbolique
// php artisan storage:link


PHP;
    @endphp

    <x-daisy::ui.collapse title="Exemples de code · Usage Blade" bordered bg>
        <x-daisy::ui.code-editor language="html" :value="$codeWysiwygBlade" readonly height="280px" />
    </x-daisy::ui.collapse>


    <x-daisy::ui.collapse title="Exemples de code · JS Upload (client)" bordered bg>
        <x-daisy::ui.code-editor language="javascript" :value="$codeWysiwygJs" readonly height="400px" />
    </x-daisy::ui.collapse>


    <x-daisy::ui.collapse title="Exemples de code · Laravel (route + controller)" bordered bg>
        <x-daisy::ui.code-editor language="php" :value="$codeWysiwygPhp" readonly height="350px" />
    </x-daisy::ui.collapse>


</div>

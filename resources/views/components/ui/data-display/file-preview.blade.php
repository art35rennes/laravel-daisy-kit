@props([
    'url' => null,
    'name' => null,
    'type' => null, // image|video|audio|pdf|document|other
    'fileSize' => null, // Format: "1.5 MB" ou nombre en bytes
    'thumbnail' => null, // URL de la miniature pour les non-images
    'downloadable' => true,
    'size' => 'md', // xs|sm|md|lg|xl
    'openMode' => null, // null|blank|modal - null = auto (modal pour images, blank pour autres)
])

@php
    // Détection automatique du type si non fourni
    if (!$type && $url) {
        $extension = strtolower(pathinfo($url, PATHINFO_EXTENSION));
        $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'];
        $videoExts = ['mp4', 'webm', 'ogg', 'mov', 'avi'];
        $audioExts = ['mp3', 'wav', 'ogg', 'm4a', 'aac'];
        $pdfExts = ['pdf'];
        $docExts = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'rtf'];
        
        if (in_array($extension, $imageExts)) {
            $type = 'image';
        } elseif (in_array($extension, $videoExts)) {
            $type = 'video';
        } elseif (in_array($extension, $audioExts)) {
            $type = 'audio';
        } elseif (in_array($extension, $pdfExts)) {
            $type = 'pdf';
        } elseif (in_array($extension, $docExts)) {
            $type = 'document';
        } else {
            $type = 'other';
        }
    }
    
    $type = $type ?? 'other';
    
    // Détermination automatique du mode d'ouverture
    // Si openMode n'est pas spécifié : modal pour les images, blank pour les autres
    if ($openMode === null) {
        $openMode = ($type === 'image') ? 'modal' : 'blank';
    }
    
    // Tailles pour les différents types
    $sizeMap = [
        'xs' => ['container' => 'max-w-32', 'image' => 'max-h-24', 'icon' => 'w-6 h-6'],
        'sm' => ['container' => 'max-w-48', 'image' => 'max-h-32', 'icon' => 'w-8 h-8'],
        'md' => ['container' => 'max-w-64', 'image' => 'max-h-48', 'icon' => 'w-10 h-10'],
        'lg' => ['container' => 'max-w-96', 'image' => 'max-h-64', 'icon' => 'w-12 h-12'],
        'xl' => ['container' => 'max-w-[32rem]', 'image' => 'max-h-96', 'icon' => 'w-16 h-16'],
    ];
    
    $sizes = $sizeMap[$size] ?? $sizeMap['md'];
    
    // Icônes par type
    $icons = [
        'image' => 'bi-image',
        'video' => 'bi-play-circle',
        'audio' => 'bi-music-note-beamed',
        'pdf' => 'bi-file-pdf',
        'document' => 'bi-file-text',
        'other' => 'bi-file-earmark',
    ];
    
    $icon = $icons[$type] ?? $icons['other'];
    
    // ID unique pour le modal si mode modal
    $modalId = $openMode === 'modal' ? 'file-preview-modal-'.\Illuminate\Support\Str::uuid() : null;
@endphp

<div {{ $attributes->merge(['class' => 'file-preview inline-block '.$sizes['container']]) }}>
    @if($type === 'image')
        <div class="relative rounded-box overflow-hidden card-border hover:border-primary transition-colors">
            @if($openMode === 'modal')
                <button 
                    type="button"
                    onclick="document.getElementById('{{ $modalId }}').showModal()"
                    class="block w-full cursor-pointer"
                >
                    <img 
                        src="{{ $url }}" 
                        alt="{{ $name ?? 'Image' }}"
                        class="w-full h-auto {{ $sizes['image'] }} object-cover"
                        loading="lazy"
                    />
                </button>
            @else
                <a 
                    href="{{ $url }}" 
                    target="_blank" 
                    rel="noopener noreferrer"
                    class="block"
                >
                    <img 
                        src="{{ $url }}" 
                        alt="{{ $name ?? 'Image' }}"
                        class="w-full h-auto {{ $sizes['image'] }} object-cover"
                        loading="lazy"
                    />
                </a>
            @endif
            @if($downloadable)
                <button 
                    type="button"
                    class="absolute top-2 right-2 btn btn-sm btn-circle btn-primary opacity-80 hover:opacity-100 file-download"
                    data-url="{{ $url }}"
                    data-filename="{{ $name ?? 'image' }}"
                    title="{{ __('Download') }}"
                    onclick="event.stopPropagation();"
                >
                    <x-icon name="bi-download" class="w-4 h-4" />
                </button>
            @endif
        </div>
        @if($name)
            <p class="text-xs text-base-content/70 mt-1 truncate">{{ $name }}</p>
        @endif
        
        @if($openMode === 'modal' && $modalId)
            <x-daisy::ui.overlay.modal 
                :id="$modalId" 
                :title="$name ?? 'Image'"
                size="7xl"
                :boxClass="'p-0'"
            >
                <div class="flex items-center justify-center bg-base-200">
                    <img 
                        src="{{ $url }}" 
                        alt="{{ $name ?? 'Image' }}"
                        class="max-w-full max-h-[calc(100svh-8rem)] object-contain"
                    />
                </div>
                <x-slot:actions>
                    @if($downloadable)
                        <button 
                            type="button"
                            class="btn btn-primary file-download"
                            data-url="{{ $url }}"
                            data-filename="{{ $name ?? 'image' }}"
                            title="{{ __('Download') }}"
                        >
                            <x-icon name="bi-download" class="w-4 h-4 mr-2" />
                            {{ __('Download') }}
                        </button>
                    @endif
                    <form method="dialog">
                        <button type="submit" class="btn">{{ __('Close') }}</button>
                    </form>
                </x-slot:actions>
            </x-daisy::ui.overlay.modal>
        @endif
    @elseif($type === 'video')
        <div class="rounded-box overflow-hidden card-border bg-base-200">
            <video 
                src="{{ $url }}" 
                controls
                class="w-full {{ $sizes['image'] }} object-contain"
            >
                {{ __('Your browser does not support the video tag.') }}
            </video>
        </div>
        @if($name)
            <div class="flex items-center justify-between mt-1">
                <p class="text-xs text-base-content/70 truncate flex-1">{{ $name }}</p>
                @if($downloadable)
                    <button 
                        type="button"
                        class="btn btn-ghost btn-xs file-download"
                        data-url="{{ $url }}"
                        data-filename="{{ $name }}"
                        title="{{ __('Download') }}"
                    >
                        <x-icon name="bi-download" class="w-4 h-4" />
                    </button>
                @endif
            </div>
        @endif
    @elseif($type === 'audio')
        <div class="rounded-box card-border bg-base-200 p-3">
            <div class="flex items-center gap-3">
                <x-icon name="{{ $icon }}" class="{{ $sizes['icon'] }} text-primary" />
                <div class="flex-1 min-w-0">
                    @if($name)
                        <p class="text-sm font-medium truncate">{{ $name }}</p>
                    @endif
                    <audio src="{{ $url }}" controls class="w-full mt-1">
                        {{ __('Your browser does not support the audio tag.') }}
                    </audio>
                </div>
            </div>
        </div>
    @else
        {{-- PDF, Document, Other --}}
        <div class="rounded-box card-border bg-base-200 hover:bg-base-300 transition-colors p-3">
            <div class="flex items-center gap-3">
                <button 
                    type="button"
                    class="flex-shrink-0 file-download hover:opacity-80 transition-opacity cursor-pointer"
                    data-url="{{ $url }}"
                    data-filename="{{ $name ?? 'file' }}"
                    title="{{ __('Download') }}"
                >
                    <x-icon name="{{ $icon }}" class="{{ $sizes['icon'] }} text-primary" />
                </button>
                <button 
                    type="button"
                    class="flex-1 min-w-0 text-left file-download hover:opacity-80 transition-opacity cursor-pointer"
                    data-url="{{ $url }}"
                    data-filename="{{ $name ?? 'file' }}"
                    title="{{ __('Download') }}"
                >
                    @if($name)
                        <p class="text-sm font-medium truncate">{{ $name }}</p>
                    @endif
                    @if($fileSize)
                        <p class="text-xs text-base-content/70">{{ $fileSize }}</p>
                    @endif
                </button>
                @if($downloadable)
                    <button 
                        type="button"
                        class="btn btn-ghost btn-xs btn-circle file-download flex-shrink-0"
                        data-url="{{ $url }}"
                        data-filename="{{ $name ?? 'file' }}"
                        title="{{ __('Download') }}"
                        onclick="event.stopPropagation();"
                    >
                        <x-icon name="bi-download" class="w-4 h-4" />
                    </button>
                @endif
            </div>
        </div>
    @endif
</div>

@once
@push('scripts')
<script>
    (function() {
        // Gestion du téléchargement de fichiers
        function initFileDownloads() {
            document.querySelectorAll('.file-download').forEach(button => {
                if (button.dataset.downloadInitialized) return;
                button.dataset.downloadInitialized = 'true';
                
                button.addEventListener('click', async function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const url = this.dataset.url;
                    const filename = this.dataset.filename || 'file';
                    
                    try {
                        // Télécharger le fichier via fetch pour gérer les CORS
                        const response = await fetch(url, {
                            method: 'GET',
                            headers: {
                                'Accept': '*/*',
                            },
                        });
                        
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        
                        const blob = await response.blob();
                        const blobUrl = window.URL.createObjectURL(blob);
                        
                        // Créer un lien temporaire pour télécharger
                        const link = document.createElement('a');
                        link.href = blobUrl;
                        link.download = filename;
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        
                        // Libérer l'URL du blob
                        window.URL.revokeObjectURL(blobUrl);
                    } catch (error) {
                        console.error('Error downloading file:', error);
                        // Fallback : ouvrir dans un nouvel onglet si le téléchargement échoue
                        window.open(url, '_blank');
                    }
                });
            });
        }
        
        // Initialiser au chargement
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initFileDownloads);
        } else {
            initFileDownloads();
        }
        
        // Réinitialiser après les mutations DOM (pour le lazy-loading)
        if (typeof MutationObserver !== 'undefined') {
            const observer = new MutationObserver(initFileDownloads);
            observer.observe(document.body, {
                childList: true,
                subtree: true,
            });
        }
    })();
</script>
@endpush
@endonce


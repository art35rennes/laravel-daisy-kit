/**
 * Daisy Kit - File Input
 *
 * Fonctionnalités principales :
 * - Zone drag & drop optionnelle, reliée au vrai <input type="file">
 * - Prévisualisations basiques (image / vidéo / audio / autre)
 * - Respecte l'attribut accept (types MIME et extensions)
 *
 * Intégration (Blade) : voir le composant `ui/file-input.blade.php` qui injecte :
 * - [data-fileinput="1"] sur le wrapper principal
 * - [data-dropzone] pour la zone de glisser-déposer (DnD)
 * - [data-previews] pour le conteneur des vignettes de prévisualisation
 * - data-preview="true|false" pour activer ou non l'aperçu
 *
 * API globale : window.DaisyFileInput.{ init(root), initAll() }
 */

/**
 * Crée dynamiquement un élément DOM de prévisualisation pour un fichier donné,
 * en fonction de son type MIME.
 * - Images : <img>
 * - Vidéos : <video>
 * - Audios : <audio> dans un conteneur stylisé
 * - Autres : nom du fichier dans une boîte stylisée
 *
 * @param {File} file - Le fichier à prévisualiser
 * @returns {HTMLElement} - L'élément DOM de prévisualisation
 */
function createPreview(file) {
  const wrap = document.createElement('div');
  // Style général du conteneur de prévisualisation
  wrap.className = 'relative rounded-box overflow-hidden border border-base-300 bg-base-200/50 aspect-video';
  const ext = (file.name || '').toLowerCase();

  // Prévisualisation pour les images
  if (file.type.startsWith('image/')) {
    const img = document.createElement('img');
    img.className = 'h-full w-full object-cover';
    img.alt = file.name;
    const url = URL.createObjectURL(file);
    img.src = url;
    // Libère l'URL objet après chargement de l'image pour éviter les fuites mémoire
    img.onload = () => URL.revokeObjectURL(url);
    wrap.append(img);

  // Prévisualisation pour les vidéos
  } else if (file.type.startsWith('video/')) {
    const vid = document.createElement('video');
    vid.className = 'h-full w-full object-cover';
    vid.controls = true;
    vid.src = URL.createObjectURL(file);
    wrap.append(vid);

  // Prévisualisation pour les fichiers audio
  } else if (file.type.startsWith('audio/')) {
    const aud = document.createElement('audio');
    aud.controls = true;
    aud.className = 'w-full';
    aud.src = URL.createObjectURL(file);
    // Conteneur pour centrer le lecteur audio
    const box = document.createElement('div');
    box.className = 'flex items-center justify-center h-full bg-base-200';
    box.append(aud);
    wrap.append(box);

  // Prévisualisation générique pour les autres types de fichiers
  } else {
    const box = document.createElement('div');
    box.className = 'flex items-center justify-center h-full text-xs text-center p-2';
    box.textContent = file.name || 'Fichier';
    wrap.append(box);
  }
  return wrap;
}

/**
 * Initialise un composant file input personnalisé sur un élément racine donné.
 * Gère :
 * - La synchronisation entre la zone DnD et l'input natif
 * - Le filtrage des fichiers selon l'attribut accept
 * - L'affichage des prévisualisations si activé
 *
 * @param {HTMLElement} root - Élément racine du composant file input
 */
function initFileInput(root) {
  // Empêche une double initialisation sur le même élément
  if (!root || root.__fiInit) return;
  root.__fiInit = true;

  // Sélectionne l'input file, la zone DnD et le conteneur de prévisualisation
  const input = root.querySelector('input[type="file"]');
  if (!input) return;
  const drop = root.querySelector('[data-dropzone]');
  const previews = root.querySelector('[data-previews]');
  const wantPreview = root.dataset.preview === 'true';
  // Forcer le mode multiple si demandé côté wrapper (fiable même si l'attribut manque)
  if (root.dataset.multiple === 'true') {
    try { input.multiple = true; } catch (_) {}
  }

  // Analyse de l'attribut accept pour filtrer les fichiers autorisés
  const acceptAttr = (input.getAttribute('accept') || '').toLowerCase();
  const acceptTokens = acceptAttr.split(',').map((s) => s.trim()).filter(Boolean);

  /**
   * Vérifie si un fichier correspond aux contraintes de l'attribut accept.
   * Gère les extensions (.jpg), les types MIME (image/png), et les jokers (image/*).
   *
   * @param {File} file
   * @returns {boolean}
   */
  const matchesAccept = (file) => {
    if (!acceptTokens.length) return true;
    const type = (file.type || '').toLowerCase();
    const name = (file.name || '').toLowerCase();
    const dot = name.lastIndexOf('.');
    const ext = dot >= 0 ? name.slice(dot) : '';
    for (const token of acceptTokens) {
      if (token.startsWith('.')) {
        if (ext === token) return true;
      } else if (token.endsWith('/*')) {
        const major = token.split('/')[0];
        if (type && type.startsWith(major + '/')) return true;
      } else if (type === token) {
        return true;
      }
    }
    return false;
  };

  /**
   * Affiche les prévisualisations des fichiers sélectionnés dans le conteneur dédié.
   * Ne fait rien si l'aperçu n'est pas activé ou si le conteneur n'existe pas.
   *
   * @param {FileList|Array<File>} files
   */
  function render(files) {
    if (!wantPreview || !previews) return;
    previews.innerHTML = '';
    Array.from(files || []).forEach((f) => previews.append(createPreview(f)));
  }

  // Clique sur la zone DnD = déclenche le sélecteur de fichiers natif
  root.addEventListener('click', (e) => {
    if (e.target.closest('[data-dropzone]')) input.click();
  });

  // Gestion des événements drag & drop sur la zone dédiée
  if (drop) {
    // Ajoute une bordure colorée lors du drag
    ['dragenter', 'dragover'].forEach((ev) =>
      drop.addEventListener(ev, (e) => {
        e.preventDefault();
        drop.classList.add('border-primary');
      })
    );
    // Retire la bordure colorée lors du dragleave ou du drop
    ['dragleave', 'drop'].forEach((ev) =>
      drop.addEventListener(ev, (e) => {
        e.preventDefault();
        drop.classList.remove('border-primary');
      })
    );
    // Lors d'un drop, filtre les fichiers et les assigne à l'input natif
    drop.addEventListener('drop', (e) => {
      const dt = e.dataTransfer;
      if (!dt) return;
      const incoming = Array.from(dt.files || []);
      const accepted = incoming.filter(matchesAccept);
      if (accepted.length) {
        // Utilise DataTransfer pour assigner les fichiers à l'input (supporte le mode multiple)
        if (window.DataTransfer) {
          const d = new DataTransfer();
          // Si multiple, on conserve les fichiers déjà sélectionnés puis on ajoute les nouveaux
          if (input.multiple && input.files?.length) {
            Array.from(input.files).forEach((f) => d.items.add(f));
          }
          if (input.multiple) accepted.forEach((f) => d.items.add(f));
          else d.items.add(accepted[0]);
          input.files = d.files;
        }
        // Affiche les prévisualisations (utilise input.files si possible, sinon accepted)
        render(input.files?.length ? input.files : accepted);
        // Déclenche l'événement change pour l'input natif
        input.dispatchEvent(new Event('change', { bubbles: true }));
      }
    });
  }

  // Lors d'un changement de fichiers (sélection manuelle ou drop)
  input.addEventListener('change', () => {
    // Filtrage supplémentaire côté JS si accept est défini
    if (acceptTokens.length && input.files?.length && window.DataTransfer) {
      const filtered = Array.from(input.files).filter(matchesAccept);
      if (filtered.length !== input.files.length) {
        // Remplace la liste des fichiers par ceux acceptés uniquement
        const d = new DataTransfer();
        filtered.forEach((f) => d.items.add(f));
        input.files = d.files;
      }
    }
    // Affiche les prévisualisations
    render(input.files);
  });
}

/**
 * Initialise tous les file-inputs présents dans le DOM,
 * c'est-à-dire tous les éléments ayant l'attribut [data-fileinput="1"].
 */
function initAllFileInputs() {
  document.querySelectorAll('[data-fileinput="1"]').forEach(initFileInput);
}

// Expose l'API globale pour usage externe (ex: Alpine, Livewire, etc.)
window.DaisyFileInput = { init: initFileInput, initAll: initAllFileInputs };

// Export pour le système data-module (kit/index.js)
export default initFileInput;
export { initFileInput, initAllFileInputs };

// Initialisation automatique (compatible import tardif)
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initAllFileInputs);
} else {
  initAllFileInputs();
}

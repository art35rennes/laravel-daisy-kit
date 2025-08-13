/**
 * Daisy Kit - Éditeur de Code (CodeMirror 6)
 * 
 * Ce module fournit une interface complète pour intégrer CodeMirror 6 dans les composants Daisy Kit.
 * Il supporte la coloration syntaxique, le pliage de code, la recherche, et de nombreuses fonctionnalités avancées.
 * 
 * API globale: window.DaisyCodeEditor
 * 
 * Méthodes principales:
 * - init(root) : Initialise un éditeur sur l'élément racine
 * - initAll() : Initialise tous les éditeurs de la page
 * - getValue(root) : Récupère la valeur textuelle de l'éditeur
 * - setValue(root, text) : Définit la valeur textuelle de l'éditeur
 * - setLanguage(root, lang) : Change le langage de coloration syntaxique
 * - foldAll(root) : Plie tous les blocs de code
 * - unfoldAll(root) : Déplie tous les blocs de code
 * - format(root) : Formate le code (JSON uniquement pour l'instant)
 * - copy(root) : Copie le contenu dans le presse-papiers
 * 
 * Événements émis:
 * - 'code:change' : Émis lors du changement de contenu ({ detail: { value } })
 *
 * Attributs data supportés sur l'élément racine:
 * - data-language : Langage de programmation (js, html, css, json, md, xml, php, py)
 * - data-readonly : Mode lecture seule (true/false)
 * - data-tab-size : Taille des tabulations (défaut: 2)
 * - data-theme : Thème de l'éditeur (dark|light, auto-détecté si non spécifié)
 * 
 * Éléments enfants requis:
 * - .cm-host : Conteneur où sera monté l'éditeur CodeMirror
 * - textarea[data-sync] (optionnel) : Champ de formulaire synchronisé avec la valeur
 * - script[data-initial] (optionnel) : Données initiales JSON ({ value: "..." })
 */

// Imports des modules principaux de CodeMirror 6
import { EditorView, keymap, highlightActiveLine } from '@codemirror/view';
import { EditorState, Compartment } from '@codemirror/state';
import { defaultKeymap, indentWithTab } from '@codemirror/commands';
import { history, historyKeymap } from '@codemirror/commands';
import { lineNumbers } from '@codemirror/view';
import { highlightActiveLineGutter } from '@codemirror/view';
import { indentOnInput, foldGutter, foldKeymap, foldAll, unfoldAll } from '@codemirror/language';
import { searchKeymap } from '@codemirror/search';
import { lintKeymap } from '@codemirror/lint';
import { oneDark } from '@codemirror/theme-one-dark';

// Imports des extensions de langage
import { javascript } from '@codemirror/lang-javascript';
import { html } from '@codemirror/lang-html';
import { css } from '@codemirror/lang-css';
import { json } from '@codemirror/lang-json';
import { markdown } from '@codemirror/lang-markdown';
import { xml } from '@codemirror/lang-xml';
import { php } from '@codemirror/lang-php';
import { python } from '@codemirror/lang-python';

/**
 * Retourne l'extension de langage appropriée selon le nom fourni
 * @param {string} lang - Nom du langage (js, html, css, json, etc.)
 * @returns {Extension} Extension CodeMirror pour le langage
 */
function languageExtension(lang) {
  switch ((lang || '').toLowerCase()) {
    case 'js':
    case 'javascript':
    case 'ts':
    case 'typescript':
      return javascript({ typescript: /ts/.test(lang) });
    case 'html': return html();
    case 'css': return css();
    case 'json': return json();
    case 'md':
    case 'markdown': return markdown();
    case 'xml': return xml();
    case 'php': return php();
    case 'py':
    case 'python': return python();
    default: return javascript(); // Fallback vers JavaScript
  }
}

/**
 * Détermine si le thème sombre doit être utilisé
 * Priorité: data-theme > document data-theme > prefers-color-scheme
 * @param {HTMLElement} root - Élément racine de l'éditeur
 * @returns {boolean} True si le thème sombre doit être appliqué
 */
function currentIsDark(root) {
  const theme = root.dataset.theme;
  return theme === 'dark' || (!theme && (document.documentElement.getAttribute('data-theme')?.toLowerCase().includes('dark') || (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches)));
}

// Compartments pour permettre la reconfiguration dynamique des extensions
const languageCompartment = new Compartment();
const readOnlyCompartment = new Compartment();
const themeCompartment = new Compartment();

/**
 * Crée et initialise une nouvelle instance d'éditeur CodeMirror
 * @param {HTMLElement} root - Élément racine contenant la configuration et l'hôte
 */
function createEditor(root) {
  const host = root.querySelector('.cm-host');
  
  // Lecture des données initiales depuis un script JSON optionnel
  let initial = {};
  try {
    const raw = root.querySelector('script[data-initial]')?.textContent?.trim();
    initial = raw ? JSON.parse(raw) : {};
  } catch (e) {
    initial = {};
  }
  
  // Extraction de la configuration depuis les attributs data
  const readOnly = root.dataset.readonly === 'true';
  const lang = root.dataset.language || 'javascript';
  const tabSize = parseInt(root.dataset.tabSize || '2', 10);
  
  // Configuration des extensions CodeMirror
  const extensions = [
    // Interface utilisateur
    lineNumbers(),
    highlightActiveLineGutter(),
    highlightActiveLine(),
    
    // Fonctionnalités d'édition
    history(),
    indentOnInput(),
    foldGutter(),
    
    // Raccourcis clavier
    keymap.of([indentWithTab, ...defaultKeymap, ...foldKeymap, ...historyKeymap, ...searchKeymap, ...lintKeymap]),
    
    // Configuration dynamique via compartments
    languageCompartment.of(languageExtension(lang)),
    themeCompartment.of(currentIsDark(root) ? oneDark : []),
    readOnlyCompartment.of(EditorState.readOnly.of(!!readOnly)),
    
    // Configuration statique
    EditorState.tabSize.of(tabSize),
    
    // Écouteur de changements pour la synchronisation
    EditorView.updateListener.of((v) => {
      if (v.docChanged) {
        const value = v.state.doc.toString();
        
        // Synchronisation avec un textarea optionnel
        const sync = root.querySelector('textarea[data-sync]');
        if (sync) sync.value = value;
        
        // Émission de l'événement de changement
        root.dispatchEvent(new CustomEvent('code:change', { detail: { value }, bubbles: true }));
      }
    })
  ];
  
  // Création de l'état initial de l'éditeur
  const state = EditorState.create({
    doc: initial?.value ?? '',
    extensions
  });
  
  // Création et montage de la vue
  const view = new EditorView({ state, parent: host });
  
  // Stockage de la référence pour un accès ultérieur
  root.__cmView = view;
}

/**
 * Récupère l'instance EditorView associée à un élément racine
 * @param {HTMLElement} root - Élément racine de l'éditeur
 * @returns {EditorView|undefined} Instance de l'éditeur ou undefined
 */
function getView(root) { return root.__cmView; }

/**
 * Récupère la valeur textuelle complète de l'éditeur
 * @param {HTMLElement} root - Élément racine de l'éditeur
 * @returns {string} Contenu textuel de l'éditeur
 */
function getValue(root) { return getView(root)?.state.doc.toString() ?? ''; }

/**
 * Définit la valeur textuelle complète de l'éditeur
 * @param {HTMLElement} root - Élément racine de l'éditeur
 * @param {string} text - Nouveau contenu textuel
 */
function setValue(root, text) {
  const view = getView(root); if (!view) return;
  view.dispatch({ changes: { from: 0, to: view.state.doc.length, insert: text } });
}

/**
 * Change le langage de coloration syntaxique de l'éditeur
 * @param {HTMLElement} root - Élément racine de l'éditeur
 * @param {string} lang - Nouveau langage (js, html, css, etc.)
 */
function setLanguage(root, lang) {
  const view = getView(root); if (!view) return;
  view.dispatch({ effects: languageCompartment.reconfigure(languageExtension(lang)) });
  root.dataset.language = lang;
}

/**
 * Plie tous les blocs de code pliables dans l'éditeur
 * @param {HTMLElement} root - Élément racine de l'éditeur
 */
function doFoldAll(root) {
  const view = getView(root); if (!view) return;
  foldAll(view);
}

/**
 * Déplie tous les blocs de code dans l'éditeur
 * @param {HTMLElement} root - Élément racine de l'éditeur
 */
function doUnfoldAll(root) {
  const view = getView(root); if (!view) return;
  unfoldAll(view);
}

/**
 * Formate le code selon le langage détecté
 * Actuellement supporte uniquement le formatage JSON
 * @param {HTMLElement} root - Élément racine de l'éditeur
 */
async function doFormat(root) {
  const view = getView(root); if (!view) return;
  const code = getValue(root);
  const lang = (root.dataset.language || '').toLowerCase();
  
  try {
    // Formatage JSON avec indentation
    if (lang === 'json') {
      const obj = JSON.parse(code);
      setValue(root, JSON.stringify(obj, null, 2));
      return;
    }
  } catch (_) { 
    // Ignore les erreurs de parsing JSON
  }
  
  // Fallback: simple nettoyage des espaces
  setValue(root, code.trim());
}

/**
 * Copie le contenu de l'éditeur dans le presse-papiers
 * Affiche un feedback visuel temporaire sur le bouton de copie
 * @param {HTMLElement} root - Élément racine de l'éditeur
 */
async function doCopy(root) {
  try {
    await navigator.clipboard.writeText(getValue(root));
    
    // Feedback visuel sur le bouton de copie
    const btn = root.querySelector('[data-action="copy"]');
    if (btn) {
      const prev = btn.textContent;
      btn.textContent = 'Copié!';
      setTimeout(() => (btn.textContent = prev), 1000);
    }
  } catch (_) {
    // Échec silencieux si l'API clipboard n'est pas disponible
  }
}

/**
 * Attache les gestionnaires d'événements pour la barre d'outils
 * Gère les boutons avec l'attribut data-action
 * @param {HTMLElement} root - Élément racine de l'éditeur
 */
function bindToolbar(root) {
  root.addEventListener('click', async (e) => {
    const btn = e.target.closest('button[data-action]');
    if (!btn || !root.contains(btn)) return;
    
    const act = btn.dataset.action;
    
    // Dispatch vers les actions appropriées
    if (act === 'fold-all') doFoldAll(root);
    if (act === 'unfold-all') doUnfoldAll(root);
    if (act === 'format') await doFormat(root);
    if (act === 'copy') await doCopy(root);
  });
}

/**
 * Initialise un éditeur sur un élément racine donné
 * Évite la double initialisation grâce au flag __cmInit
 * @param {HTMLElement} root - Élément racine à transformer en éditeur
 */
function init(root) {
  if (!root || root.__cmInit) return;
  root.__cmInit = true;
  createEditor(root);
  bindToolbar(root);
}

/**
 * Initialise tous les éditeurs présents dans la page
 * Recherche tous les éléments avec la classe 'code-editor'
 */
function initAll() {
  document.querySelectorAll('.code-editor').forEach(init);
}

// Export de l'API publique vers l'objet global window
window.DaisyCodeEditor = {
  init,
  initAll,
  getValue,
  setValue,
  setLanguage,
  foldAll: doFoldAll,
  unfoldAll: doUnfoldAll,
  format: doFormat,
  copy: doCopy,
};

// Auto-initialisation (compatible import tardif)
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initAll);
} else {
  initAll();
}

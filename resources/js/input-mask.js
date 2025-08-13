/**
 * Daisy Kit - Input mask & obfuscation utilities
 *
 * CONTEXTE GÉNÉRAL :
 * Ce module JS fournit deux fonctionnalités principales pour les champs de saisie :
 *   1. Un masquage de saisie basé sur un "pattern" (hérité, type InputMask classique)
 *   2. Une obfuscation simple d'affichage (recommandée pour la confidentialité UX)
 *      - Exemples d'usage : masquage partiel de numéros, mots de passe, etc.
 *
 * API (data-attributes) :
 * - Pattern mask (hérité) :
 *   - input: [data-inputmask="1"]
 *   - data-mask : pattern avec tokens '9' (chiffre), 'a' (lettre), '*' (alphanumérique), + caractères spéciaux
 *   - data-mask-placeholder, data-char-placeholder, data-clear-incomplete, data-input-placeholder
 *   - data-custom-mask, data-custom-validator
 *   - Événements JS : 'inputmask:change', 'inputmask:completed'
 *
 * - Obfuscation (recommandée) :
 *   - input: [data-obfuscate="1"]
 *   - data-obfuscate-char : caractère d'affichage masqué (défaut '*')
 *   - data-obfuscate-keep-end : nombre de caractères visibles en fin de chaîne (ex: 4)
 *   - Événement : 'obfuscate:change' (detail.value = valeur réelle)
 *   - Implémentation : la valeur réelle est conservée, l'affichage est masqué, un input hidden est créé pour le submit si un name est défini.
 *
 * Notes de maintenance :
 * - La partie "pattern mask" est conservée pour compatibilité, mais non requise si seule l'obfuscation est voulue.
 * - L'obfuscation s'appuie sur 'beforeinput' pour garder la maîtrise du caret et éviter les incohérences.
 * - Les méthodes sont découpées pour faciliter la compréhension et la maintenance.
 */

/**
 * Construit la table des définitions de tokens pour le mask pattern.
 * @param {Array} customMaskChars - caractères personnalisés pour le mask
 * @param {Array} customValidators - regex personnalisées pour chaque caractère
 * @returns {Object} defs - dictionnaire {token: regex}
 */
function buildTokenDefs(customMaskChars, customValidators) {
  // Définitions de base : 9 = chiffre, a = lettre, * = alphanumérique
  const defs = {
    '9': /[0-9]/,
    'a': /[A-Za-z]/,
    '*': /[A-Za-z0-9]/,
  };
  // Ajout des définitions personnalisées si présentes
  if (customMaskChars && customValidators && customMaskChars.length === customValidators.length) {
    customMaskChars.forEach((ch, idx) => {
      try {
        // On retire les éventuels '/' entourant la regex (ex: /[abc]/)
        const raw = String(customValidators[idx]);
        const body = raw.startsWith('/') && raw.lastIndexOf('/') > 0 ? raw.slice(1, raw.lastIndexOf('/')) : raw;
        defs[ch] = new RegExp(body);
      } catch (_) {
        // En cas d'erreur de regex, on ignore silencieusement
      }
    });
  }
  return defs;
}

/**
 * Compile le pattern de mask en une liste de tokens (slots ou statiques)
 * @param {string} maskPattern - pattern du mask (ex: '99-99')
 * @param {Object} defs - définitions des tokens
 * @returns {Array} tokens - liste d'objets {type, ...}
 */
function compileMask(maskPattern, defs) {
  const tokens = [];
  for (let i = 0; i < maskPattern.length; i += 1) {
    const ch = maskPattern[i];
    if (defs[ch]) {
      // Slot à remplir par l'utilisateur
      tokens.push({ type: 'slot', test: defs[ch], key: ch });
    } else {
      // Caractère statique (ex: '-')
      tokens.push({ type: 'static', value: ch });
    }
  }
  return tokens;
}

/**
 * Génère le placeholder d'affichage à partir des tokens et du caractère de placeholder
 * @param {Array} tokens
 * @param {string} placeholderChar
 * @returns {string}
 */
function formatPlaceholder(tokens, placeholderChar) {
  let out = '';
  tokens.forEach((t) => {
    if (t.type === 'slot') out += placeholderChar;
    else out += t.value;
  });
  return out;
}

/**
 * Extrait les caractères saisis par l'utilisateur qui remplissent les slots du mask
 * @param {string} str - valeur courante de l'input
 * @param {Array} tokens - tokens du mask
 * @returns {Array} typed - caractères valides saisis
 */
function extractTypedChars(str, tokens) {
  // On ne garde que les caractères qui pourraient remplir les slots, dans l'ordre
  const typed = [];
  let si = 0; // index du slot courant dans tokens
  for (let i = 0; i < str.length; i += 1) {
    const ch = str[i];
    // Avance dans les tokens jusqu'au prochain slot ou statique correspondant
    while (si < tokens.length && tokens[si].type === 'static') {
      // Si l'utilisateur a tapé le caractère statique attendu, on le consomme
      if (ch === tokens[si].value) {
        si += 1;
      }
      si += 0; // no-op; break condition handled below
      break;
    }
    // Recherche du prochain slot
    while (si < tokens.length && tokens[si].type !== 'slot') si += 1;
    if (si >= tokens.length) break;
    const slot = tokens[si];
    if (slot.test.test(ch)) {
      typed.push(ch);
      si += 1; // passe au slot suivant
    } else {
      // caractère invalide ignoré
    }
  }
  return typed;
}

/**
 * Applique le mask aux caractères saisis, retourne la valeur masquée et si le mask est complet
 * @param {Array} typedChars - caractères saisis valides
 * @param {Array} tokens - tokens du mask
 * @param {Object} options - options diverses
 * @returns {Object} { value, isCompleted }
 */
function applyMask(typedChars, tokens, options) {
  const { maskPlaceholder, charPlaceholder } = options;
  let out = '';
  let ti = 0; // index dans typedChars
  tokens.forEach((t) => {
    if (t.type === 'static') {
      out += t.value;
    } else {
      if (ti < typedChars.length) {
        out += typedChars[ti++];
      } else if (maskPlaceholder) {
        out += charPlaceholder;
      }
    }
  });
  // Le mask est "complet" si tous les slots sont remplis
  const isCompleted = ti >= typedChars.length && tokens.filter((t) => t.type === 'slot').length === typedChars.length;
  return { value: out, isCompleted };
}

/**
 * Classe principale pour le pattern mask (hérité).
 * Pour la confidentialité simple, préférer initObfuscate.
 */
class DaisyInputMask {
  /**
   * @param {HTMLInputElement} input - l'input à masquer
   * @param {Object} options - options de configuration
   */
  constructor(input, options = {}) {
    this.input = input;
    this.options = Object.assign({
      inputMask: '',
      charPlaceholder: '_',
      maskPlaceholder: false,
      inputPlaceholder: true,
      clearIncomplete: true,
      customMask: '',
      customValidator: '',
    }, options);

    // Extraction des masques personnalisés éventuels
    const customMaskChars = (this.options.customMask || '')
      .split(',').map((s) => s.trim()).filter(Boolean);
    const customValidators = (this.options.customValidator || '')
      .split(',').map((s) => s.trim()).filter((v) => v != null && v !== '');

    // Construction des définitions et tokens du mask
    this.defs = buildTokenDefs(customMaskChars, customValidators);
    this.tokens = compileMask(this.options.inputMask || '', this.defs);

    // Liaison des handlers d'événements
    this.onInput = this.onInput.bind(this);
    this.onBlur = this.onBlur.bind(this);

    this._init();
  }

  /**
   * Initialise le composant : placeholder, valeur initiale, listeners
   */
  _init() {
    // Ajout du placeholder si demandé
    if (this.options.inputPlaceholder) {
      this.input.setAttribute('placeholder', formatPlaceholder(this.tokens, this.options.charPlaceholder));
    }
    // Premier rendu basé sur la valeur initiale
    this.renderFromRaw(this.getRawValueFromInput());
    // Ajout des listeners
    this.input.addEventListener('input', this.onInput);
    this.input.addEventListener('blur', this.onBlur);
  }

  /**
   * Extrait la valeur "brute" (non masquée) de l'input
   * @returns {Array} caractères saisis valides
   */
  getRawValueFromInput() {
    const current = this.input.value || '';
    return extractTypedChars(current, this.tokens);
  }

  /**
   * Met à jour l'affichage de l'input à partir des caractères saisis
   * @param {Array} typedChars
   */
  renderFromRaw(typedChars) {
    const { value, isCompleted } = applyMask(typedChars, this.tokens, this.options);
    this.input.value = value;
    // Événement personnalisé pour signaler le changement
    const changeEvent = new CustomEvent('inputmask:change', { detail: { value }, bubbles: true });
    this.input.dispatchEvent(changeEvent);
    // Si le mask est complet, on émet un événement dédié
    if (isCompleted) {
      this.input.dispatchEvent(new CustomEvent('inputmask:completed', { detail: { value }, bubbles: true }));
    }
  }

  /**
   * Handler pour l'événement 'input'
   */
  onInput() {
    const typed = this.getRawValueFromInput();
    this.renderFromRaw(typed);
  }

  /**
   * Handler pour l'événement 'blur'
   * Vide l'input si la saisie est incomplète (option clearIncomplete)
   */
  onBlur() {
    if (this.options.clearIncomplete) {
      const typed = this.getRawValueFromInput();
      const totalSlots = this.tokens.filter((t) => t.type === 'slot').length;
      if (typed.length < totalSlots) {
        this.input.value = '';
      }
    }
  }

  /**
   * Nettoie les listeners et références (pour le GC)
   */
  dispose() {
    this.input.removeEventListener('input', this.onInput);
    this.input.removeEventListener('blur', this.onBlur);
    delete this.input.__daisyMask;
  }

  /**
   * Récupère l'instance associée à un input (si existante)
   * @param {HTMLElement} element
   * @returns {DaisyInputMask|null}
   */
  static getInstance(element) {
    return element.__daisyMask || null;
  }
}

/**
 * Initialise un input avec le pattern mask si non déjà initialisé
 * @param {HTMLInputElement} el
 */
function initInputMask(el) {
  if (!el || el.__daisyMask) return;
  // Récupération des options via data-attributes
  const opts = {
    inputMask: el.getAttribute('data-mask') || '',
    charPlaceholder: el.getAttribute('data-char-placeholder') || '_',
    maskPlaceholder: el.getAttribute('data-mask-placeholder') === 'true',
    inputPlaceholder: el.getAttribute('data-input-placeholder') !== 'false',
    clearIncomplete: el.getAttribute('data-clear-incomplete') !== 'false',
    customMask: el.getAttribute('data-custom-mask') || '',
    customValidator: el.getAttribute('data-custom-validator') || '',
  };
  el.__daisyMask = new DaisyInputMask(el, opts);
}

/**
 * Initialise tous les inputs de la page avec [data-inputmask="1"]
 */
function initAllInputMasks() {
  document.querySelectorAll('input[data-inputmask="1"]').forEach(initInputMask);
}

// -----------------------------------------------------------------------------
// Obfuscation simple (masquage d'affichage) - version recommandée
// -----------------------------------------------------------------------------

/**
 * Obfuscation d'affichage : affiche une version masquée tout en conservant la valeur réelle.
 *
 * Comportement :
 * - Si l'input possède un "name", on le supprime côté visible et on crée un input hidden
 *   jumeau pour envoyer la valeur réelle côté serveur.
 * - Gestion du caret et des inserts/suppressions via 'beforeinput' pour garantir la
 *   cohérence entre valeur réelle et affichage masqué.
 * - La valeur réelle est stockée sur input.__obfReal
 * - L'affichage masqué est généré à la volée à chaque modification
 * - L'événement 'obfuscate:change' est émis à chaque changement de valeur réelle
 *
 * @param {HTMLInputElement} input
 */
function initObfuscate(input) {
  if (!input || input.__obfInit) return; // Évite double initialisation
  input.__obfInit = true;

  // Nombre de caractères à garder visibles en fin de chaîne
  const keepEnd = parseInt(input.getAttribute('data-obfuscate-keep-end') || '0', 10) || 0;
  // Caractère utilisé pour masquer
  const maskChar = input.getAttribute('data-obfuscate-char') || '*';
  let hidden = null;
  const originalName = input.getAttribute('name');
  let real = input.value || '';

  // Si l'input a un name, on crée un input hidden pour le submit
  if (originalName) {
    hidden = document.createElement('input');
    hidden.type = 'hidden';
    hidden.name = originalName;
    hidden.value = real;
    input.removeAttribute('name');
    input.insertAdjacentElement('afterend', hidden);
  }

  // Stocke la valeur réelle sur l'input
  input.__obfReal = real;

  /**
   * Transforme la valeur réelle en affichage masqué, en gardant 'keepEnd' caractères finaux visibles
   * @param {string} value
   * @returns {string}
   */
  function maskString(value) {
    const s = String(value || '');
    if (!s.length) return '';
    const k = Math.max(0, Math.min(keepEnd, s.length));
    const maskLen = Math.max(0, s.length - k);
    return (maskLen ? maskChar.repeat(maskLen) : '') + (k ? s.slice(-k) : '');
  }

  /**
   * Met à jour l'affichage masqué et la valeur du hidden input
   */
  function render() {
    input.value = maskString(input.__obfReal);
    if (hidden) hidden.value = input.__obfReal;
    // Émet un événement personnalisé à chaque changement de valeur réelle
    input.dispatchEvent(new CustomEvent('obfuscate:change', { detail: { value: input.__obfReal }, bubbles: true }));
  }

  // Initialisation de l'affichage masqué
  render();

  /**
   * Handler 'beforeinput' : intercepte toutes les modifications pour garder la cohérence
   * entre la valeur réelle et l'affichage masqué, et gérer correctement le caret.
   */
  input.addEventListener('beforeinput', (e) => {
    try {
      const type = e.inputType;
      const data = e.data;
      // Position du caret dans l'input visible
      let start = input.selectionStart ?? input.value.length;
      let end = input.selectionEnd ?? input.value.length;
      // On s'assure que les indices sont dans les bornes de la valeur réelle
      start = Math.max(0, Math.min(start, input.__obfReal.length));
      end = Math.max(0, Math.min(end, input.__obfReal.length));
      let next = input.__obfReal;

      // Gestion des différents types d'input
      if (type === 'insertText') {
        // Insertion de texte (touche clavier)
        const text = data ?? '';
        next = next.slice(0, start) + text + next.slice(end);
        const newPos = start + text.length;
        input.__obfReal = next;
        e.preventDefault();
        render();
        // Restaure la position du caret après l'insertion
        requestAnimationFrame(() => { try { input.setSelectionRange(newPos, newPos); } catch (_) {} });
        return;
      }
      if (type === 'insertFromPaste') {
        // Collage de texte
        const text = data ?? '';
        next = next.slice(0, start) + text + next.slice(end);
        const newPos = start + text.length;
        input.__obfReal = next;
        e.preventDefault();
        render();
        requestAnimationFrame(() => { try { input.setSelectionRange(newPos, newPos); } catch (_) {} });
        return;
      }
      if (type === 'deleteContentBackward') {
        // Suppression arrière (Backspace)
        if (start === end) {
          if (start > 0) next = next.slice(0, start - 1) + next.slice(end);
          start = Math.max(0, start - 1);
        } else {
          next = next.slice(0, start) + next.slice(end);
        }
        input.__obfReal = next;
        e.preventDefault();
        render();
        requestAnimationFrame(() => { try { input.setSelectionRange(start, start); } catch (_) {} });
        return;
      }
      if (type === 'deleteContentForward') {
        // Suppression avant (Delete)
        if (start === end) {
          next = next.slice(0, start) + next.slice(Math.min(next.length, end + 1));
        } else {
          next = next.slice(0, start) + next.slice(end);
        }
        input.__obfReal = next;
        e.preventDefault();
        render();
        requestAnimationFrame(() => { try { input.setSelectionRange(start, start); } catch (_) {} });
        return;
      }
      // Par défaut : on bloque toute autre modification imprévisible
      e.preventDefault();
      render();
    } catch (_) {
      // En cas d'erreur, on ignore pour éviter de casser l'input
    }
  });
}

/**
 * Initialise tous les inputs de la page avec [data-obfuscate="1"]
 */
function initAllObfuscate() {
  document.querySelectorAll('input[data-obfuscate="1"]').forEach(initObfuscate);
}

// -----------------------------------------------------------------------------
// Export global et auto-initialisation au DOMContentLoaded
// -----------------------------------------------------------------------------

// Expose les fonctions principales sur window.DaisyInputMask
window.DaisyInputMask = {
  init: initInputMask,
  initAll: initAllInputMasks,
  Class: DaisyInputMask,
  initObfuscate,
  initAllObfuscate
};

// Initialise automatiquement (compatible import tardif)
function doInit() {
  initAllInputMasks();
  initAllObfuscate();
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', doInit);
} else {
  doInit();
}

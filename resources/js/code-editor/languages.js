const loaders = {
    text: async () => [],
    php: () => import('@codemirror/lang-php').then(module => module.php()),
    html: () => import('@codemirror/lang-html').then(module => module.html()),
    css: () => import('@codemirror/lang-css').then(module => module.css()),
    javascript: () => import('@codemirror/lang-javascript').then(module => module.javascript()),
    typescript: () => import('@codemirror/lang-javascript').then(module => module.javascript({ typescript: true })),
    json: () => import('@codemirror/lang-json').then(module => module.json()),
    markdown: () => import('@codemirror/lang-markdown').then(module => module.markdown()),
    sql: () => import('@codemirror/lang-sql').then(module => module.sql()),
    yaml: () => import('@codemirror/lang-yaml').then(module => module.yaml()),
};
const pending = new Map();

export function loadLanguage(language) {
    if (!Object.hasOwn(loaders, language)) return Promise.reject(new Error('Unsupported language.'));
    if (!pending.has(language)) {
        pending.set(language, loaders[language]().catch(error => {
            pending.delete(language);
            throw error;
        }));
    }
    return pending.get(language);
}

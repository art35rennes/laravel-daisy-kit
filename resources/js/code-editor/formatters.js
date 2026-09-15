const javascript = () => Promise.all([import('prettier/plugins/babel'), import('prettier/plugins/estree')]);
const loaders = {
    json: { parser: 'json', plugins: javascript },
    javascript: { parser: 'babel', plugins: javascript },
    typescript: { parser: 'typescript', plugins: () => Promise.all([import('prettier/plugins/typescript'), import('prettier/plugins/estree')]) },
    html: { parser: 'html', plugins: () => Promise.all([import('prettier/plugins/html')]) },
    css: { parser: 'css', plugins: () => Promise.all([import('prettier/plugins/postcss')]) },
    markdown: { parser: 'markdown', plugins: () => Promise.all([import('prettier/plugins/markdown')]) },
    yaml: { parser: 'yaml', plugins: () => Promise.all([import('prettier/plugins/yaml')]) },
    php: { parser: 'php', plugins: () => Promise.all([import('@prettier/plugin-php/standalone')]) },
};
const pending = new Map();

export const canFormat = language => language === 'sql' || Object.hasOwn(loaders, language);

function loadFormatter(language) {
    if (!pending.has(language)) {
        const operation = language === 'sql'
            ? import('sql-formatter').then(module => (value, tabWidth, cursorOffset) => {
                const formatted = module.format(value, { language: 'sql', tabWidth });
                return { formatted, cursorOffset: Math.min(cursorOffset, formatted.length) };
            })
            : Promise.all([import('prettier/standalone'), loaders[language].plugins()]).then(([prettier, plugins]) =>
                (value, tabWidth, cursorOffset) => prettier.formatWithCursor(value, {
                    parser: loaders[language].parser, plugins: plugins.map(plugin => plugin.default ?? plugin),
                    tabWidth, cursorOffset, embeddedLanguageFormatting: 'off',
                    ...(language === 'php' ? { phpVersion: '8.4' } : {}),
                }));
        pending.set(language, operation.catch(error => { pending.delete(language); throw error; }));
    }
    return pending.get(language);
}

export async function formatCode(value, language, tabWidth, cursorOffset) {
    if (!canFormat(language)) throw new Error('Unsupported formatting language.');
    const formatter = await loadFormatter(language);
    return formatter(value, tabWidth, cursorOffset);
}

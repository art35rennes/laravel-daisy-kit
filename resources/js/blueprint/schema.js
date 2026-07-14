const TRANSITION_SHAPES = new Set(['straight', 'curve', 's', 'orthogonal']);
const SEMANTIC_COLORS = new Set([
    'primary',
    'secondary',
    'accent',
    'neutral',
    'info',
    'success',
    'warning',
    'error',
]);

function normalizeText(value) {
    return typeof value === 'string' || typeof value === 'number'
        ? String(value).trim()
        : '';
}

function normalizeCategory(category, { withColor, withPresentation }) {
    if (typeof category === 'string' || typeof category === 'number') {
        const value = normalizeText(category);

        return value ? { value, label: value } : null;
    }

    if (!category || typeof category !== 'object' || Array.isArray(category)) {
        return null;
    }

    const value = normalizeText(category.value);
    if (!value) {
        return null;
    }

    const normalized = {
        value,
        label: normalizeText(category.label) || value,
    };

    if (withPresentation && TRANSITION_SHAPES.has(category.shape)) {
        normalized.shape = category.shape;
    }

    if ((withColor || withPresentation) && SEMANTIC_COLORS.has(category.color)) {
        normalized.color = category.color;
    }

    return normalized;
}

export function normalizeCategories(categories, options = {}) {
    const normalizedOptions = {
        withColor: options.withColor === true,
        withPresentation: options.withPresentation === true,
    };

    return (Array.isArray(categories) ? categories : [])
        .map(category => normalizeCategory(category, normalizedOptions))
        .filter((category, index, collection) => (
            category && collection.findIndex(item => item?.value === category.value) === index
        ));
}

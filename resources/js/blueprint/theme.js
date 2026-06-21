export const blueprintThemeNames = ['primary', 'secondary', 'accent', 'neutral', 'info', 'success', 'warning', 'error'];

export const blueprintThemeAliases = {
  action: 'success',
  condition: 'warning',
  data: 'accent',
  default: 'primary',
  function: 'info',
  schema: 'secondary',
  trigger: 'primary',
};

export function normalizeBlueprintTheme(theme) {
  const normalized = String(theme || 'primary').trim();

  if (blueprintThemeNames.includes(normalized)) {
    return normalized;
  }

  return blueprintThemeAliases[normalized] || 'primary';
}

export function resolveThemeTokens(theme) {
  const name = normalizeBlueprintTheme(theme);

  return {
    name,
    color: `var(--color-${name})`,
    content: `var(--color-${name}-content)`,
  };
}

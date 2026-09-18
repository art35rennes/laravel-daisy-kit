import { describe, expect, it } from 'vitest';
import { canFormat, formatCode } from '../../../resources/js/code-editor/formatters.js';

describe('on-demand code formatters', () => {
    it.each([
        ['json', '{"enabled":true,"nested":{"value":1}}'],
        ['javascript', 'function add(a,b){return a+b}'],
        ['typescript', 'const value:number=1'],
        ['html', '<div><p>Hello</p><p>World</p></div>'],
        ['css', 'a{color:red;background:white}'],
        ['markdown', '# Heading\n\n-   item\n'],
        ['yaml', 'items: [one,two]'],
        ['php', '<?php function add($a,$b){return $a+$b;}'],
        ['sql', 'select id,name from users where active=1'],
    ])('formats %s idempotently', async (language, value) => {
        expect(canFormat(language)).toBe(true);
        const result = await formatCode(value, language, 4, value.length);
        expect(result.formatted).not.toBe(value);
        expect(result.cursorOffset).toBeGreaterThanOrEqual(0);
        expect(result.cursorOffset).toBeLessThanOrEqual(result.formatted.length);
        const again = await formatCode(result.formatted, language, 4, result.cursorOffset);
        expect(again.formatted).toBe(result.formatted);
    });

    it('rejects invalid code and unsupported languages', async () => {
        expect(canFormat('text')).toBe(false);
        await expect(formatCode('{invalid', 'json', 4, 0)).rejects.toThrow();
        await expect(formatCode('hello', 'text', 4, 0)).rejects.toThrow();
    });
});

/** @vitest-environment jsdom */

import { beforeEach, describe, expect, it, vi } from 'vitest';

const grid = {
    engine: { nodes: [] },
    opts: { column: 12, float: false },
    on: vi.fn(),
    setStatic: vi.fn(),
};

const init = vi.fn(() => grid);

vi.mock('gridstack', () => ({
    GridStack: { init },
}));

const { default: initEditableGrid } = await import('../../../resources/js/modules/editable-grid.js');

describe('editable grid module', () => {
    beforeEach(() => {
        document.body.innerHTML = '';
        vi.clearAllMocks();
    });

    it('initializes GridStack with four corner resize handles', () => {
        document.body.innerHTML = `
            <div data-module="editable-grid">
                <div class="grid-stack daisy-editable-grid">
                    <div class="grid-stack-item" gs-id="item-a" gs-x="0" gs-y="0" gs-w="4" gs-h="2">
                        <div class="grid-stack-item-content">Widget</div>
                    </div>
                </div>
                <script type="application/json" data-editable-grid-config>
                    {"editable":true,"static":false,"columns":12,"cellHeight":80,"gap":12}
                </script>
            </div>
        `;

        const host = document.querySelector('[data-module="editable-grid"]');

        initEditableGrid(host);

        expect(init).toHaveBeenCalledWith(
            expect.objectContaining({
                alwaysShowResizeHandle: true,
                resizable: {
                    handles: 'se,sw,ne,nw',
                    autoHide: false,
                },
            }),
            document.querySelector('.grid-stack'),
        );
    });
});

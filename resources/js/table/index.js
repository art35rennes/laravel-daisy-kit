import initTable, { initAllTables } from '../table-kit.js';

// This is the lazy-loaded application entrypoint, so initialize the table
// runtime explicitly after the chunk is evaluated.
void initAllTables();

export * from '../table-kit.js';

export default initTable;

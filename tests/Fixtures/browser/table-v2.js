import { initTable, tableApi } from '/resources/js/table-kit.js';

const root = document.getElementById('scope-users');

await initTable(root);

const table = tableApi('scope-users');
const stateOutput = document.getElementById('state-output');

function reportState() {
  stateOutput.value = JSON.stringify({
    rows: table.getRows().map((row) => ({ id: row.id, name: row.name })),
    tableResolved: window.DaisyTable.table('scope-users') !== null,
    missingIsNull: window.DaisyTable.table('missing') === null,
    tenant: new URL(window.location.href).searchParams.get('tenant'),
  });
}

document.getElementById('load').addEventListener('click', async () => {
  await table.setLoading(true);
  await table.setRows([
    { id: '1', name: 'Ada', actions: { action: 'remove', label: 'Remove Ada', variant: 'error' } },
    { id: '2', name: 'Benoit', actions: { action: 'remove', label: 'Remove Benoit', variant: 'error' } },
  ]);
  reportState();
});

document.getElementById('upsert').addEventListener('click', async () => {
  await table.upsertRows([
    { id: '2', name: 'Benoit updated', actions: { action: 'remove', label: 'Remove Benoit', variant: 'error' } },
    { id: '3', name: 'Chloe', actions: { action: 'remove', label: 'Remove Chloe', variant: 'error' } },
  ]);
  reportState();
});

document.getElementById('remove').addEventListener('click', async () => {
  await table.removeRows(['1']);
  reportState();
});

root.addEventListener('daisy:table-row-action', (event) => {
  document.getElementById('action-output').value = `${event.detail.action}:${event.detail.rowId}`;
});

window.__daisyTableHarness = {
  table,
  root,
};

reportState();

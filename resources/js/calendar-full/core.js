import { readOptions, DEFAULTS } from './defaults';
import { createStaticSource, createHttpSource, toIsoDate, intersects } from './data';
import { renderMonth, renderWeek, renderDay, renderList, renderYear } from './renderers';
import { onNextFrame } from '../utils/scheduler';

/**
 * Gestion d'instances CalendarFull
 */

const INSTANCES = new WeakMap();

export function mountAllCalendars(){
  document.querySelectorAll('[data-calendar-full="1"]').forEach((el) => mount(el));
}

export function mount(el){
  if (INSTANCES.has(el)) return INSTANCES.get(el);
  const opts = readOptions(el);
  const eventsInline = parseJson(el.getAttribute('data-events')) || null;
  const eventsUrl = el.getAttribute('data-events-url');
  const source = eventsUrl ? createHttpSource(eventsUrl) : createStaticSource(eventsInline || []);

  const state = {
    options: opts,
    source,
    currentDate: opts.initialDate ? new Date(opts.initialDate) : new Date(),
    view: opts.view,
    destroy: () => {},
  };

  const api = buildInstanceApi(el, state);
  INSTANCES.set(el, api);
  api.render();
  return api;
}

function buildInstanceApi(el, state){
  const header = document.createElement('div'); header.className = 'cf-toolbar';
  const titleEl = document.createElement('div'); titleEl.className = 'font-medium';
  const left = document.createElement('div'); left.className = 'btn-group';
  const right = document.createElement('div'); right.className = 'btn-group';
  header.appendChild(left); header.appendChild(titleEl); header.appendChild(right);

  function buildToolbar(){
    left.innerHTML = '';
    right.innerHTML = '';
    const prev = btn('«'); prev.addEventListener('click', () => step(-1));
    const today = btnLabel(i18n('today')); today.addEventListener('click', () => { state.currentDate = new Date(); render(); });
    const next = btn('»'); next.addEventListener('click', () => step(1));
    left.appendChild(prev); left.appendChild(today); left.appendChild(next);
    const views = state.options.views;
    views.forEach((v) => {
      const b = btnLabel(capitalize(v));
      if (v === state.view) b.classList.add('btn-primary'); else b.classList.add('btn-outline');
      b.addEventListener('click', () => { state.view = v; render(); });
      right.appendChild(b);
    });
  }

  function btn(txt){ const b = document.createElement('button'); b.className = 'btn btn-sm'; b.textContent = txt; return b; }
  function btnLabel(txt){ const b = document.createElement('button'); b.className = 'btn btn-sm'; b.textContent = txt; return b; }

  function step(dir){
    const d = new Date(state.currentDate);
    switch(state.view){
      case 'day': d.setDate(d.getDate()+dir); break;
      case 'week': d.setDate(d.getDate()+7*dir); break;
      case 'year': d.setFullYear(d.getFullYear()+dir); break;
      case 'list': d.setDate(d.getDate()+7*dir); break;
      default: d.setMonth(d.getMonth()+dir); break; // month
    }
    state.currentDate = d; render();
  }

  async function render(){
    // Calcul de la plage à charger selon la vue actuelle
    const { start, end } = rangeForView(state.view, state.currentDate, state.options);
    // Chargement (avec micro-délai pour laisser respirer le main thread)
    await new Promise((r) => onNextFrame(r));
    let events = [];
    try { events = await state.source.loadRange(start, end); } catch (_) { events = []; }

    // Prépare le conteneur visuel
    el.style.minHeight = state.options.height === 'auto' ? '' : `${parseInt(state.options.height,10)||600}px`;
    // Construire le contenu
    const container = document.createElement('div');
    function onEventClick(ev){ if (state.options.detail === 'modal') openEventModal(ev); el.dispatchEvent(new CustomEvent('calendar:detail', { detail: ev })); }
    function onMore(day, dayEvents){ openDayList(day, dayEvents); }
    const ctx = { ...state.options, currentDate: state.currentDate, events, onEventClick, onMore };

    let viewRes = { title: '', cleanup: () => {} };
    switch(state.view){
      case 'week': viewRes = renderWeek(container, ctx); break;
      case 'day': viewRes = renderDay(container, ctx); break;
      case 'list': viewRes = renderList(container, ctx); break;
      case 'year': viewRes = renderYear(container, ctx); break;
      default: viewRes = renderMonth(container, ctx); break;
    }

    // Injecte toolbar + contenu
    buildToolbar();
    titleEl.textContent = viewRes.title;
    el.innerHTML = '';
    el.appendChild(header);
    el.appendChild(container);
  }

  function destroy(){ el.innerHTML=''; }

  return { render, destroy };
}

function rangeForView(view, currentDate, options){
  const d = new Date(currentDate);
  if (view === 'day'){
    const start = startOfDay(d); const end = addDays(start,1); return { start, end };
  }
  if (view === 'week' || view === 'list'){
    const start = startOfWeek(d, options.firstDay); const end = addDays(start,7); return { start, end };
  }
  if (view === 'year'){
    const start = new Date(d.getFullYear(), 0, 1); const end = new Date(d.getFullYear()+1, 0, 1); return { start, end };
  }
  // month
  const start = new Date(d.getFullYear(), d.getMonth(), 1); const end = new Date(d.getFullYear(), d.getMonth()+1, 1); return { start, end };
}

function parseJson(txt){ try { return JSON.parse(txt || ''); } catch(_) { return null; } }
function i18n(key){ try { return (window.daisyI18n && window.daisyI18n.calendar && window.daisyI18n.calendar[key]) || key; } catch(_) { return key; } }
function capitalize(s){ return String(s).charAt(0).toUpperCase() + String(s).slice(1); }

// Utilitaires dates
function startOfWeek(date, firstDay){ const d = new Date(date); const dow = d.getDay(); const diff = (dow - firstDay + 7) % 7; d.setDate(d.getDate() - diff); return d; }
function startOfDay(d){ const x = new Date(d); x.setHours(0,0,0,0); return x; }
function addDays(d, n){ const x = new Date(d); x.setDate(x.getDate()+n); return x; }
function openEventModal(ev){
  const dialog = document.createElement('dialog');
  dialog.className = 'modal modal-middle';
  dialog.innerHTML = `
    <div class="modal-box">
      <h3 class="text-lg font-bold mb-2">${escapeHtml(ev.title || 'Event')}</h3>
      <div class="mb-4 text-sm opacity-80">${formatRange(ev.start, ev.end, ev.allDay)}</div>
      <div class="mb-2">${ev.raw?.description ? escapeHtml(ev.raw.description) : ''}</div>
      <div class="modal-action">
        <form method="dialog"><button class="btn">OK</button></form>
      </div>
    </div>
    <form method="dialog" class="modal-backdrop"><button>close</button></form>`;
  document.body.appendChild(dialog);
  try { dialog.showModal(); } catch(_) { dialog.setAttribute('open',''); }
  dialog.addEventListener('close', () => dialog.remove());
}
function openDayList(day, events){
  const dialog = document.createElement('dialog');
  dialog.className = 'modal modal-middle';
  const items = events.map((e) => `<li class="py-1"><a class="link" href="#" data-id="${e.id||''}">${escapeHtml(e.title||'(untitled)')}</a></li>`).join('');
  dialog.innerHTML = `
    <div class="modal-box">
      <h3 class="text-lg font-bold mb-2">${day.toLocaleDateString(undefined,{ dateStyle:'full' })}</h3>
      <ul class="mb-4">${items || '<li class="opacity-70">No events</li>'}</ul>
      <div class="modal-action"><form method="dialog"><button class="btn">OK</button></form></div>
    </div>
    <form method="dialog" class="modal-backdrop"><button>close</button></form>`;
  document.body.appendChild(dialog);
  dialog.addEventListener('click', (e) => {
    const a = e.target.closest('a[data-id]'); if (!a) return; e.preventDefault(); const id = a.getAttribute('data-id'); const ev = events.find((x) => String(x.id||'') === id); if (ev) openEventModal(ev);
  });
  try { dialog.showModal(); } catch(_) { dialog.setAttribute('open',''); }
  dialog.addEventListener('close', () => dialog.remove());
}
function escapeHtml(s){ return String(s||'').replace(/[&<>"']/g, (c) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[c])); }
function formatRange(start, end, allDay){
  if (allDay){
    const s = start.toLocaleDateString(undefined, { dateStyle:'medium' });
    const e = end ? end.toLocaleDateString(undefined,{ dateStyle:'medium' }) : null;
    return e ? `${s} → ${e}` : s;
  }
  const s = `${start.toLocaleDateString(undefined,{ month:'short', day:'numeric' })} ${start.toLocaleTimeString(undefined,{ hour:'2-digit', minute:'2-digit' })}`;
  if (!end) return s;
  const sameDay = start.toDateString() === end.toDateString();
  const e = sameDay ? end.toLocaleTimeString(undefined,{ hour:'2-digit', minute:'2-digit' }) : `${end.toLocaleDateString(undefined,{ month:'short', day:'numeric' })} ${end.toLocaleTimeString(undefined,{ hour:'2-digit', minute:'2-digit' })}`;
  return `${s} – ${e}`;
}



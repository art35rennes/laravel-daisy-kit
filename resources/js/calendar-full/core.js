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
  const titleEl = document.createElement('h2'); titleEl.className = 'cf-title'; titleEl.setAttribute('aria-live', 'polite');
  const left = document.createElement('div'); left.className = 'join';
  const right = document.createElement('div'); right.className = 'tabs tabs-box'; right.setAttribute('role', 'tablist');
  header.appendChild(left); header.appendChild(titleEl); header.appendChild(right);

  function buildToolbar(){
    left.innerHTML = '';
    right.innerHTML = '';
    const prev = navigationButton('‹', i18n('previous')); prev.addEventListener('click', () => step(-1));
    const today = navigationButton(i18n('today'), i18n('today'), false); today.addEventListener('click', () => { state.currentDate = new Date(); render(); });
    const next = navigationButton('›', i18n('next')); next.addEventListener('click', () => step(1));
    left.appendChild(prev); left.appendChild(today); left.appendChild(next);
    const views = state.options.views;
    views.forEach((v) => {
      const b = document.createElement('button');
      b.type = 'button';
      b.className = 'tab';
      b.setAttribute('role', 'tab');
      b.setAttribute('aria-selected', String(v === state.view));
      b.textContent = i18n(v);
      if (v === state.view) b.classList.add('tab-active');
      b.addEventListener('click', () => { state.view = v; render(); });
      right.appendChild(b);
    });
  }

  function navigationButton(text, label, square = true){
    const button = document.createElement('button');
    button.type = 'button';
    button.className = `btn btn-sm join-item${square ? ' btn-square' : ''}`;
    button.textContent = text;
    button.setAttribute('aria-label', label);

    return button;
  }

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

    // Construire le contenu
    const container = document.createElement('div');
    container.className = 'cf-content';
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
function i18n(key){ try { return (window.daisyI18n && window.daisyI18n.calendar && window.daisyI18n.calendar[key]) || capitalize(key); } catch(_) { return capitalize(key); } }
function capitalize(s){ return String(s).charAt(0).toUpperCase() + String(s).slice(1); }

// Utilitaires dates
function startOfWeek(date, firstDay){ const d = new Date(date); const dow = d.getDay(); const diff = (dow - firstDay + 7) % 7; d.setDate(d.getDate() - diff); return d; }
function startOfDay(d){ const x = new Date(d); x.setHours(0,0,0,0); return x; }
function addDays(d, n){ const x = new Date(d); x.setDate(x.getDate()+n); return x; }
function openEventModal(ev){
  const { dialog, box } = createDialog(ev.title || 'Event');
  const range = document.createElement('p');
  range.className = 'mb-4 text-sm text-base-content/70';
  range.textContent = formatRange(ev.start, ev.end, ev.allDay);
  box.appendChild(range);

  if (ev.raw?.description) {
    const description = document.createElement('p');
    description.className = 'mb-2';
    description.textContent = String(ev.raw.description);
    box.appendChild(description);
  }

  box.appendChild(createModalActions());
  document.body.appendChild(dialog);
  try { dialog.showModal(); } catch(_) { dialog.setAttribute('open',''); }
  dialog.addEventListener('close', () => dialog.remove());
}
function openDayList(day, events){
  const { dialog, box } = createDialog(day.toLocaleDateString(undefined, { dateStyle: 'full' }));
  const list = document.createElement('ul');
  list.className = 'list mb-4';

  events.forEach(event => {
    const item = document.createElement('li');
    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'list-row w-full text-start hover:bg-base-200';
    button.textContent = event.title || '(untitled)';
    button.addEventListener('click', () => openEventModal(event));
    item.appendChild(button);
    list.appendChild(item);
  });

  if (!events.length) {
    const empty = document.createElement('li');
    empty.className = 'p-4 text-base-content/60';
    empty.textContent = i18n('no_events');
    list.appendChild(empty);
  }

  box.appendChild(list);
  box.appendChild(createModalActions());
  document.body.appendChild(dialog);
  try { dialog.showModal(); } catch(_) { dialog.setAttribute('open',''); }
  dialog.addEventListener('close', () => dialog.remove());
}

function createDialog(title){
  const dialog = document.createElement('dialog');
  dialog.className = 'modal modal-middle';
  const box = document.createElement('div');
  box.className = 'modal-box';
  const heading = document.createElement('h3');
  heading.className = 'mb-2 text-lg font-bold';
  heading.textContent = title;
  const backdrop = document.createElement('form');
  backdrop.method = 'dialog';
  backdrop.className = 'modal-backdrop';
  const close = document.createElement('button');
  close.type = 'submit';
  close.setAttribute('aria-label', i18n('close'));
  backdrop.appendChild(close);
  box.appendChild(heading);
  dialog.appendChild(box);
  dialog.appendChild(backdrop);

  return { dialog, box };
}

function createModalActions(){
  const actions = document.createElement('div');
  actions.className = 'modal-action';
  const form = document.createElement('form');
  form.method = 'dialog';
  const button = document.createElement('button');
  button.type = 'submit';
  button.className = 'btn';
  button.textContent = i18n('close');
  form.appendChild(button);
  actions.appendChild(form);

  return actions;
}
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

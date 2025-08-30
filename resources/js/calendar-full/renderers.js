import { toIsoDate, intersects } from './data';

/**
 * RENDERERS — Chaque fonction remplit containerEl et renvoie un objet { title, cleanup }
 * Les fonctions ne font pas d'IO, elles consomment déjà les évènements fournis.
 */

export function renderMonth(container, ctx){
  const { currentDate, events, firstDay } = ctx;
  const year = currentDate.getFullYear();
  const month = currentDate.getMonth();
  const start = startOfMonthGrid(year, month, firstDay);
  const end = addDays(start, 42); // 6 semaines

  const days = [];
  for (let d = new Date(start); d < end; d = addDays(d,1)) days.push(new Date(d));

  const frag = document.createDocumentFragment();
  const grid = el('div','cf-grid cf-month');
  frag.appendChild(toolbarSpacer(ctx));
  frag.appendChild(grid);

  const todayKey = toIsoDate(new Date());
  days.forEach((day,i) => {
    const cell = el('div','cf-cell');
    if (toIsoDate(day) === todayKey) cell.classList.add('is-today');
    const dateEl = el('div','cf-date');
    dateEl.textContent = String(day.getDate());
    cell.appendChild(dateEl);
    const dayEvents = events.filter((e) => intersectsDay(e, day));
    let shown = 0; const max = 3;
    for (const ev of dayEvents) {
      if (shown >= max) break;
      cell.appendChild(eventChip(ev, ctx));
      shown++;
    }
    if (dayEvents.length > shown) {
      const more = el('a','cf-more link');
      more.textContent = `+${dayEvents.length - shown} more`;
      more.href = '#';
      more.addEventListener('click', (e) => { e.preventDefault(); ctx.onMore(day, dayEvents); });
      cell.appendChild(more);
    }
    grid.appendChild(cell);
  });

  container.innerHTML = '';
  container.appendChild(frag);
  return { title: monthLabel(year, month), cleanup(){} };
}

export function renderWeek(container, ctx){
  const { currentDate, events, hourStart, hourEnd, firstDay } = ctx;
  const start = startOfWeek(currentDate, firstDay);
  const end = addDays(start, 7);

  const frag = document.createDocumentFragment();
  frag.appendChild(toolbarSpacer(ctx));
  const grid = el('div','cf-grid cf-week');
  frag.appendChild(grid);

  // En-tête heures colonne 0
  const hoursCol = el('div');
  hoursCol.style.display = 'grid';
  hoursCol.style.gridTemplateRows = `repeat(${hourEnd-hourStart}, 3rem)`;
  for (let h = hourStart; h < hourEnd; h++) {
    const hr = el('div','cf-hour');
    hr.textContent = `${h}:00`;
    hoursCol.appendChild(hr);
  }
  grid.appendChild(hoursCol);

  // 7 colonnes jour
  for (let i=0;i<7;i++){
    const col = el('div');
    col.style.position = 'relative';
    col.style.display = 'grid';
    col.style.gridTemplateRows = `repeat(${hourEnd-hourStart}, 3rem)`;
    const day = addDays(start,i);
    const dayEvents = events.filter((e) => e.allDay ? intersectsDay(e, day) : intersects(e, day, addDays(day,1)));
    // Slots horizontaux (lignes)
    for (let h = hourStart; h < hourEnd; h++) col.appendChild(el('div','cf-slot'));
    // Blocs horaires
    for (const ev of dayEvents) {
      if (ev.allDay) continue; // all-day non géré visuel ici pour simplicité
      const top = posFromTime(maxDate(ev.start, day)) - hourStart;
      const bottom = posFromTime(minDate(ev.end || ev.start, addDays(day,1))) - hourStart;
      const block = el('div','cf-block');
      block.style.top = `${(top) * 3}rem`;
      block.style.height = `${Math.max(0.8, bottom - top) * 3}rem`;
      if (ev.color) block.style.background = ev.color;
      // Contenu enrichi via template "block"
      const tpl = queryEventTemplate('block');
      const payload = {
        title: ev.title || '(untitled)',
        timeRange: formatDateTime(ev.start, ev.end)
      };
      block.appendChild(applyEventTemplate(tpl, payload));
      block.addEventListener('click', () => ctx.onEventClick(ev));
      col.appendChild(block);
    }
    grid.appendChild(col);
  }

  container.innerHTML = '';
  container.appendChild(frag);
  return { title: weekLabel(start), cleanup(){} };
}

export function renderDay(container, ctx){
  // Utilise renderWeek mais centré sur un seul jour
  const tmp = { ...ctx };
  const node = el('div');
  container.innerHTML = '';
  container.appendChild(node);
  const { title, cleanup } = renderWeek(node, { ...tmp, currentDate: tmp.currentDate });
  return { title: dayLabel(tmp.currentDate), cleanup };
}

export function renderList(container, ctx){
  const { currentDate, events } = ctx;
  const start = startOfWeek(currentDate, ctx.firstDay);
  const end = addDays(start,7);
  const list = el('div','cf-list');
  const frag = document.createDocumentFragment();
  frag.appendChild(toolbarSpacer(ctx));
  const sorted = events.filter((e) => intersects(e,start,end))
    .sort((a,b) => (a.start - b.start));
  if (!sorted.length){
    const p = el('p','opacity-70'); p.textContent = 'No events'; list.appendChild(p);
  } else {
    for (const ev of sorted){
      const li = el('div','cf-li');
      const tpl = queryEventTemplate('list');
      const payload = { title: ev.title || '(untitled)', timeRange: formatDateTime(ev.start, ev.end) };
      li.appendChild(applyEventTemplate(tpl, payload));
      li.addEventListener('click', () => ctx.onEventClick(ev));
      list.appendChild(li);
    }
  }
  frag.appendChild(list);
  container.innerHTML = '';
  container.appendChild(frag);
  return { title: weekLabel(start), cleanup(){} };
}

export function renderYear(container, ctx){
  const { currentDate, firstDay } = ctx;
  const year = currentDate.getFullYear();
  const frag = document.createDocumentFragment();
  frag.appendChild(toolbarSpacer(ctx));
  const wrap = el('div','cf-grid');
  wrap.style.display = 'grid';
  wrap.style.gridTemplateColumns = 'repeat(4, minmax(0, 1fr))';
  wrap.style.gap = '.75rem';
  for (let m=0;m<12;m++){
    const box = el('div');
    const title = el('div','text-center font-medium mb-1');
    title.textContent = monthLabel(year,m);
    const monthGrid = el('div','cf-grid cf-month');
    const start = startOfMonthGrid(year,m,firstDay);
    const end = addDays(start, 42);
    for (let d = new Date(start); d < end; d = addDays(d,1)){
      const cell = el('div','cf-cell');
      const dateEl = el('div','cf-date'); dateEl.textContent = String(d.getDate());
      cell.appendChild(dateEl);
      monthGrid.appendChild(cell);
    }
    box.appendChild(title); box.appendChild(monthGrid);
    wrap.appendChild(box);
  }
  container.innerHTML = '';
  frag.appendChild(wrap); // Important: insérer wrap dans le fragment AVANT son insertion dans le DOM
  container.appendChild(frag);
  return { title: String(year), cleanup(){} };
}

/** UTILITAIRES **/
function el(tag, cls){ const n = document.createElement(tag); if (cls) n.className = cls; return n; }
function addDays(d, n){ const x = new Date(d); x.setDate(x.getDate()+n); return x; }
function startOfMonthGrid(year, month, firstDay){
  const d = new Date(year, month, 1);
  const dow = d.getDay();
  const diff = (dow - firstDay + 7) % 7;
  d.setDate(d.getDate() - diff);
  return d;
}
function startOfWeek(date, firstDay){
  const d = new Date(date); const dow = d.getDay();
  const diff = (dow - firstDay + 7) % 7; d.setDate(d.getDate() - diff); return d;
}
function monthLabel(year, month){
  return new Date(year, month, 1).toLocaleDateString(undefined, { month:'long', year:'numeric' });
}
function dayLabel(date){ return date.toLocaleDateString(undefined, { dateStyle: 'full' }); }
function weekLabel(start){
  const end = addDays(start,6);
  const opts = { month:'short', day:'numeric' };
  return `${start.toLocaleDateString(undefined, opts)} – ${end.toLocaleDateString(undefined, opts)}, ${start.getFullYear()}`;
}
function posFromTime(d){ return d.getHours() + d.getMinutes()/60; }
function maxDate(a,b){ return a > b ? a : b; }
function minDate(a,b){ return a < b ? a : b; }
function escapeHtml(s){ return String(s).replace(/[&<>"]/g, (c) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c])); }
function formatDateTime(start, end){
  const sameDay = !end || (start.toDateString() === end.toDateString());
  const d = start.toLocaleDateString(undefined, { weekday:'short', month:'short', day:'numeric' });
  const t = start.toLocaleTimeString(undefined, { hour:'2-digit', minute:'2-digit' });
  if (!end) return `${d} ${t}`;
  const te = end.toLocaleTimeString(undefined, { hour:'2-digit', minute:'2-digit' });
  return sameDay ? `${d} ${t} – ${te}` : `${d} ${t} → ${end.toLocaleDateString(undefined,{ month:'short', day:'numeric' })} ${te}`;
}
function intersectsDay(ev, day){
  const start = new Date(day); const end = addDays(day,1);
  const s = ev.start; const e = ev.end || ev.start;
  return s < end && e >= start;
}

function eventChip(ev, ctx){
  const a = el('a','cf-event');
  if (ev.color) a.style.background = ev.color;
  a.href = ev.url || '#';
  const tpl = queryEventTemplate('chip');
  const payload = { title: ev.title || '(untitled)', dotColor: ev.color || 'currentColor' };
  a.appendChild(applyEventTemplate(tpl, payload));
  a.addEventListener('click', (e) => { if (!ev.url) e.preventDefault(); ctx.onEventClick(ev); });
  return a;
}

// Recherche le <template data-calendar-event="kind"> le plus proche (dans le DOM/document)
function queryEventTemplate(kind){
  const sel = `template[data-calendar-event="${kind}"]`;
  return document.querySelector(sel);
}

// Clône et remplace les {{placeholders}}
function applyEventTemplate(tpl, payload){
  const span = el('span');
  if (!tpl) { span.textContent = payload.title || ''; return span; }
  const node = tpl.content.cloneNode(true);
  const html = nodeToHtml(node).replace(/\{\{(\w+)\}\}/g, (_, k) => k in payload ? String(payload[k]) : '');
  span.innerHTML = html;
  return span.firstElementChild ? span.firstElementChild : span;
}
function nodeToHtml(node){ const div = document.createElement('div'); div.appendChild(node); return div.innerHTML; }

function toolbarSpacer(ctx){
  // Les barres d'outils sont construites dans core.js. Ce noeud est un espace réservé.
  const d = el('div'); d.className = 'cf-toolbar-space'; return d;
}



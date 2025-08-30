import { postTask } from '../utils/scheduler';

/**
 * Source d'évènements à partir d'un tableau statique
 * @param {Array} events
 */
export function createStaticSource(events){
  const data = Array.isArray(events) ? events.slice() : [];
  // Normalise et pré-parse les dates pour limiter les allocations lors des filtrages
  const norm = data.map((e) => normalizeEvent(e));
  return {
    async loadRange(start, end){
      // Filtrage simple: intersect(start,end)
      const res = norm.filter((e) => intersects(e, start, end));
      return res.map((e) => ({ ...e }));
    }
  };
}

/**
 * Source d'évènements asynchrone via HTTP. Réponses JSON tableau d'évènements.
 * L'API reçoit ?start=YYYY-MM-DD&end=YYYY-MM-DD (inclusifs côté affichage)
 */
export function createHttpSource(url){
  let controller = null;
  const cache = new Map(); // clé: start|end → events
  return {
    async loadRange(start, end){
      const key = `${toIsoDate(start)}|${toIsoDate(end)}`;
      if (cache.has(key)) return cache.get(key).map((e) => ({ ...e }));
      if (controller) controller.abort();
      controller = new AbortController();
      const u = new URL(url, window.location.origin);
      u.searchParams.set('start', toIsoDate(start));
      u.searchParams.set('end', toIsoDate(end));
      const res = await fetch(u, { signal: controller.signal, headers: { Accept: 'application/json' } });
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const data = await res.json();
      // normalisation en amont
      const norm = Array.isArray(data) ? data.map((e) => normalizeEvent(e)) : [];
      cache.set(key, norm);
      return norm.map((e) => ({ ...e }));
    }
  };
}

/** Helpers communs **/
export function normalizeEvent(e){
  const start = toDate(e.start);
  const end = e.end ? toDate(e.end) : null;
  return {
    id: e.id ?? null,
    title: e.title ?? '',
    start,
    end,
    allDay: Boolean(e.allDay),
    url: e.url || null,
    color: e.color || null,
    data: e.data || null,
    raw: e,
  };
}

export function intersects(e, start, end){
  const s = e.start; const t = e.end || e.start;
  return s < end && t >= start; // intervalle [s,t) intersecte [start,end)
}

export function toIsoDate(d){ return d.toISOString().slice(0,10); }
export function toDate(v){ if (v instanceof Date) return v; return new Date(v); }



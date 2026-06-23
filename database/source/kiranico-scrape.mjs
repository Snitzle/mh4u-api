// Regenerates database/source/kiranico.json — the "universal gap" fields
// (HP, crowns, enraged, limp/capture, stagger limits, trap durations, ecology)
// scraped from Kiranico MH4U, keyed by our monster id. Consumed by the
// KiranicoTopUpSeeder. Data is MIT (Kiranico-lineage); monster names/art are
// Capcom's. See README attribution.
//
// Usage (needs the API running so monsters can be paired by name, and network):
//   node database/source/kiranico-scrape.mjs            # fetch + write json
//   API_BASE=http://localhost:8088/api/v1 node database/source/kiranico-scrape.mjs
//
// One GET per monster, with a browser User-Agent (Kiranico 403s the default UA)
// and a polite delay. The data is a static game DB, so the committed snapshot
// rarely needs refreshing.
import { writeFileSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const HERE = dirname(fileURLToPath(import.meta.url));
const UA = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36';
const API = process.env.API_BASE ?? 'http://localhost:8088/api/v1';
const sleep = (ms) => new Promise((r) => setTimeout(r, ms));
const norm = (s) => String(s ?? '').toLowerCase().replace(/[^a-z0-9]/g, '');
const num = (v) => (v === null || v === undefined || v === '' || Number(v) === 0 ? null : Number(v));

async function fetchHtml(slug) {
  const res = await fetch(`https://kiranico.com/en/mh4u/monster/${slug}`, {
    headers: { 'User-Agent': UA, Accept: 'text/html', 'Accept-Language': 'en-US,en;q=0.9' },
  });
  if (!res.ok) throw new Error(`Kiranico ${res.status}`);
  return res.text();
}
function extractJsVars(html) {
  const at = html.search(/js_vars\s*=\s*\{/);
  if (at === -1) return null;
  const start = html.indexOf('{', at);
  let depth = 0, inStr = false, q = '', esc = false, i = start;
  for (; i < html.length; i++) {
    const c = html[i];
    if (inStr) { if (esc) esc = false; else if (c === '\\') esc = true; else if (c === q) inStr = false; }
    else if (c === '"' || c === "'") { inStr = true; q = c; }
    else if (c === '{') depth++;
    else if (c === '}' && --depth === 0) { i++; break; }
  }
  try { return JSON.parse(html.slice(start, i)).monster; } catch { return null; }
}
function ecology(html) {
  const m = html.match(/data-swiftype-name="body"[^>]*>([\s\S]*?)<\/p>/);
  return m ? m[1].replace(/<[^>]+>/g, '').replace(/\s+/g, ' ').trim() || null : null;
}

const listHtml = await fetchHtml('').catch(() => null) ?? await (await fetch('https://kiranico.com/en/mh4u/monster', { headers: { 'User-Agent': UA } })).text();
const slugs = [...new Set([...listHtml.matchAll(/\/en\/mh4u\/monster\/([a-z0-9-]+)/g)].map((m) => m[1]))];

const apiMonsters = [];
for (let page = 1, last = 1; page <= last; page++) {
  const j = await (await fetch(`${API}/monsters?lang=en&per_page=100&page=${page}`)).json();
  apiMonsters.push(...j.data);
  last = j.meta.last_page;
}
const idByName = new Map(apiMonsters.map((m) => [norm(m.name), m.id]));

const out = {};
let staggerRows = 0, trapRows = 0, paired = 0, missing = [];
for (const slug of slugs) {
  const id = idByName.get(slug.replace(/-/g, ''));
  if (!id) { missing.push(slug); continue; }
  await sleep(300);
  const html = await fetchHtml(slug).catch(() => null);
  const k = html && extractJsVars(html);
  if (!k) { missing.push(slug); continue; }
  paired++;
  const stagger = (k.monsterstaggerlimits || []).map((s, i) => ({ region: s.region, value: num(s.value), value_cut: num(s.value_cut), extract_color: s.extract_color ?? null, sort_order: i }));
  const traps = (k.itemeffects || []).map((t, i) => ({ trap: t.local_name, normal: num(t.pivot?.normal), enraged: num(t.pivot?.enraged), fatigued: num(t.pivot?.fatigued), sort_order: i }));
  if (k.canopytrap) traps.push({ trap: 'Canopy Trap', normal: num(k.canopytrap.normal), enraged: num(k.canopytrap.enraged), fatigued: num(k.canopytrap.fatigued), sort_order: traps.length });
  staggerRows += stagger.length;
  trapRows += traps.length;
  out[id] = {
    name: apiMonsters.find((m) => m.id === id)?.name, slug,
    base_hp: num(k.base_hp),
    hp_mult_low: num(k.hp_mult_low), hp_mult_high: num(k.hp_mult_high), hp_mult_g: num(k.hp_mult_g),
    crown_mini: num(k.crown_miniature), crown_large: num(k.crown_large), crown_king: num(k.crown_king),
    size_class: k.size ?? null,
    rage_duration: num(k.rage_duration), rage_mod_attack: num(k.rage_mod_attack), rage_mod_defense: num(k.rage_mod_defense), rage_mod_speed: num(k.rage_mod_speed),
    limp_low: num(k.limp_low), limp_high: num(k.limp_high), limp_high_apex: num(k.limp_high_apex), limp_g: num(k.limp_g), limp_g_apex: num(k.limp_g_apex),
    cap_low: num(k.cap_low), cap_high: num(k.cap_high), cap_high_apex: num(k.cap_high_apex), cap_g: num(k.cap_g), cap_g_apex: num(k.cap_g_apex),
    ecology: ecology(html),
    stagger, traps,
  };
}

writeFileSync(join(HERE, 'kiranico.json'), JSON.stringify(out, null, 1));
console.log(`paired ${paired}/${slugs.length} monsters | stagger rows ${staggerRows} | trap rows ${trapRows}`);
if (missing.length) console.log('no Kiranico data for slugs:', missing.join(', '));

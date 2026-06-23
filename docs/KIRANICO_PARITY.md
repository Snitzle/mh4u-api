# Handoff prompt — Kiranico data-parity audit

> Paste everything below into a fresh Claude Code session on the machine that has the
> **Claude-in-Chrome** extension connected. It is self-contained.

---

You are continuing the **MH4U Database** project — a modernization of a legacy iOS Monster
Hunter 4 Ultimate database into three repos. Everything below is already built, committed and
pushed to GitHub (account **Snitzle**). **This session's job: audit our data against Kiranico
(the agreed source of truth) and bring our database to data parity**, using the Claude-in-Chrome
extension (this machine has it) to read Kiranico pages.

## Repos
- **mh4u-api** — https://github.com/Snitzle/mh4u-api — Laravel 13 (PHP 8.3+) public read-only REST API. **Owns the data.**
- **mh4u-web** — https://github.com/Snitzle/mh4u-web — Next.js 16 web client.
- **mh4u-mobile** — https://github.com/Snitzle/mh4u-mobile — Expo 56 / React Native mobile client.

Clone all three as siblings (e.g. `~/Sites/mh4u-api`, `~/Sites/mh4u-web`, `~/Sites/mh4u-mobile`).

## Standing rules
- Every feature / data change ships to **both web and mobile** (and the API).
- Commit + push **every logical step** to each affected repo. End commit messages with `Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>`.
- mh4u-api quality gates must stay green: `composer ci` (Pint + Larastan level 6 + Pest). Use `php artisan make:` generators and run `vendor/bin/pint --dirty` after editing PHP (Laravel Boost guidelines are in mh4u-api/AGENTS.md).

## Run locally
- **API:** `cd mh4u-api && composer setup` (creates SQLite at `database/database.sqlite`, migrates + seeds from the vendored `database/source/mh4u.db`, syncs assets, generates docs). Then `php artisan serve --port=8088`. Local dev is **SQLite, no Docker**. In `.env` set `MH4U_ASSET_BASE_URL=http://localhost:8088/assets` so image URLs resolve.
- **Web:** `cd mh4u-web && cp .env.example .env.local && npm install && npm run dev` → localhost:3000 (expects API on :8088).
- **Mobile:** `cd mh4u-mobile && npm install && npx expo start --ios` (iOS simulator reaches the API on localhost:8088).

## Data model (mh4u-api) — monster-related tables
Imported from a vendored SQLite DB (`database/source/mh4u.db`, ~79k rows, a Kiranico-lineage
open Android MH4U dataset) via `Database\Seeders\Mh4uImportSeeder` into a normalized schema:
- `monsters` (id, class, name + name_de/fr/es/it/jp, signature_move, trait, icon_name, sort_name)
- `monster_damage` — hitzones (monster_id, body_part, cut, impact, shot, fire, water, ice, thunder, dragon, ko)
- `monster_weaknesses` (monster_id, **state**, fire, water, thunder, ice, dragon, poison, paralysis, sleep, pitfall_trap, shock_trap, flash_bomb, sonic_bomb, dung_bomb, meat) — **most monsters only have a "Normal" state; only 6 have Enraged/Charged**
- `monster_statuses` (monster_id, status, initial, increase, max, duration, damage)
- `monster_ailments` (monster_id, ailment) — ailments the monster **inflicts**
- `monster_habitats`; `monster_quest` pivot (with `unstable`); `hunting_rewards` (item_id, monster_id, condition, rank LR/HR/G, stack_size, percentage)
Equipment uses shared-PK inheritance: `weapons.id == armor.id == decorations.id == items.id`.
Skills via `item_skill_tree` (pivot point_value). Filters already exist on the API for quests
(stars, monster), weapons (wtype, element, rarity), armor (slot, hunter_type, rarity, skill), items (type, rarity).

## What's already verified (Rathalos = monster **72**)
Our API matches Kiranico on the **core combat data**: 7 hitzones, weakness effectiveness
(dragon = 3 / most-weak, fire = 0 / immune — correct), 8 status tolerances, 96 rank rewards with
break conditions, habitats, 28 quests. The earlier "missing data" was a **rendering gap, now
fixed** — web (`mh4u-web/src/app/monsters/[id]/page.tsx`) and mobile
(`mh4u-mobile/src/app/monsters/[id].tsx`) now render hitzone tables, status-tolerance tables,
full weaknesses (elements + status + traps/bombs), and inflicted ailments. So **do not assume the
DB is empty** — confirm gaps before importing.

## Likely GENUINE gaps vs Kiranico (confirm, then fill)
1. **Monster size / crown thresholds** (smallest / largest) — not modelled at all.
2. **Per-state hitzones** — `monster_damage` has no state column (usually fine for MH4U).
3. **Reward %s / quantities** — spot-check a sample against Kiranico.
4. **Multi-state weaknesses** — confirm monsters that gain an element weakness when enraged aren't missing an Enraged row.
5. **Ecology / description text** — minor, not modelled.
Spot-check a few items/weapons/armor too if time allows.

## Task
1. **Read Kiranico via the browser extension** (`mcp__claude-in-chrome__navigate` then `get_page_text` / `read_page`). Kiranico returns **403 to plain HTTP fetch**, so the real browser is required. Sample: a flagship (Rathalos `…/monster/rathalos`), a couple of small monsters, and a couple that enrage.
2. **Diff** Kiranico vs our API (`curl 'http://localhost:8088/api/v1/monsters/{id}?lang=en'`) field-by-field; record genuine gaps.
3. **Implement**: new migration(s)/columns (e.g. a `monster_sizes` table or columns on `monsters`), a Kiranico-import or top-up seeder/command (idempotent), then re-seed. Extend `tests/Feature/SeederIntegrityTest.php` with the new expectations.
4. **Surface** any new fields on web + mobile monster detail (standing rule), with API resources + types updated.
5. Keep `composer ci` green; commit + push each step.

## Notes
- Capcom IP: code/data are MIT (Kiranico-lineage open dataset); names/art are Capcom's — keep attribution, non-commercial posture.
- API docs: `/docs` (Scribe), OpenAPI at `/docs.openapi`, committed spec at `mh4u-api/docs/openapi.yaml`.

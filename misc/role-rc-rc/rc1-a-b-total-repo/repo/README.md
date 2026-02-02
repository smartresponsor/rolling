# SmartResponsor / Role — RC1..RC2 Repository (working tree)

Это рабочее дерево для компонента **Role** (канон: single-hyphen, EN-комментарии, mirror interface layers).
Киты A/B уже распакованы в `../kits/`. Этот `repo/` — место, куда интегрируются изменения.

## Быстрый старт

```bash
git init
git checkout -b master
git add .
git commit -m "role: repo scaffold (rc1..rc2 kits ready)"
```

### Интеграция кита в рабочее дерево

```bash
# пример: интегрировать роль-кийт B1 (кеш PDP v2)
./tools/integrate-kit.sh ../kits/B/kits/role-rc-b1-cached-pdp-v2
git add -A && git commit -m "role: integrate B1 (cached pdp v2)"
```

### Смоук/бенчи

```bash
# b4 бенчи
./tools/run_bench.sh
# b3 перф CLI
php bin/role-batch-perf.php 1000 100
```

### TS SDK build

```bash
cd sdk/js
npm i
npm run build
```

## Канон (памятка)

- **no plurals** в путях/классах;
- зеркала слоёв `<Layer>` ↔ `<Layer Interface>`;
- комментарии и сообщения — EN;
- без встроенного fast-import; git-коммиты создаём обычным add/commit.

# RC-B5 — SDK polish

## TS SDK (ESM + d.ts + tree-shaking)

1. Смерджи `sdk/js/package.json.merge.json` в свой `sdk/js/package.json` (или замени
   поля `type`, `exports`, `files`, `scripts`, `devDependencies`).
2. Добавь `sdk/js/tsconfig.build.json`. Если нет `tsconfig.json`, создай по проекту.
3. Запусти:

```bash
cd sdk/js
npm i
npm run build
ls -l dist/
```

Ожидаемо: `dist/index.js`, `dist/index.d.ts`.

## PHP SDK (исключения)

- Добавь классы из `sdk/php/src/Exception/*` и `sdk/php/src/Http/ResponseErrorMapper.php`.
- В клиенте перед десериализацией вызывай `ResponseErrorMapper::throwOnError($res)`.

## Acceptance

- `npm run build` сгенерировал d.ts и ESM-артефакты.
- PHP-клиент кидает нужные исключения по 401/403/429/5xx.

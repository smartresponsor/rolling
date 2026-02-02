# RC-B5 — SDK polish (TS ESM + d.ts + tree-shaking, PHP typed exceptions)

## Что входит

- **TS SDK**: ESM-билд, `sideEffects:false` для tree-shaking, генерация `d.ts`, `exports`/`types`.
- **PHP SDK**: типизированные исключения + маппинг HTTP статусов/заголовков.

## Acceptance

- TS: `npm run build` создаёт `dist/` с `index.js` и `index.d.ts`; в `package.json`
  есть `type:module`, `exports`, `sideEffects:false`.
- PHP: при 401/403/429/5xx кидаются соответствующие исключения; покрыто тестом или smoke-скриптом.

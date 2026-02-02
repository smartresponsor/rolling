# TS SDK build

```bash
cd sdk/js
npm i
npm run build
# итог: dist/index.js, dist/index.d.ts
```

Проверь tree-shaking (пример с vite/rollup): за счёт `sideEffects:false` неиспользуемые экспорты должны выкидываться.

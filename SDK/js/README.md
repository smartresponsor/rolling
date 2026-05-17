# SmartResponsor Role — TS SDK

## Установка

```bash
npm i @smartresponsor/role-sdk
# или локально: скопируйте SDK/js и соберите tsc
```

## Использование (Node)

```ts
import { Client } from '@smartresponsor/role-sdk';

const client = new Client('https://pdp.internal', { apiKey: process.env.ROLE_PDP_API_KEY, hmacSecret: process.env.ROLE_PDP_HMAC });
const res = await client.check({
  subjectId: 'u1',
  action: 'message.read',
  scopeType: 'tenant',
  tenantId: 't1'
});
console.log(res.decision);
```

## Использование (Browser)

Передавайте `hmacSecret` только если вы доверяете среде (обычно **нельзя** держать секрет в браузере).
Если нужно — проксируйте запросы через бэкенд.

Браузерное HMAC использует WebCrypto и возвращает Promise.

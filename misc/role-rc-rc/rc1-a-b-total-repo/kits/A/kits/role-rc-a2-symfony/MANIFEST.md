# RC-A2 — Symfony wiring (Bundle + routes + /metrics)

## Вход в проект

- Скопируй файлы `config/**` и `src/**` поверх своего приложения (или вручную смерджи).
- В `config/bundles.php` добавь:

```php
<?php
return [
  // ...
  App\Integration\Symfony\RoleBundle\RoleBundle::class => ['all' => true],
];
```

- Проверь переменные окружения: `ROLE_PDP_BASE_URL`, `ROLE_PDP_API_KEY`, `ROLE_PDP_HMAC`, `ROLE_AUDIT_DSN`.

## Что добавлено

- Маршруты `/v2/access/check` и `/v2/access/check:batch`.
- Контроллер `/metrics` с `PrometheusExporter`.
- Пример `role.yaml`.

## Acceptance (проверка)

- `POST /v2/access/check` → 200 JSON (ALLOW/DENY — плейсхолдер ок).
- `GET /metrics` содержит `role_pdp_requests_total`.

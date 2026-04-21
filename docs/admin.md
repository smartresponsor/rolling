# Admin plane & signing (RC-D4)

## Endpoints

- `GET /v2/admin/keys/jwks` — JWKS (RSA public keys for RS256), содержит активный и следующий ключ (`kid`).
- `POST /v2/admin/keys/rotate` — промоут `next -> active`, генерирует новый `next`. **Требует** хедеры:
    - `X-Role-Admin: owner|operator`
    - `X-Role-Admin-Secret: <value from var/admin_secret.txt>`

## Key management

- Доступ к ключам в `var/keys/{active.pem, active.pub, active.kid, next.*}`.
- JWKS генерируется в `var/keys/jwks.json`.

## Bundle verification

- `App\Rolling\Security\Role\Keys\BundleVerifier::verify($payload, $sigB64, $kid, $notAfter)`
    - Подпись — RSA-SHA256 (PKCS#1 v1.5), `sigB64` — Base64.
    - Если указан `$notAfter`, истёкшая подпись отклоняется.

## CLI

```bash
bash tools/admin_secret_init.sh          # создать admin_secret.txt
php tools/keys_rotate.php                # ротация ключей без HTTP
bash tools/rc_d4_smoke.sh                # быстрая проверка
```

## Безопасность

- Минимальная проверка администратора: секретный заголовок + роль. В бою подключите полноценную аутентификацию (
  JWT/OIDC) и маппинг ролей.
- Импорты политик/бандлов должны требовать подпись активным ключом (см. `BundleVerifier`).

# SDK sanity

### PHP

Установи HTTP-стеки (однократно):

```bash
composer require guzzlehttp/guzzle:^7 guzzlehttp/psr7:^2 http-interop/http-factory-guzzle:^1
```

Запуск:

```bash
ROLE_PDP_BASE_URL=http://localhost:8000 \
php examples/php/check.php | tee report/php_sdk.json
```

### Node/TS

Подготовка:

```bash
cd examples/js
npm i
cd -
```

Запуск:

```bash
ROLE_PDP_BASE_URL=http://localhost:8000 \
node --loader ts-node/esm examples/js/check.ts | tee report/ts_sdk.json
```

### Автосмоук

```bash
chmod +x tools/run_sdk_sanity.sh
./tools/run_sdk_sanity.sh
# или PowerShell: ./tools/run_sdk_sanity.ps1
```

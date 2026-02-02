# RC-A1 — Smoke + Lint + Tests Kit

Запуск из корня проекта (где лежит `composer.json`, `src/`, `tests/` и т.п.).
Результаты пишутся в `report/smoke.txt` и `report/smoke.json`.

## Unix/macOS

```bash
chmod +x tools/smoke.sh
./tools/smoke.sh
```

## Windows (PowerShell 5+)

```powershell
Set-ExecutionPolicy -Scope Process Bypass -Force
./tools/smoke.ps1
```

Скрипт делает:

1. `php -l` по всем `*.php` в `src/`, `sdk/php/`, `tests/`, `bin/` (если есть).
2. Запускает PHPUnit, если найден `vendor/bin/phpunit` или `phpunit` в PATH.
3. Формирует сводку и статусы accept/reject по критериям RC-A1.

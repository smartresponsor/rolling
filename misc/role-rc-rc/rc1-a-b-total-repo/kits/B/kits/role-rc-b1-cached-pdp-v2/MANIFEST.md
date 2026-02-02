# RC-B1 — CachedPdpV2 (реальный кеш + инвалидации)

## Что добавлено

- Реализация `CachedPdpV2` с ключом: `v2:{sid}:{scope}:{action}:ctx:{sha256}:se:{epoch}`.
- Нормализация контекста (детерминированный JSON → sha256).
- Инвалидации через `SubjectEpochs::bump($subjectId)`.
- Кеш-стор: `KeyValueCache` + `InMemoryCache` (TTL/истечение).
- Адаптер `Psr16CacheAdapter` (используй, если есть PSR-16).
- Тесты: hit/miss, bypass на obligations, инвалидация bump().
- Смоук-скрипты.

## Acceptance

- ≥80% кеш-хит на повторных идентичных запросах.
- `bump(subject)` инвалидирует ключи этого субъекта.
- При `obligations != []` — байпас кеша.

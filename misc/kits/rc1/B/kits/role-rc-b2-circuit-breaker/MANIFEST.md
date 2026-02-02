# RC-B2 — CircuitBreaker для RemotePdpV2 (и любого PdpV2Interface)

## Что добавлено

- `CircuitBreakingPdpV2` — декоратор над PdpV2Interface со состояниями: **closed → open → half-open**.
- Экспоненциальный backoff: `openSeconds = min(open_base * 2^retries, open_max)`.
- Фолбэк: `Decision DENY + obligation(type='degraded', params={reason:'circuit_open'})` при open.
- `RemoteHttpException` — исключение с HTTP‑статусом (если оборачиваешь реальный HTTP‑клиент).
- Инжектируемые параметры: `failureThreshold`, `openBaseSeconds`, `openMaxSeconds`.
- Инжектируемые часы (Clock) для тестов/детерминированности.

## Acceptance

- Серия 5xx (или исключений) ≥ threshold → состояние **open**.
- В состоянии **open** запросы НЕ идут во внутренний PDP, сразу фолбэк DENY (degraded).
- По истечении окна open — один пробный запрос (**half-open**):
    - успех → **closed** (сброс счётчиков);
    - ошибка → снова **open** с увеличенным бэкоффом.

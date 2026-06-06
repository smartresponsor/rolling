# Rolling Role W07 Checkbox and Null Semantics

Rolling uses two HTTP/UI surfaces:

- native EasyAdmin/admin forms under `src/Controller/Admin/*` and `src/Form/*`;
- front/runtime HTTP services through `src/Service/Http/*`.

Checkbox behavior is intentionally documented before Symfony 8.1 adoption because unchecked HTML checkboxes are omitted from submitted payloads.

## Canon

- Full form submit: omitted checkbox means `false`.
- PATCH-like partial submit: omitted checkbox keeps the current value.
- Runtime JSON payloads must not infer `false` from missing keys unless the DTO explicitly defines that default.
- Textarea fields mapped to array value objects must use an explicit transformer.

## W07 changes

- Adds regression tests for role runtime and role hierarchy checkbox behavior.
- Adds a model transformer for hierarchy default edges so textarea input remains compatible with the array value object.
- Adds `tools/qa/rolling-checkbox-form-audit.php` and composer script `checkbox-form:audit`.
- Adds explicit `symfony/form` dependency because Rolling owns form types directly.

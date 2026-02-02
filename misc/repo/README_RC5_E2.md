# role-rc5-e2-pel

Created: 2025-10-27T18:22:21Z UTC

EN-only comments. Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Adds tiny Policy Expression Language (PEL v1) and PolicyStage.
Supports:

- equality: attr == "value"
- in-list: attr in ["a","b"]
- and/or grouping with parentheses
  Mapping keys: subject.role, action, resource.type, attrs[key]

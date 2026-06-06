# Rolling W30 — single roll surface folder

Rolling exports interface data through one singular business word: `roll`.

The producer-side contract is intentionally small:

- no Interfacing namespace dependency;
- no bridge class;
- no plural folder;
- no generic fallback folder;
- one business template folder: `templates/roll/`.

The exported surface payload contains named slots. Interfacing may render
`templates/roll/summary.html.twig` when it exists. When it does not exist, the
same slot payload remains safe to render as data.

Canonical path:

```text
templates/roll/summary.html.twig
templates/roll/base.html.twig
templates/roll/data.html.twig
```

The component remains the owner of role/authorization business data. Interfacing
remains the owner of rendering, layout, and final DOM placement.


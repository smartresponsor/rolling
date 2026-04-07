# Rolling / Role — W18 business scenario expansion

W18 extends the fixture/scenario perimeter introduced in W16–W17 with richer business cases:

- partial-propagation
- multi-hop-chain
- revoke-after-propagation

It also adds CLI scenario introspection via `app:role:scenario:list <fixture>` and Composer shortcuts for the newly introduced scenarios.

This wave keeps the same lightweight test/CLI foundation and does not introduce Symfony Console or Doctrine Fixtures. The focus is reusable business fixtures and scenario assertions.

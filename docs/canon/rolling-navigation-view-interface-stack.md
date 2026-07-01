# Rolling navigation, view, and interface stack

Rolling participates in the platform UI stack without taking over shell rendering.

## Direct dependency

Rolling directly depends on Navigating:

```json
"navigating/navigation": "dev-master"
```

The local workspace path repository is `../Navigating`.

Navigating owns navigation item ownership, runtime visibility projection, role/scope filtering, and `interface.locations` projection for navigation chrome.

Relevant Navigating contract:

```text
App\Navigating\ServiceInterface\Navigation\Provide\NavigationInterfaceLocationProjectionProviderInterface
```

It returns canonical location buckets:

```text
provideInterfaceLocations(Request): array<string, list<array<string, mixed>>>
provideInterfacePayload(Request): array{locations: ..., active: ...}
```

## Viewing boundary

Viewing owns the `kernel.view` render boundary.

Rolling should return neutral producer payloads or domain surface output. It should not decide final HTML vs JSON rendering in producer code.

Viewing normalizes payloads into:

```text
_view
interface.locations
locations
data
meta
debug
```

Viewing may also merge App-composed interface locations before rendering. App-composed locations are authoritative for the slots they publish.

## Interfacing boundary

Interfacing owns shell locations, Twig integration, React provider mounting, and final DOM placement.

Canonical Interfacing shell locations include:

```text
top_bar
left_navigation
body_header
body_toolbar
body_content
right_context
bottom_bar
```

Rolling must not render those locations directly. Rolling may expose data and recommended actions that another layer places into the interface.

## Rolling responsibility

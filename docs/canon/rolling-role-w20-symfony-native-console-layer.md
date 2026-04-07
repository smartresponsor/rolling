# Rolling / Role — W20 Symfony-native console layer

This wave rebuilds the operational CLI around `symfony/console` instead of a manual argv dispatcher.

## Scope

- keep the existing command surface stable
- move command handling into dedicated command classes under `src/Infrastructure/Console/Command`
- preserve the lightweight non-FrameworkBundle model for now
- make the CLI testable through `CommandTester`

## Result

`RoleConsoleApplication` now builds a real `Symfony\Component\Console\Application` and registers:

- fixture commands
- scenario listing and execution commands
- propagation / elimination preview and run commands
- explain / audit diagnostics

## Boundary

This is Symfony Console native, but not yet full FrameworkBundle / DI-container command registration.

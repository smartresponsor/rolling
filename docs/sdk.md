# SDK parity (RC-D7)

Languages: **TS/Node 18+**, **Go 1.22+**, **Java 11+**.

Common features:

- `/check` with optional `?consistency=strong|eventual` and header echo `X-Role-Consistency`.
- HMAC signing headers: `X-Role-Date: <ISO>` and `X-Role-Signature: hmac-sha256:<base64>` over `date + \n + payload`.
- Retries with exponential backoff, timeout controls.
- Minimal dependency footprint.

Examples:

- TS: `ROLE_ENDPOINT=http://localhost:8088/v2 npx ts-node examples/ts/check_sample/main.ts`
- Go: `cd examples/go/check_sample && go run .`
- Java: `javac $(find sdk/java/role/src/main/java -name "*.java") && java -cp sdk/java/role/src/main/java com.smartresponsor.role.example.Main`

Server-side validation: verify signature using the shared HMAC key and reject if missing/invalid.

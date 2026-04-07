# PHP SDK — типизированные исключения

Пример:

```php
use App\Legacy\Http\ResponseErrorMapper;

$res = $http->sendRequest($req);
ResponseErrorMapper::throwOnError($res); // кидает исключение по статусу
```

Исключения: `BadRequestException`, `UnauthorizedException`, `ForbiddenException`, `RateLimitException`, `RemoteErrorException`,
базовый `ApiException`.

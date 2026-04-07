# CachedPdpV2 (RC-B1)

## Как подключить

```php
use App\Legacy\Cache\InMemoryCache;use App\Legacy\Invalidation\SubjectEpochs;use App\Legacy\Policy\Decorator\V2\CachedPdpV2;

/** @var \PolicyInterface\Role\PdpV2Interface $inner */
$cache = new InMemoryCache();
$epochs = new SubjectEpochs();
$pdp = new CachedPdpV2($inner, $cache, $epochs, ttlSeconds: 600);
```

### Инвалидация

```php
$epochs->bump('u1'); // инвалидирует все ключи по subjectId=u1 (через смену epoch)
```

### Ключ кеша

`v2:{sid}:{scope}:{action}:ctx:{sha256}:se:{epoch}`

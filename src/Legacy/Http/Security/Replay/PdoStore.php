<?php

declare(strict_types=1);

namespace App\Legacy\Http\Security\Replay;

use App\Infrastructure\Security\Replay\PdoReplayNonceStore;

final class PdoStore extends PdoReplayNonceStore implements StoreInterface
{
}

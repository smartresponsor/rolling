<?php

declare(strict_types=1);

namespace App\Integration\Symfony\EventSubscriber;

use App\Infrastructure\Symfony\EventSubscriber\HmacGuardSubscriber as CanonicalHmacGuardSubscriber;

final class HmacGuardSubscriber extends CanonicalHmacGuardSubscriber
{
}

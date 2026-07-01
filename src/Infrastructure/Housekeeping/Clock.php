<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Housekeeping;

final class Clock
{
    public static function now(): int
    {
        return time();
    }
}

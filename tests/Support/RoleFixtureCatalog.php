<?php

declare(strict_types=1);

namespace App\Tests\Support;

use InvalidArgumentException;

final class RoleFixtureCatalog
{
    private const BASE_DIR = __DIR__ . '/../Fixture/Role';

    public static function names(): array
    {
        $names = [];
        foreach (glob(self::BASE_DIR . '/*.php') ?: [] as $path) {
            $names[] = basename($path, '.php');
        }

        sort($names);

        return $names;
    }

    public static function get(string $name): array
    {
        $path = self::BASE_DIR . '/' . $name . '.php';
        if (!is_file($path)) {
            throw new InvalidArgumentException(sprintf('Unknown fixture "%s".', $name));
        }

        $fixture = require $path;
        if (!is_array($fixture)) {
            throw new InvalidArgumentException(sprintf('Fixture "%s" must return an array.', $name));
        }

        return $fixture;
    }
}

<?php

declare(strict_types=1);

namespace App\Rolling\Tests\Support;

final class RoleFixtureCatalog
{
    private const BASE_DIR = __DIR__.'/../Fixture/Role';

    public static function names(): array
    {
        $names = [];
        foreach (glob(self::BASE_DIR.'/*.php') ?: [] as $path) {
            $names[] = basename($path, '.php');
        }

        sort($names);

        return $names;
    }

    public static function get(string $nameEntity): array
    {
        $path = self::BASE_DIR.'/'.$nameEntity.'.php';
        if (!is_file($path)) {
            throw new \InvalidArgumentException(sprintf('Unknown fixture "%s".', $nameEntity));
        }

        $fixture = require $path;
        if (!is_array($fixture)) {
            throw new \InvalidArgumentException(sprintf('Fixture "%s" must return an array.', $nameEntity));
        }

        return $fixture;
    }
}

<?php

declare(strict_types=1);

namespace App\Infrastructure\Console\Support;

final class JsonReportLoader
{
    /**
     * @return array<string, mixed>
     */
    public function load(string $path): array
    {
        if ($path === '') {
            throw new \InvalidArgumentException('Baseline report path must not be empty.');
        }

        if (!is_file($path)) {
            throw new \RuntimeException(sprintf('Baseline report not found: %s', $path));
        }

        $decoded = json_decode((string) file_get_contents($path), true);
        if (!is_array($decoded)) {
            throw new \RuntimeException(sprintf('Baseline report is not a valid JSON object: %s', $path));
        }

        return $decoded;
    }
}

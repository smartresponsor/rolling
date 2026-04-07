<?php

declare(strict_types=1);

namespace App\Infrastructure\Console\Support;

final class ComparisonProfileCatalog
{
    public function __construct(
        private readonly string $configPath = __DIR__ . '/../../../../config/role/perf_profiles.json',
        private readonly JsonReportLoader $loader = new JsonReportLoader(),
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->loader->load($this->configPath);
    }

    /**
     * @return array<string, float|int>
     */
    public function profile(string $kind, string $profile): array
    {
        $catalog = $this->all();
        $kindProfiles = $catalog[$kind] ?? null;
        if (!is_array($kindProfiles)) {
            throw new \RuntimeException(sprintf('Unknown comparison kind "%s".', $kind));
        }

        $selected = $kindProfiles[$profile] ?? null;
        if (!is_array($selected)) {
            throw new \RuntimeException(sprintf('Unknown comparison profile "%s" for "%s".', $profile, $kind));
        }

        return $selected;
    }


    /**
     * @return array<string, array<string, float|int>>
     */
    public function profiles(string $kind): array
    {
        $catalog = $this->all();
        $kindProfiles = $catalog[$kind] ?? null;
        if (!is_array($kindProfiles)) {
            return [];
        }

        return $kindProfiles;
    }

    /**
     * @return list<string>
     */
    public function names(string $kind): array
    {
        $catalog = $this->all();
        $kindProfiles = $catalog[$kind] ?? [];

        return array_values(array_map('strval', array_keys(is_array($kindProfiles) ? $kindProfiles : [])));
    }
}

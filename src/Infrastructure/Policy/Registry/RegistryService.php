<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Policy\Registry;

use App\Rolling\Service\Consistency\Policy\PolicyConsistencyToken;

final class RegistryService
{
    public function __construct(private readonly StoreInterface $store)
    {
    }

    public function importPolicy(string $ns, string $nameEntity, string $version, string $docJson): PolicyConsistencyToken
    {
        return $this->store->put($ns, $nameEntity, $version, $docJson);
    }

    public function activatePolicy(string $ns, string $nameEntity, string $version): PolicyConsistencyToken
    {
        return $this->store->activate($ns, $nameEntity, $version);
    }

    public function exportPolicy(string $ns, string $nameEntity, string $version): ?string
    {
        return $this->store->export($ns, $nameEntity, $version);
    }

    /** @return list<PolicyRecord> */
    public function listVersions(string $ns, string $nameEntity): array
    {
        return $this->store->listVersions($ns, $nameEntity);
    }

    public function getActive(string $ns, string $nameEntity): ?PolicyRecord
    {
        return $this->store->getActive($ns, $nameEntity);
    }

    public function recordMigration(string $ns, string $nameEntity, string $from, string $to, ?string $note = null, ?string $stepsJson = null): void
    {
        $this->store->recordMigration($ns, $nameEntity, $from, $to, $note, $stepsJson);
    }

    /** @return list<array{from: string, to: string, migrationNote: ?string, appliedAt: int}> */
    public function listMigrations(string $ns, string $nameEntity): array
    {
        return $this->store->listMigrations($ns, $nameEntity);
    }

    public function token(): PolicyConsistencyToken
    {
        return $this->store->currentToken();
    }
}

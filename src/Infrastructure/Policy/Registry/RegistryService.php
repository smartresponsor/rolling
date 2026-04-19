<?php

declare(strict_types=1);

namespace App\Infrastructure\Policy\Registry;

use App\Service\Consistency\Policy\Token;

final class RegistryService
{
    public function __construct(private readonly StoreInterface $store)
    {
    }

    public function importPolicy(string $ns, string $name, string $version, string $docJson): Token
    {
        return $this->store->put($ns, $name, $version, $docJson);
    }

    public function activatePolicy(string $ns, string $name, string $version): Token
    {
        return $this->store->activate($ns, $name, $version);
    }

    public function exportPolicy(string $ns, string $name, string $version): ?string
    {
        return $this->store->export($ns, $name, $version);
    }

    /** @return list<PolicyRecord> */
    public function listVersions(string $ns, string $name): array
    {
        return $this->store->listVersions($ns, $name);
    }

    public function getActive(string $ns, string $name): ?PolicyRecord
    {
        return $this->store->getActive($ns, $name);
    }

    public function recordMigration(string $ns, string $name, string $from, string $to, ?string $note = null, ?string $stepsJson = null): void
    {
        $this->store->recordMigration($ns, $name, $from, $to, $note, $stepsJson);
    }

    /** @return list<array{from: string, to: string, migrationNote: ?string, appliedAt: int}> */
    public function listMigrations(string $ns, string $name): array
    {
        return $this->store->listMigrations($ns, $name);
    }

    public function token(): Token
    {
        return $this->store->currentToken();
    }
}

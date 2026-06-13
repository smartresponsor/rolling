<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Policy\Registry;

use App\Rolling\Service\Consistency\Policy\PolicyConsistencyToken;

interface StoreInterface
{
    public function put(string $ns, string $nameEntity, string $version, string $docJson): PolicyConsistencyToken;

    public function activate(string $ns, string $nameEntity, string $version): PolicyConsistencyToken;

    public function getActive(string $ns, string $nameEntity): ?PolicyRecord;

    /** @return list<PolicyRecord> */
    public function listVersions(string $ns, string $nameEntity): array;

    public function export(string $ns, string $nameEntity, string $version): ?string;

    public function currentToken(): PolicyConsistencyToken;

    public function recordMigration(string $ns, string $nameEntity, string $from, string $to, ?string $note = null, ?string $stepsJson = null): void;

    /** @return list<array{from: string, to: string, migrationNote: ?string, appliedAt: int}> */
    public function listMigrations(string $ns, string $nameEntity): array;
}

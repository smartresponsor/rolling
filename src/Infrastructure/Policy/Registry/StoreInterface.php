<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Policy\Registry;

use App\Rolling\Service\Consistency\Policy\Token;

interface StoreInterface
{
    public function put(string $ns, string $name, string $version, string $docJson): Token;

    public function activate(string $ns, string $name, string $version): Token;

    public function getActive(string $ns, string $name): ?PolicyRecord;

    /** @return list<PolicyRecord> */
    public function listVersions(string $ns, string $name): array;

    public function export(string $ns, string $name, string $version): ?string;

    public function currentToken(): Token;

    public function recordMigration(string $ns, string $name, string $from, string $to, ?string $note = null, ?string $stepsJson = null): void;

    /** @return list<array{from: string, to: string, migrationNote: ?string, appliedAt: int}> */
    public function listMigrations(string $ns, string $name): array;
}

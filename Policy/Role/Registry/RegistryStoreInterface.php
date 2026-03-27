<?php

declare(strict_types=1);

namespace Policy\Role\Registry;

use App\Consistency\Role\Policy\Token;

/**
 *
 */

/**
 *
 */
interface RegistryStoreInterface
{
    /**
     * @param string $ns
     * @param string $name
     * @param string $version
     * @param string $docJson
     * @return \App\Consistency\Role\Policy\Token
     */
    public function put(string $ns, string $name, string $version, string $docJson): Token;

    /**
     * @param string $ns
     * @param string $name
     * @param string $version
     * @return \App\Consistency\Role\Policy\Token
     */
    public function activate(string $ns, string $name, string $version): Token;

    /**
     * @param string $ns
     * @param string $name
     * @return \Policy\Role\Registry\PolicyRecord|null
     */
    public function getActive(string $ns, string $name): ?PolicyRecord;

    /** @return list<PolicyRecord> */
    public function listVersions(string $ns, string $name): array;

    /**
     * @param string $ns
     * @param string $name
     * @param string $version
     * @return string|null
     */
    public function export(string $ns, string $name, string $version): ?string;

    /**
     * @return \App\Consistency\Role\Policy\Token
     */
    public function currentToken(): Token;

    /**
     * @param string $ns
     * @param string $name
     * @param string $from
     * @param string $to
     * @param string|null $note
     * @param string|null $stepsJson
     * @return void
     */
    public function recordMigration(string $ns, string $name, string $from, string $to, ?string $note = null, ?string $stepsJson = null): void;

    /** @return list<array{from:string,to:string,note:?string,applied_at:int}> */
    public function listMigrations(string $ns, string $name): array;
}

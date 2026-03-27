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
final class RegistryService
{
    /**
     * @param \Policy\Role\Registry\RegistryStoreInterface $store
     */
    public function __construct(private readonly RegistryStoreInterface $store) {}

    /**
     * @param string $ns
     * @param string $name
     * @param string $version
     * @param string $docJson
     * @return \App\Consistency\Role\Policy\Token
     */
    public function importPolicy(string $ns, string $name, string $version, string $docJson): Token
    {
        // Could add JSON validation here
        return $this->store->put($ns, $name, $version, $docJson);
    }

    /**
     * @param string $ns
     * @param string $name
     * @param string $version
     * @return \App\Consistency\Role\Policy\Token
     */
    public function activatePolicy(string $ns, string $name, string $version): Token
    {
        return $this->store->activate($ns, $name, $version);
    }

    /**
     * @param string $ns
     * @param string $name
     * @param string $version
     * @return string|null
     */
    public function exportPolicy(string $ns, string $name, string $version): ?string
    {
        return $this->store->export($ns, $name, $version);
    }

    /**
     * @param string $ns
     * @param string $name
     * @return array
     */
    public function listVersions(string $ns, string $name): array
    {
        return $this->store->listVersions($ns, $name);
    }

    /**
     * @param string $ns
     * @param string $name
     * @return \Policy\Role\Registry\PolicyRecord|null
     */
    public function getActive(string $ns, string $name): ?PolicyRecord
    {
        return $this->store->getActive($ns, $name);
    }

    /**
     * @param string $ns
     * @param string $name
     * @param string $from
     * @param string $to
     * @param string|null $note
     * @param string|null $stepsJson
     * @return void
     */
    public function recordMigration(string $ns, string $name, string $from, string $to, ?string $note = null, ?string $stepsJson = null): void
    {
        $this->store->recordMigration($ns, $name, $from, $to, $note, $stepsJson);
    }

    /**
     * @param string $ns
     * @param string $name
     * @return array
     */
    public function listMigrations(string $ns, string $name): array
    {
        return $this->store->listMigrations($ns, $name);
    }

    /**
     * @return \App\Consistency\Role\Policy\Token
     */
    public function token(): Token
    {
        return $this->store->currentToken();
    }
}

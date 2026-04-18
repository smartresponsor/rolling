<?php

declare(strict_types=1);

namespace Tests\Role\Policy\Registry;

use PHPUnit\Framework\TestCase;

/**
 *
 */

/**
 *
 */
final class PolicyRegistryTest extends TestCase
{
    /**
     * @return void
     */
    public function testImportActivateMigrate(): void
    {
        $svc = new \Policy\Role\Registry\RegistryService(new \Policy\Role\Registry\InMemoryRegistryStore());
        $ns = 'acme';
        $name = 'doc-view';

        $svc->importPolicy($ns, $name, '1.0.0', '{"rules":[{"allow":"viewer"}]}');
        $svc->importPolicy($ns, $name, '1.1.0', '{"rules":[{"allow":"viewer"},{"deny":"banned"}]}');

        // activate v1
        $svc->activatePolicy($ns, $name, '1.0.0');
        $active = $svc->getActive($ns, $name);
        $this->assertNotNull($active);
        $this->assertSame('1.0.0', $active->version);

        // migrate to v1.1.0 and activate
        $svc->recordMigration($ns, $name, '1.0.0', '1.1.0', 'add deny banned');
        $svc->activatePolicy($ns, $name, '1.1.0');
        $active2 = $svc->getActive($ns, $name);
        $this->assertNotNull($active2);
        $this->assertSame('1.1.0', $active2->version);
    }
}

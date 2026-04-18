<?php

declare(strict_types=1);

namespace Tests\Role\Policy\Registry;

use App\Infrastructure\Policy\Registry\InMemoryStore;
use App\Infrastructure\Policy\Registry\RegistryService;
use PHPUnit\Framework\TestCase;

final class PolicyRegistryTest extends TestCase
{
    public function testImportActivateMigrate(): void
    {
        $svc = new RegistryService(new InMemoryStore());
        $ns = 'acme';
        $name = 'doc-view';

        $svc->importPolicy($ns, $name, '1.0.0', '{"rules":[{"allow":"viewer"}]}');
        $svc->importPolicy($ns, $name, '1.1.0', '{"rules":[{"allow":"viewer"},{"deny":"banned"}]}');

        $svc->activatePolicy($ns, $name, '1.0.0');
        $active = $svc->getActive($ns, $name);
        self::assertNotNull($active);
        self::assertSame('1.0.0', $active->version);

        $svc->recordMigration($ns, $name, '1.0.0', '1.1.0', 'add deny banned');
        $svc->activatePolicy($ns, $name, '1.1.0');
        $active2 = $svc->getActive($ns, $name);
        self::assertNotNull($active2);
        self::assertSame('1.1.0', $active2->version);
    }
}

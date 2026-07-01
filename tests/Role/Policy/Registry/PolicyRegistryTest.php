<?php

declare(strict_types=1);

namespace App\Rolling\Tests\Role\Policy\Registry;

use App\Rolling\Infrastructure\Policy\Registry\InMemoryStore;
use App\Rolling\Infrastructure\Policy\Registry\RegistryService;
use PHPUnit\Framework\TestCase;

final class PolicyRegistryTest extends TestCase
{
    public function testImportActivateMigrate(): void
    {
        $svc = new RegistryService(new InMemoryStore());
        $ns = 'acme';
        $nameEntity = 'doc-view';

        $svc->importPolicy($ns, $nameEntity, '1.0.0', '{"rules":[{"allow":"viewer"}]}');
        $svc->importPolicy($ns, $nameEntity, '1.1.0', '{"rules":[{"allow":"viewer"},{"deny":"banned"}]}');

        $svc->activatePolicy($ns, $nameEntity, '1.0.0');
        $active = $svc->getActive($ns, $nameEntity);
        self::assertNotNull($active);
        self::assertSame('1.0.0', $active->version);

        $svc->recordMigration($ns, $nameEntity, '1.0.0', '1.1.0', 'add deny banned');
        $svc->activatePolicy($ns, $nameEntity, '1.1.0');
        $active2 = $svc->getActive($ns, $nameEntity);
        self::assertNotNull($active2);
        self::assertSame('1.1.0', $active2->version);
    }
}

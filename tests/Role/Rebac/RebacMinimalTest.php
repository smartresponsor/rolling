<?php

declare(strict_types=1);

namespace Tests\Role\Rebac;

use App\Rolling\Infrastructure\Rebac\InMemoryTupleStore;
use App\Rolling\Infrastructure\Rebac\Tuple;
use App\Rolling\Service\Rebac\Checker;
use App\Rolling\Service\Rebac\Writer;
use PHPUnit\Framework\TestCase;

final class RebacMinimalTest extends TestCase
{
    /**
     * @return void
     */
    public function testDirectAndGroupMembership(): void
    {
        $store = new InMemoryTupleStore();
        $w = new Writer($store);
        $c = new Checker($store);

        $ns = 'acme';

        // group dev: user:42 is member
        $w->write($ns, [new Tuple($ns, 'group', 'dev', 'member', 'user', '42', null)]);
        // doc:1 viewer <- group:dev#member  (indirect)
        $w->write($ns, [new Tuple($ns, 'doc', '1', 'viewer', 'group', 'dev', 'member')]);

        $res1 = $c->check($ns, 'user:42', 'doc:1', 'viewer');
        $this->assertTrue($res1['allow']);

        // negative case
        $res2 = $c->check($ns, 'user:41', 'doc:1', 'viewer');
        $this->assertFalse($res2['allow']);
    }
}

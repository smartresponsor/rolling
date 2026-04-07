<?php
declare(strict_types=1);

namespace Tests\Role\Opa;

use App\Legacy\Net\Opa\OpaClientInterface;
use App\Policy\Role\Opa\Policy\Role\Opa\OpaPdpV2;
use App\Policy\Role\Opa\Policy\Role\Opa\InputBuilder;
use PHPUnit\Framework\TestCase;
use App\Entity\Role\Scope;
use App\Entity\Role\PermissionKey;
use App\Entity\Role\SubjectId;

/**
 *
 */

/**
 *
 */
final class OpaPdpV2Test extends TestCase
{
    /**
     * @return void
     */
    public function testAllowAndObligations(): void
    {
        $client = new class implements OpaClientInterface {
            /**
             * @param string $path
             * @param array $input
             * @return array[]
             */
            public function evaluate(string $path, array $input): array
            {
                return ['result' => ['allow' => $input['subject']['id'] === 'u1', 'reason' => 'ok', 'obligations' => []]];
            }
        };
        $pdp = new \Policy\Role\Opa\OpaPdpV2($client, new \Policy\Role\Opa\InputBuilder(), 'role/v2/decision');

        $allow = $pdp->check(new SubjectId('u1'), new PermissionKey('message.read'), Scope::global());
        $deny = $pdp->check(new SubjectId('u2'), new PermissionKey('message.read'), Scope::global());

        $this->assertTrue($allow->isAllow());
        $this->assertFalse($deny->isAllow());
    }
}

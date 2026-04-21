<?php

declare(strict_types=1);

namespace Tests\Role\Opa;

use App\Rolling\Entity\Role\PermissionKey;
use App\Rolling\Entity\Role\Scope;
use App\Rolling\Entity\Role\SubjectId;
use App\Rolling\InfrastructureInterface\Net\Opa\OpaClientInterface;
use App\Rolling\Policy\Opa\InputBuilder;
use App\Rolling\Policy\Opa\OpaPdpV2;
use PHPUnit\Framework\TestCase;

final class OpaPdpV2Test extends TestCase
{
    public function testAllowAndObligations(): void
    {
        $client = new class implements OpaClientInterface {
            public function evaluate(string $path, array $input): array
            {
                return ['result' => ['allow' => 'u1' === $input['subject']['id'], 'reason' => 'ok', 'obligations' => []]];
            }
        };

        $pdp = new OpaPdpV2($client, new InputBuilder(), 'role/v2/decision');

        $allow = $pdp->check(new SubjectId('u1'), new PermissionKey('message.read'), Scope::global());
        $deny = $pdp->check(new SubjectId('u2'), new PermissionKey('message.read'), Scope::global());

        self::assertTrue($allow->isAllow());
        self::assertFalse($deny->isAllow());
    }
}

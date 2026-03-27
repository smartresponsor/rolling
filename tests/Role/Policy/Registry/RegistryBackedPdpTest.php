<?php

declare(strict_types=1);

namespace Tests\Role\Policy\Registry;

use App\Policy\Role\Obligation\Applier\ArrayApplier;
use PHPUnit\Framework\TestCase;
use Policy\Role\Decorator\V2\RegistryBackedPdp;
use Policy\Role\V2\DecisionWithObligations;
use PolicyInterface\Role\PdpV2Interface;
use src\Entity\Role\{Scope};
use src\Entity\Role\PermissionKey;
use src\Entity\Role\SubjectId;

/**
 *
 */

/**
 *
 */
final class RegistryBackedPdpTest extends TestCase
{
    /**
     * @return \PolicyInterface\Role\PdpV2Interface
     */
    private function baseAllow(): PdpV2Interface
    {
        return new class implements PdpV2Interface {
            /**
             * @param \src\Entity\Role\SubjectId $s
             * @param \src\Entity\Role\PermissionKey $a
             * @param \src\Entity\Role\Scope $sc
             * @param array $c
             * @return \Policy\Role\V2\DecisionWithObligations
             */
            public function check(SubjectId $s, PermissionKey $a, Scope $sc, array $c = []): DecisionWithObligations
            {
                return DecisionWithObligations::allow('base');
            }
        };
    }

    /**
     * @return array
     */
    private function sampleConfig(): array
    {
        return [
            'flags' => [
                'redact' => [
                    'when' => [['tenantId' => 't1']],
                    'rules' => [['type' => 'redact_fields', 'params' => ['fields' => ['secret']]]],
                ],
            ],
            'routes' => [
                ['actions' => ['message.read'], 'use' => ['redact']],
            ],
        ];
    }

    /**
     * @return void
     */
    public function testDecoratorAddsObligationsFromRegistry(): void
    {
        $pdp = new RegistryBackedPdp($this->baseAllow(), new \Policy\Role\Registry\PolicyRegistry(new \Policy\Role\Registry\InMemorySource($this->sampleConfig())));
        $sid = new SubjectId('u1');
        $act = new PermissionKey('message.read');
        $sc = Scope::tenant('t1');

        $d = $pdp->check($sid, $act, $sc, ['tenantId' => 't1']);
        $applied = (new ArrayApplier())->apply(['secret' => 'x', 'ok' => 1], $d->obligations);
        $this->assertSame('***', $applied['data']['secret']);
    }
}

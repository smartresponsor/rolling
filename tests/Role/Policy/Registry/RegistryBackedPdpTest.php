<?php
declare(strict_types=1);

namespace Tests\Role\Policy\Registry;

use App\Policy\Obligation\Applier\ArrayApplier;
use App\Policy\Role\Registry\Policy\Role\Registry\PolicyRegistry;
use App\Policy\Role\Registry\Policy\Role\Registry\InMemorySource;
use PHPUnit\Framework\TestCase;
use App\Legacy\Policy\Decorator\V2\RegistryBackedPdp;
use App\Policy\V2\DecisionWithObligations;
use App\PolicyInterface\PdpV2Interface;
use App\Entity\Role\Scope;
use App\Entity\Role\PermissionKey;
use App\Entity\Role\SubjectId;

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
             * @param \App\Entity\Role\SubjectId $s
             * @param \App\Entity\Role\PermissionKey $a
             * @param \App\Entity\Role\Scope $sc
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

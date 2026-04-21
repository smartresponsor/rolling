<?php

declare(strict_types=1);

namespace Tests\Role\Policy\Registry;

use App\Rolling\Entity\Role\PermissionKey;
use App\Rolling\Entity\Role\Scope;
use App\Rolling\Entity\Role\SubjectId;
use App\Rolling\Infrastructure\Policy\Registry\InMemorySource;
use App\Rolling\Infrastructure\Policy\Registry\PolicyRegistry;
use App\Rolling\Policy\Decorator\V2\RegistryBackedPdp;
use App\Rolling\Policy\Obligation\Applier\ArrayApplier;
use App\Rolling\Policy\V2\DecisionWithObligations;
use App\Rolling\ServiceInterface\Policy\PdpV2Interface;
use PHPUnit\Framework\TestCase;

final class RegistryBackedPdpTest extends TestCase
{
    private function baseAllow(): PdpV2Interface
    {
        return new class implements PdpV2Interface {
            public function check(SubjectId $s, PermissionKey $a, Scope $sc, array $c = []): DecisionWithObligations
            {
                return DecisionWithObligations::allow('base');
            }
        };
    }

    /** @return array<string,mixed> */
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

    public function testDecoratorAddsObligationsFromRegistry(): void
    {
        $pdp = new RegistryBackedPdp($this->baseAllow(), new PolicyRegistry(new InMemorySource($this->sampleConfig())));
        $sid = new SubjectId('u1');
        $act = new PermissionKey('message.read');
        $sc = Scope::tenant('t1');

        $decision = $pdp->check($sid, $act, $sc, ['tenantId' => 't1']);
        $applied = (new ArrayApplier())->apply(['secret' => 'x', 'ok' => 1], $decision->obligations());
        self::assertSame('***', $applied['data']['secret']);
    }
}

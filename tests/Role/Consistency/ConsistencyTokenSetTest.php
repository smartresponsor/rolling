<?php

declare(strict_types=1);

namespace Tests\Role\Consistency;

use App\Rolling\Service\Consistency\ConsistencyTokenSet;
use PHPUnit\Framework\TestCase;

final class ConsistencyTokenSetTest extends TestCase
{
    public function testRoundTripWithSubjectEpoch(): void
    {
        $token = new ConsistencyTokenSet(3, 7, 11);

        $parsed = ConsistencyTokenSet::fromString((string) $token);

        $this->assertSame(3, $parsed->policyRev);
        $this->assertSame(7, $parsed->rebacRev);
        $this->assertSame(11, $parsed->subjectEpoch);
        $this->assertSame('p:3;r:7;s:11;', (string) $parsed);
    }

    public function testMalformedSegmentsAreIgnored(): void
    {
        $parsed = ConsistencyTokenSet::fromString('p:2;broken;r:9;');

        $this->assertSame(2, $parsed->policyRev);
        $this->assertSame(9, $parsed->rebacRev);
        $this->assertNull($parsed->subjectEpoch);
    }
}

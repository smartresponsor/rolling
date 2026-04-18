<?php

declare(strict_types=1);

namespace Tests\Role\Context;

use Http\Role\V2\Context\ContextMerge;
use PHPUnit\Framework\TestCase;

final class ContextMergeTest extends TestCase
{
    public function testServerValuesOverrideConflictingClientKeys(): void
    {
        $merged = ContextMerge::merge(
            ['user' => ['id' => 'client'], 'keep' => 'client'],
            ['user' => ['id' => 'server'], 'org' => ['id' => 'org-1']],
        );

        $this->assertSame(
            [
                'user' => ['id' => 'server'],
                'keep' => 'client',
                'org' => ['id' => 'org-1'],
            ],
            $merged,
        );
    }
}

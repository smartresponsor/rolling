<?php

declare(strict_types=1);

namespace App\Rolling\Tests\Role\Model;

use App\Rolling\Service\Model\ModelSchemaDiffCalculator;
use PHPUnit\Framework\TestCase;

final class ModelSchemaDiffCalculatorTest extends TestCase
{
    public function testAddedRemovedChanged(): void
    {
        $from = ['namespace' => 'doc', 'relations' => ['viewer' => ['of' => 'user']]];
        $to = ['namespace' => 'doc', 'relations' => ['viewer' => ['of' => 'user'], 'editor' => ['of' => 'user']]];
        $d = ModelSchemaDiffCalculator::compute($from, $to);
        $this->assertFalse($d['breaking']);
        $this->assertContains('editor', $d['added']);
    }

    public function testBreakingOnRemoval(): void
    {
        $from = ['namespace' => 'doc', 'relations' => ['viewer' => ['of' => 'user'], 'editor' => ['of' => 'user']]];
        $to = ['namespace' => 'doc', 'relations' => ['viewer' => ['of' => 'user']]];
        $d = ModelSchemaDiffCalculator::compute($from, $to);
        $this->assertTrue($d['breaking']);
        $this->assertContains('editor', $d['removed']);
    }
}

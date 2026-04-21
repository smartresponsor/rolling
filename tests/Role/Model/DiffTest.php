<?php

declare(strict_types=1);

namespace App\Rolling\Tests\Role\Model;

use App\Rolling\Service\Model\Diff;
use PHPUnit\Framework\TestCase;

final class DiffTest extends TestCase
{
    public function testAddedRemovedChanged(): void
    {
        $from = ['namespace' => 'doc', 'relations' => ['viewer' => ['of' => 'user']]];
        $to = ['namespace' => 'doc', 'relations' => ['viewer' => ['of' => 'user'], 'editor' => ['of' => 'user']]];
        $d = Diff::compute($from, $to);
        $this->assertFalse($d['breaking']);
        $this->assertContains('editor', $d['added']);
    }

    public function testBreakingOnRemoval(): void
    {
        $from = ['namespace' => 'doc', 'relations' => ['viewer' => ['of' => 'user'], 'editor' => ['of' => 'user']]];
        $to = ['namespace' => 'doc', 'relations' => ['viewer' => ['of' => 'user']]];
        $d = Diff::compute($from, $to);
        $this->assertTrue($d['breaking']);
        $this->assertContains('editor', $d['removed']);
    }
}

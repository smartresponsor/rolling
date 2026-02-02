<?php
declare(strict_types=1);

use Model\Diff;
use PHPUnit\Framework\TestCase;

/**
 *
 */

/**
 *
 */
final class DiffTest extends TestCase
{
    /**
     * @return void
     */
    public function testAddedRemovedChanged(): void
    {
        $from = ['namespace' => 'doc', 'relations' => ['viewer' => ['of' => 'user']]];
        $to = ['namespace' => 'doc', 'relations' => ['viewer' => ['of' => 'user'], 'editor' => ['of' => 'user']]];
        $d = Diff::compute($from, $to);
        $this->assertFalse($d['breaking']);
        $this->assertContains('editor', $d['added']);
    }

    /**
     * @return void
     */
    public function testBreakingOnRemoval(): void
    {
        $from = ['namespace' => 'doc', 'relations' => ['viewer' => ['of' => 'user'], 'editor' => ['of' => 'user']]];
        $to = ['namespace' => 'doc', 'relations' => ['viewer' => ['of' => 'user']]];
        $d = Diff::compute($from, $to);
        $this->assertTrue($d['breaking']);
        $this->assertContains('editor', $d['removed']);
    }
}

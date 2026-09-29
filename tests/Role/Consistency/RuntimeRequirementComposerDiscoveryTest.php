<?php

declare(strict_types=1);

namespace App\Rolling\Tests\Role\Consistency;

use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 3).'/bin/bootstrap-runtime-requirements.php';

final class RuntimeRequirementComposerDiscoveryTest extends TestCase
{
    private string|false $originalComposerBinary;

    protected function setUp(): void
    {
        $this->originalComposerBinary = getenv('COMPOSER_BINARY');
    }

    protected function tearDown(): void
    {
        if (false === $this->originalComposerBinary) {
            putenv('COMPOSER_BINARY');

            return;
        }

        putenv('COMPOSER_BINARY='.$this->originalComposerBinary);
    }

    public function testComposerScriptEnvironmentIsAcceptedAsBinaryEvidence(): void
    {
        $composerBinary = 'C:\\tools\\composer.phar';
        putenv('COMPOSER_BINARY='.$composerBinary);

        $composerResolver = 'role_composer_binary_path';
        $requirementResolver = 'role_runtime_requirement_status';

        self::assertTrue(function_exists($composerResolver));
        self::assertTrue(function_exists($requirementResolver));
        self::assertSame($composerBinary, $composerResolver());

        $status = $requirementResolver(dirname(__DIR__, 3));

        self::assertTrue($status['composer_binary_present']);
        self::assertSame($composerBinary, $status['composer_binary_path']);
    }
}

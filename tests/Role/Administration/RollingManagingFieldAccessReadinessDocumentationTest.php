<?php

declare(strict_types=1);

namespace App\Rolling\Tests\Role\Administration;

use PHPUnit\Framework\TestCase;

final class RollingManagingFieldAccessReadinessDocumentationTest extends TestCase
{
    public function testReadinessDocumentSeparatesFieldValueAccessFromProfilePermissions(): void
    {
        $doc = file_get_contents(dirname(__DIR__, 3).'/docs/security/managing-field-access-readiness.adoc');

        self::assertIsString($doc);
        self::assertStringContainsString('managing.field.view', $doc);
        self::assertStringContainsString('must not be treated as field-value access grants', $doc);
        self::assertStringContainsString('component:managing:resource:<resource>:page:<page>:field:<field>', $doc);
        self::assertStringContainsString('permission.grant', $doc);
        self::assertStringContainsString('acl.deny', $doc);
        self::assertStringContainsString('rolling_field_value_access_denied', $doc);
    }
}

<?php

declare(strict_types=1);

namespace App\Rolling\Tests\Role\Model;

use App\Rolling\Service\Model\ModelSchemaValidator;
use PHPUnit\Framework\TestCase;

final class ModelSchemaValidatorTest extends TestCase
{
    public function testAcceptsTypedRelationsPermissionsAndConditions(): void
    {
        $schema = [
            'namespace' => 'document',
            'relations' => [
                'owner' => ['of' => 'user'],
                'editor' => ['of' => ['user', 'team#member'], 'condition' => 'business_hours'],
            ],
            'permissions' => [
                'view' => ['via' => ['owner', 'editor']],
                'edit' => ['via' => 'editor'],
            ],
            'conditions' => [
                'business_hours' => ['expression' => 'context.hour >= 8 && context.hour <= 18'],
            ],
        ];

        self::assertSame([], ModelSchemaValidator::validate($schema));
    }

    public function testRejectsUnknownPermissionRelation(): void
    {
        $schema = [
            'namespace' => 'document',
            'relations' => [
                'owner' => ['of' => 'user'],
            ],
            'permissions' => [
                'edit' => ['via' => 'editor'],
            ],
        ];

        self::assertContains(
            "Permission 'edit' references unknown relation: editor",
            ModelSchemaValidator::validate($schema)
        );
    }

    public function testRejectsUnknownConditionReference(): void
    {
        $schema = [
            'namespace' => 'document',
            'relations' => [
                'editor' => ['of' => 'user', 'condition' => 'missing_condition'],
            ],
        ];

        self::assertContains(
            "Relation 'editor' references unknown condition: missing_condition",
            ModelSchemaValidator::validate($schema)
        );
    }

    public function testRejectsInvalidSubjectType(): void
    {
        $schema = [
            'namespace' => 'document',
            'relations' => [
                'viewer' => ['of' => 'Team#Member'],
            ],
        ];

        self::assertContains(
            "Relation 'viewer' has invalid subject type: Team#Member",
            ModelSchemaValidator::validate($schema)
        );
    }
}

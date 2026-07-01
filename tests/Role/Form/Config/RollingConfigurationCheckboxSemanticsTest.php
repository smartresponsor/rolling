<?php

declare(strict_types=1);

namespace App\Rolling\Tests\Role\Form\Config;

use App\Rolling\Form\Config\RollingConfigurationRoleHierarchyFormType;
use App\Rolling\Form\Config\RollingConfigurationRoleRuntimeFormType;
use App\Rolling\Value\Form\Config\RollingConfigurationRoleHierarchyData;
use App\Rolling\Value\Form\Config\RollingConfigurationRoleRuntimeData;
use Symfony\Component\Form\Test\TypeTestCase;

final class RollingConfigurationCheckboxSemanticsTest extends TypeTestCase
{
    public function testRuntimeCheckboxOmittedInFullSubmitBecomesFalse(): void
    {
        $data = new RollingConfigurationRoleRuntimeData();
        $data->roleEnabled = true;

        $form = $this->factory->create(RollingConfigurationRoleRuntimeFormType::class, $data);
        $form->submit([
            'rolePolicyNamespace' => 'role',
            'roleAdminNamespace' => 'role-admin',
            'roleAuditNamespace' => 'role-audit',
            'roleOpsDir' => '/tmp/ops',
            'roleSdkNamespace' => 'Rolling\\SDK\\V2',
        ]);

        self::assertTrue($form->isSynchronized());
        self::assertFalse($data->roleEnabled);
    }

    public function testRuntimeCheckboxOmittedInPatchSubmitKeepsExistingValue(): void
    {
        $data = new RollingConfigurationRoleRuntimeData();
        $data->roleEnabled = true;

        $form = $this->factory->create(RollingConfigurationRoleRuntimeFormType::class, $data);
        $form->submit([
            'rolePolicyNamespace' => 'role-patch',
        ], false);

        self::assertTrue($form->isSynchronized());
        self::assertTrue($data->roleEnabled);
        self::assertSame('role-patch', $data->rolePolicyNamespace);
    }

    public function testHierarchyCheckboxesOmittedInFullSubmitBecomeFalse(): void
    {
        $data = new RollingConfigurationRoleHierarchyData();
        $data->roleHierarchyEnabled = true;
        $data->roleHierarchyReviewRequired = true;

        $form = $this->factory->create(RollingConfigurationRoleHierarchyFormType::class, $data);
        $form->submit([
            'roleHierarchyBootstrapViewerRole' => 'administration.viewer',
            'roleHierarchyBootstrapOperatorRole' => 'administration.operator',
            'roleHierarchyBootstrapSecurityAdminRole' => 'administration.security_admin',
            'roleHierarchyDefaultEdges' => "administration.operator>administration.viewer\nadministration.security_admin>administration.operator",
        ]);

        self::assertTrue($form->isSynchronized());
        self::assertFalse($data->roleHierarchyEnabled);
        self::assertFalse($data->roleHierarchyReviewRequired);
        self::assertSame([
            'administration.operator>administration.viewer',
            'administration.security_admin>administration.operator',
        ], $data->roleHierarchyDefaultEdges);
    }

    public function testHierarchyCheckboxesSubmittedAsOnBecomeTrue(): void
    {
        $data = new RollingConfigurationRoleHierarchyData();
        $data->roleHierarchyEnabled = false;
        $data->roleHierarchyReviewRequired = false;

        $form = $this->factory->create(RollingConfigurationRoleHierarchyFormType::class, $data);
        $form->submit([
            'roleHierarchyEnabled' => '1',
            'roleHierarchyReviewRequired' => '1',
            'roleHierarchyBootstrapViewerRole' => 'administration.viewer',
            'roleHierarchyBootstrapOperatorRole' => 'administration.operator',
            'roleHierarchyBootstrapSecurityAdminRole' => 'administration.security_admin',
            'roleHierarchyDefaultEdges' => '',
        ]);

        self::assertTrue($form->isSynchronized());
        self::assertTrue($data->roleHierarchyEnabled);
        self::assertTrue($data->roleHierarchyReviewRequired);
        self::assertSame([], $data->roleHierarchyDefaultEdges);
    }
}

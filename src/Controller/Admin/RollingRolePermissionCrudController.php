<?php

declare(strict_types=1);

namespace App\Rolling\Controller\Admin;

use App\Rolling\Entity\Acl\RollingRolePermission;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

final class RollingRolePermissionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return RollingRolePermission::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Rolling role permission')
            ->setEntityLabelInPlural('Rolling role permissions')
            ->setDefaultSort(['roleKey' => 'ASC', 'permissionKey' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('roleKey');
        yield TextField::new('permissionKey');
        yield TextField::new('scopePattern');
        yield ChoiceField::new('effect')->setChoices([
            'Allow' => 'allow',
            'Deny' => 'deny',
        ]);
    }
}

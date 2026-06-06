<?php

declare(strict_types=1);

namespace App\Rolling\Controller\Admin;

use App\Rolling\Entity\Acl\RollingPermission;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

final class RollingPermissionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return RollingPermission::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Rolling permission')
            ->setEntityLabelInPlural('Rolling permissions')
            ->setDefaultSort(['componentName' => 'ASC', 'permissionKey' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('permissionKey');
        yield TextField::new('componentName');
        yield TextareaField::new('description')->hideOnIndex();
    }
}

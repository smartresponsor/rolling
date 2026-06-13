<?php

declare(strict_types=1);

namespace App\Rolling\Controller\Admin;

use App\Rolling\Entity\Role\RoleEntity;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

final class RollingRoleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return RoleEntity::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Rolling role')
            ->setEntityLabelInPlural('Rolling roles')
            ->setDefaultSort(['roleKey' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('roleKey');
        yield TextField::new('label');
        yield BooleanField::new('systemRole');
        yield BooleanField::new('enabled');
    }
}

<?php

declare(strict_types=1);

namespace App\Rolling\Controller\Admin;

use App\Rolling\Entity\Acl\RollingSubjectRoleAssignment;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

final class RollingSubjectRoleAssignmentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return RollingSubjectRoleAssignment::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Rolling subject role assignment')
            ->setEntityLabelInPlural('Rolling subject role assignments')
            ->setDefaultSort(['assignedAt' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('subjectIdentifier');
        yield TextField::new('roleKey');
        yield TextField::new('scopeKey');
        yield DateTimeField::new('assignedAt')->hideOnForm();
    }
}

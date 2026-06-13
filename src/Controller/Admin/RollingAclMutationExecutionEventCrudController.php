<?php

declare(strict_types=1);

namespace App\Rolling\Controller\Admin;

use App\Rolling\Entity\Role\RoleAclMutationExecutionEventEntity;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

final class RollingAclMutationExecutionEventCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return RoleAclMutationExecutionEventEntity::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Rolling mutation execution')
            ->setEntityLabelInPlural('Rolling mutation executions')
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW, Action::EDIT, Action::DELETE);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id');
        yield TextField::new('requestKey');
        yield TextField::new('mutationType');
        yield TextField::new('subjectIdentifier');
        yield TextField::new('permissionOrRoleKey')->hideOnIndex();
        yield TextField::new('scopeKey')->hideOnIndex();
        yield TextField::new('requestedBySubject')->hideOnIndex();
        yield TextField::new('status');
        yield BooleanField::new('succeeded');
        yield TextareaField::new('safeMessage')->hideOnIndex();
        yield ArrayField::new('safeContext')->onlyOnDetail();
        yield DateTimeField::new('createdAt');
    }
}

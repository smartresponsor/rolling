<?php

declare(strict_types=1);

namespace App\Rolling\Controller\Admin;

use App\Rolling\Entity\Acl\RollingAclRule;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

final class RollingAclRuleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return RollingAclRule::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Rolling ACL rule')
            ->setEntityLabelInPlural('Rolling ACL rules')
            ->setDefaultSort(['subjectIdentifier' => 'ASC', 'permissionKey' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('subjectIdentifier');
        yield TextField::new('permissionKey');
        yield TextField::new('scopeKey');
        yield ChoiceField::new('effect')->setChoices([
            'Allow' => 'allow',
            'Deny' => 'deny',
        ]);
        yield ArrayField::new('conditions')->hideOnForm();
        yield BooleanField::new('enabled');
    }
}

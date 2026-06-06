<?php

declare(strict_types=1);

namespace App\Rolling\Controller\Admin;

use App\Rolling\Entity\Acl\RollingAclMutationExecutionEventEntity;
use App\Rolling\Entity\Acl\RollingAclRule;
use App\Rolling\Entity\Acl\RollingPermission;
use App\Rolling\Entity\Acl\RollingRole;
use App\Rolling\Entity\Acl\RollingRolePermission;
use App\Rolling\Entity\Acl\RollingSubjectRoleAssignment;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RollingDashboardController extends AbstractDashboardController
{
    #[Route('/admin/rolling', name: 'rolling_admin')]
    public function index(): Response
    {
        return $this->render('admin/rolling/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Rolling')
            ->setFaviconPath('favicon.ico');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Rolling dashboard', 'fa fa-shield-halved');
        yield MenuItem::section('ACL catalog');
        yield MenuItem::linkToCrud('Roles', 'fa fa-user-shield', RollingRole::class);
        yield MenuItem::linkToCrud('Permissions', 'fa fa-key', RollingPermission::class);
        yield MenuItem::linkToCrud('Role permissions', 'fa fa-link', RollingRolePermission::class);
        yield MenuItem::linkToCrud('Subject assignments', 'fa fa-users-gear', RollingSubjectRoleAssignment::class);
        yield MenuItem::linkToCrud('ACL rules', 'fa fa-scale-balanced', RollingAclRule::class);
        yield MenuItem::section('Audit');
        yield MenuItem::linkToCrud('Mutation executions', 'fa fa-clock-rotate-left', RollingAclMutationExecutionEventEntity::class);
    }
}

<?php

declare(strict_types=1);

namespace App\Rolling\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/ea/role', routeName: 'rolling_admin')]
final class RollingDashboardController extends AbstractDashboardController
{
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
        yield MenuItem::linkTo(RollingRoleCrudController::class, 'Roles', 'fa fa-user-shield');
        yield MenuItem::linkTo(RollingRolePermissionCrudController::class, 'Role permissions', 'fa fa-link');
        yield MenuItem::linkTo(RollingSubjectRoleAssignmentCrudController::class, 'Subject assignments', 'fa fa-users-gear');
        yield MenuItem::linkTo(RollingAclRuleCrudController::class, 'ACL rules', 'fa fa-scale-balanced');
        yield MenuItem::section('Audit');
        yield MenuItem::linkTo(RollingAclMutationExecutionEventCrudController::class, 'Mutation executions', 'fa fa-clock-rotate-left');
    }
}

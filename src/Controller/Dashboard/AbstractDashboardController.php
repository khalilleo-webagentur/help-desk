<?php

declare(strict_types=1);

namespace App\Controller\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AbstractDashboardController extends AbstractController
{
    protected function denyAccessUnlessGrantedRoleCustomer(): void
    {
        $this->denyAccessUnlessGranted('ROLE_CUSTOMER');
    }

    protected function denyAccessUnlessGrantedRoleSuperAdmin(): void
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
    }

    protected function isSuperAdmin(): bool
    {
        return $this->isGranted('ROLE_SUPER_ADMIN');
    }
}
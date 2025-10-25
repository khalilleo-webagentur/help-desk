<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\Tools;

use App\Controller\Dashboard\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/tools/m6a7g6fhn8arh9kk')]
class IndexController extends AbstractDashboardController
{
    #[Route('/home', name: 'app_dashboard_tools_index')]
    public function index(): Response
    {
        $this->denyAccessUnlessGrantedRoleSuperAdmin();

        return $this->render('dashboard/tools/index.html.twig');
    }
}

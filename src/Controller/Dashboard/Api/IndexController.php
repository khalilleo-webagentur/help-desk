<?php

namespace App\Controller\Dashboard\Api;

use App\Controller\Dashboard\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/api/v1')]
class IndexController extends AbstractDashboardController
{
    #[Route('/documentation', name: 'app_dashboard_api_doc_index', methods: ['GET'])]
    public function index(): Response
    {
        $this->denyAccessUnlessGrantedRoleSuperAdmin();

        return $this->render('dashboard/api/index.html.twig');
    }
}
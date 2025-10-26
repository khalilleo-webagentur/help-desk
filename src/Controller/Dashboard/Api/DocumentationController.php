<?php

namespace App\Controller\Dashboard\Api;

use App\Controller\Dashboard\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DocumentationController extends AbstractDashboardController
{
    #[Route('/dashboard/api/v1/documentation', name: 'app_dashboard_api_doc_index', methods: ['GET'])]
    public function index(): Response
    {
        $this->denyAccessUnlessGrantedRoleSuperAdmin();

        $userAgent = $this->getParameter('api_user_agent');
        $apiKey = $this->getParameter('api_key');

        return $this->render('dashboard/api/documentation.html.twig', [
            'userAgent' => $userAgent,
            'apiKey' => $apiKey,
        ]);
    }
}
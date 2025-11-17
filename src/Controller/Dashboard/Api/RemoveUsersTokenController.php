<?php

namespace App\Controller\Dashboard\Api;

use App\Controller\Dashboard\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/api/v1/documentation/remove-users-token')]
class RemoveUsersTokenController extends AbstractDashboardController
{
    #[Route('/', name: 'app_dashboard_api_doc_remove_token_index', methods: ['GET'])]
    public function index(): Response
    {
        $this->denyAccessUnlessGrantedRoleSuperAdmin();

        $userAgent = $this->getParameter('api_user_agent');
        $apiKey = $this->getParameter('api_key');

        return $this->render('dashboard/api/docs/clear_users_token.html.twig', [
            'userAgent' => $userAgent,
            'apiKey' => $apiKey,
        ]);
    }
}
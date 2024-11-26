<?php

declare(strict_types=1);

namespace App\Controller\Dashboard;

use App\Service\UserService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('dashboard/o3o4v1v3a1g8h2q2')]
class IndexController extends AbstractDashboardController
{
    public function __construct(
        private readonly UserService $userService,
    ){
    }

    #[Route('/home', name: 'app_dashboard_index')]
    public function index(): Response
    {
        $this->denyAccessUnlessGrantedRoleCustomer();

        $user = $this->getUser();

        $isAdmin = $this->userService->isAdmin($user);

        $countIssues = $isAdmin ? 124 : 9;
        $countClosedIssues = $isAdmin ? 124 : 9;
        $countIssuesInProgress = $isAdmin ? 124 : 9;
        $countOpenIssues = $isAdmin ? 124 : 9;

        return $this->render('dashboard/index.html.twig', [
            'countIssues' => $countIssues,
            'countClosedIssues' => $countClosedIssues,
            'countIssuesInProgress' => $countIssuesInProgress,
            'countOpenIssues' => $countOpenIssues,
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Controller\Dashboard;

use App\Service\TicketService;
use App\Service\TicketStatusService;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('dashboard/o3o4v1v3a1g8h2q2')]
class IndexController extends AbstractDashboardController
{
    public function __construct(
        private readonly UserService         $userService,
        private readonly TicketService       $ticketService,
        private readonly TicketStatusService $ticketStatusService,
    ) {
    }

    #[Route('/home', name: 'app_dashboard_index')]
    public function index(): Response
    {
        $this->denyAccessUnlessGrantedRoleCustomer();

        $user = $this->getUser();

        $isAdmin = $this->userService->isAdmin($user);

        // all of them
        $countIssues = $isAdmin
            ? $this->ticketService->countAll()
            : $this->ticketService->countAllByUser($user);

        // only status open
        $status = $this->ticketStatusService->getOneByName('Open');

        $countOpenIssues = $isAdmin
            ? $this->ticketService->countStatus($status)
            : $this->ticketService->countStatusByUser($user, $status);

        // only status closed
        $status = $this->ticketStatusService->getOneByName('Closed');

        $countClosedIssues = $isAdmin
            ? $this->ticketService->countStatus($status)
            : $this->ticketService->countStatusByUser($user, $status);

        // only status resolved
        $status = $this->ticketStatusService->getOneByName('Resolved');

        $countResolvedIssues = $isAdmin
            ? $this->ticketService->countStatus($status)
            : $this->ticketService->countStatusByUser($user, $status);

        // only status in progress
        $status = $this->ticketStatusService->getOneByName('In Progress');

        $countIssuesInProgress = $isAdmin
            ? $this->ticketService->countStatus($status)
            : $this->ticketService->countStatusByUser($user, $status);

        return $this->render('dashboard/index.html.twig', [
            'countIssues' => $countIssues,
            'countOpenIssues' => $countOpenIssues,
            'countClosedIssues' => $countClosedIssues + $countResolvedIssues,
            'countIssuesInProgress' => $countIssuesInProgress,

        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Controller\Dashboard;

use App\Helper\AppHelper;
use App\Service\TicketService;
use App\Service\TicketStatusService;
use App\Service\UserService;
use App\Traits\FormValidationTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('dashboard/i4a7y5t7i2q4w5x2')]
class IndexController extends AbstractDashboardController
{
    use FormValidationTrait;

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

        $isSuperAdminOrNinja = $this->isSuperAdmin() || $user->isNinja();
        $company = $user->getCompany();

        // all of them
        $countIssues = $isSuperAdminOrNinja
            ? $this->ticketService->countAll()
            : $this->ticketService->countAllByCompany($company);

        // only status open
        $status = $this->ticketStatusService->getOneByName(AppHelper::STATUS_OPEN);

        $countOpenIssues = $isSuperAdminOrNinja
            ? $this->ticketService->countStatus($status)
            : $this->ticketService->countAllByCompanyAndStatus($company, $status);

        // only status closed
        $status = $this->ticketStatusService->getOneByName(AppHelper::STATUS_CLOSED);

        $countClosedIssues = $isSuperAdminOrNinja
            ? $this->ticketService->countStatus($status)
            : $this->ticketService->countAllByCompanyAndStatus($company, $status);

        // only status resolved
        $status = $this->ticketStatusService->getOneByName(AppHelper::STATUS_RESOLVED);

        $countResolvedIssues = $isSuperAdminOrNinja
            ? $this->ticketService->countStatus($status)
            : $this->ticketService->countAllByCompanyAndStatus($company, $status);

        // only status in progress
        $status = $this->ticketStatusService->getOneByName(AppHelper::STATUS_IN_PROGRESS);

        $countIssuesInProgress = $isSuperAdminOrNinja
            ? $this->ticketService->countStatus($status)
            : $this->ticketService->countAllByCompanyAndStatus($company, $status);

        return $this->render('dashboard/index.html.twig', [
            'countIssues' => $countIssues,
            'countOpenIssues' => $countOpenIssues,
            'countClosedIssues' => $countClosedIssues + $countResolvedIssues,
            'countIssuesInProgress' => $countIssuesInProgress,
        ]);
    }
}

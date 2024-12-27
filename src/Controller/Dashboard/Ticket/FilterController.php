<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\Ticket;

use App\Controller\Dashboard\AbstractDashboardController;
use App\Service\TicketService;
use App\Service\UserService;
use App\Traits\FormValidationTrait;
use DateTime;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/ticket/filter/fil4m6e5l4y87ter')]
class FilterController extends AbstractDashboardController
{
    use FormValidationTrait;

    private const DASHBOARD_TICKETS_ROUTE = 'app_dashboard_tickets_index';

    public function __construct(
        private readonly UserService   $userService,
        private readonly TicketService $ticketService,
    ) {
    }

    #[Route('/filter', name: 'app_dashboard_ticket_filter', methods: ['GET', 'POST'])]
    public function index(Request $request): RedirectResponse|Response
    {
        $this->denyAccessUnlessGrantedRoleSuperAdmin();
        $user = $this->getUser();

        $companyId = $this->validateNumber($request->request->get('n9w9b5a2'));
        $projectId = $this->validateNumber($request->request->get('i5x5y0k6'));
        $labelId = $this->validateNumber($request->request->get('k8b7d6z0'));
        $statusId = $this->validateNumber($request->request->get('v4b6v7u1'));
        $priorityId = $this->validateNumber($request->request->get('pT4bG2sO'));
        $issueCreatedBy = $this->validate($request->request->get('k4j0e2k9'));
        $issueAssignedTo = $this->validate($request->request->get('n7m2r3m5'));
        $ticketNo = $this->validateNumber($request->request->get('f6a0f1i8'));
        $ticketTitle = $this->validate($request->request->get('e2j0m2m5'));
        $dateFrom = $this->validate($request->request->get('o2o0b4t1'));
        $dateTo = $this->validate($request->request->get('a1n3m1j2'));

        $issueDateFrom = DateTime::createFromFormat('Y-m-d', $dateFrom);
        $issueDateTo = DateTime::createFromFormat('Y-m-d', $dateTo);

        if (false === $issueDateFrom || false === $issueDateTo) {
            $this->addFlash('warning', 'Invalid date format');
            return $this->redirectToRoute(self::DASHBOARD_TICKETS_ROUTE);
        }

        $issueDateFrom->modify('00:00:00');
        $issueDateTo->modify('23:59:59');

        $issues = $this->ticketService->getAllByCriteria(
            $projectId,
            $statusId,
            $priorityId,
            $labelId,
            $companyId,
            $issueCreatedBy,
            $issueAssignedTo,
            $ticketNo,
            $ticketTitle,
            $issueDateFrom,
            $issueDateTo
        );

        return $this->render('dashboard/tickets/filter.html.twig', [
            'issues' => $issues
        ]);
    }
}

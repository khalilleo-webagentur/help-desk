<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\Ticket;

use App\Controller\Dashboard\AbstractDashboardController;
use App\Service\TicketService;
use App\Service\UserService;
use App\Traits\FormValidationTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/ticket/filter/v6q4m6e5l4y8x4m1')]
class FilterController extends AbstractDashboardController
{
    use FormValidationTrait;

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
        $issueByName = $this->validate($request->request->get('k4j0e2k9'));
        $assigneeName = $this->validate($request->request->get('n7m2r3m5'));
        $ticketNo = $this->validate($request->request->get('f6a0f1i8'));
        $ticketTitle = $this->validate($request->request->get('e2j0m2m5'));
        $dateFrom = $this->validate($request->request->get('o2o0b4t1'));
        $dateTo = $this->validate($request->request->get('a1n3m1j2'));

        // @TODO ...

        return $this->render('dashboard/tickets/search.html.twig');
    }
}

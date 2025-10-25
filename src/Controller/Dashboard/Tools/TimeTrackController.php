<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\Tools;

use App\Controller\Dashboard\AbstractDashboardController;
use App\Service\TicketService;
use App\Traits\FormValidationTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/tool/time-track/m8zy1z3ilt2ora1')]
class TimeTrackController extends AbstractDashboardController
{
    use FormValidationTrait;

    private const string DASHBOARD_TICKETS_ROUTE = 'app_dashboard_tickets_index';
    private const string DASHBOARD_TICKET_VIEW_ROUTE = 'app_dashboard_ticket_view';

    public function __construct(
        private readonly TicketService $ticketService
    ) {
    }

    #[Route('/overview', name: 'app_dashboard_tool_time_track_index')]
    public function index(): Response
    {
        $this->denyAccessUnlessGrantedRoleSuperAdmin();

        $tickets = $this->ticketService->getAllNotClosedAndNotResolved();

        return $this->render('dashboard/tools/time-track/index.html.twig', [
            'tickets' => $tickets
        ]);
    }
}

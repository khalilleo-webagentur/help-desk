<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\TicketStatus;

use App\Controller\Dashboard\AbstractDashboardController;
use App\Service\TicketStatusService;
use App\Traits\FormValidationTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/ticket-status/helper/g8b3y9y2f5u6k2v1')]
class HelperController extends AbstractDashboardController
{
    use FormValidationTrait;

    private const string DASHBOARD_TICKET_STATUS_ROUTE = 'app_dashboard_ticket_status_index';

    public function __construct(
        private readonly TicketStatusService $ticketStatusService
    ) {
    }

    #[Route('/rearrange-positions', name: 'app_dashboard_ticket_status_positions_store', methods: 'POST')]
    public function storePositions(Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        foreach ($request->request->all('positions') as $param => $value) {

            $ticketStatus = $this->ticketStatusService->getById($param + 1);

            if ($ticketStatus !== null) {
                $this->ticketStatusService->save(
                    $ticketStatus->setPosition((int)$value)
                );
            }
        }

        $this->addFlash('success', 'All Positions has been rearranged.');

        return $this->redirectToRoute(self::DASHBOARD_TICKET_STATUS_ROUTE);
    }
}

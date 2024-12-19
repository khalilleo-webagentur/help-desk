<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\Ticket;

use App\Controller\Dashboard\AbstractDashboardController;
use App\Entity\TicketStatus;
use App\Service\TicketActivitiesService;
use App\Service\TicketService;
use App\Service\TicketStatusService;
use App\Traits\FormValidationTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/ticket/search/y9t7y1z3i0t2o8a1')]
class HelperController extends AbstractDashboardController
{
    use FormValidationTrait;

    private const DASHBOARD_TICKETS_ROUTE = 'app_dashboard_tickets_index';
    private const DASHBOARD_TICKET_VIEW_ROUTE = 'app_dashboard_ticket_view';

    public function __construct(
        private readonly TicketService           $ticketService,
        private readonly TicketStatusService     $ticketStatusService,
        private readonly TicketActivitiesService $ticketActivitiesService,
    ) {
    }

    #[Route('/store-ticket/{id}', name: 'app_dashboard_ticket_log_time_store', methods: 'POST')]
    public function logTime(?string $id, Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGrantedRoleCustomer();

        $minutes = $this->validateNumber($request->request->get('iO7nMi'));
        $projectId = $this->validateNumber($request->get('pid'));

        if ($minutes <= 0) {
            $this->addFlash('warning', 'Minutes must be greater than zero.');
            return $this->redirectToRoute(self::DASHBOARD_TICKETS_ROUTE);
        }

        if ($projectId <= 0) {
            $this->addFlash('warning', 'Project ID must be greater than zero.');
            return $this->redirectToRoute(self::DASHBOARD_TICKET_VIEW_ROUTE);
        }

        $ticketId = $this->validateNumber($id);

        $issue = $this->ticketService->getById($ticketId);

        if (!$issue) {
            $this->addFlash('warning', 'Ticket not found.');
            return $this->redirectToRoute(self::DASHBOARD_TICKET_VIEW_ROUTE);
        }

        $this->ticketService->save($issue->setTimeSpentInMinutes(
            $minutes + $issue->getTimeSpentInMinutes())
        );

        $user = $this->getUser();
        $message = sprintf('%s logged time spent of issue "%s" minutes', $user->getName(), $minutes);
        $this->ticketActivitiesService->add($issue, $user, $message);

        $this->addFlash('success', 'Time is being logged.');

        return $this->redirectToRoute(self::DASHBOARD_TICKET_VIEW_ROUTE, [
            'id' => $ticketId,
            'pid' => $projectId
        ]);
    }

    #[Route('/store-status/{id}', name: 'app_dashboard_ticket_store_issue_status', methods: 'POST')]
    public function changeStatus(?string $id, Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGrantedRoleCustomer();

        $statusId = $this->validateNumber($request->request->get('MliO7nMi'));
        $projectId = $this->validateNumber($request->get('pid'));

        if ($statusId <= 0) {
            $this->addFlash('warning', 'Status ID must be greater than zero.');
            return $this->redirectToRoute(self::DASHBOARD_TICKETS_ROUTE);
        }

        if ($projectId <= 0) {
            $this->addFlash('warning', 'Project ID must be greater than zero.');
            return $this->redirectToRoute(self::DASHBOARD_TICKET_VIEW_ROUTE);
        }

        $ticketId = $this->validateNumber($id);

        $issue = $this->ticketService->getById($ticketId);

        if (!$issue) {
            $this->addFlash('warning', 'Ticket not found.');
            return $this->redirectToRoute(self::DASHBOARD_TICKET_VIEW_ROUTE);
        }

        $status = $this->ticketStatusService->getById($statusId);

        $this->ticketService->save($issue->setStatus($status));

        $user = $this->getUser();
        $message = sprintf('%s updated status of issue to "%s"', $user->getName(), $status->getName());
        $this->ticketActivitiesService->add(
            $issue,
            $user,
            $message
        );

        $this->addFlash('success', 'Status is being updated.');

        return $this->redirectToRoute(self::DASHBOARD_TICKET_VIEW_ROUTE, [
            'id' => $ticketId,
            'pid' => $projectId
        ]);
    }
}

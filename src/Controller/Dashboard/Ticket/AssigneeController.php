<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\Ticket;

use App\Controller\Dashboard\AbstractDashboardController;
use App\Service\TicketActivitiesService;
use App\Service\TicketService;
use App\Service\TicketStatusService;
use App\Service\UserService;
use App\Traits\FormValidationTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/ticket-assignee/search/7607y1z3i0t2o8a1')]
class AssigneeController extends AbstractDashboardController
{
    use FormValidationTrait;

    private const string DASHBOARD_TICKETS_ROUTE = 'app_dashboard_tickets_index';
    private const string DASHBOARD_TICKET_VIEW_ROUTE = 'app_dashboard_ticket_view';

    public function __construct(
        private readonly UserService             $userService,
        private readonly TicketService           $ticketService,
        private readonly TicketStatusService     $ticketStatusService,
        private readonly TicketActivitiesService $ticketActivitiesService,
    ) {
    }

    #[Route('/store/{id}', name: 'app_dashboard_ticket_store_assignee', methods: 'POST')]
    public function store(?string $id, Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGrantedRoleCustomer();

        $projectId = $this->validateNumber($request->get('pid'));

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

        $assigneeId = $this->validateNumber($request->request->get('assignee'));

        $assigner = $this->userService->getById($assigneeId);

        $this->ticketService->save($issue->setAssignee($assigner));

        $user = $this->getUser();
        $message = sprintf(
            'changed assignee to "%s"',
            $assigner ? $assigner->getName() : 'unassigned'
        );
        $this->ticketActivitiesService->add($issue, $user, $message);

        $this->addFlash('success', 'Assignee is being updated.');

        return $this->redirectToRoute(self::DASHBOARD_TICKET_VIEW_ROUTE, [
            'id' => $ticketId,
            'pid' => $projectId
        ]);
    }
}

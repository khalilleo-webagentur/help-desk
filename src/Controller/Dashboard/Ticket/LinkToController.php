<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\Ticket;

use App\Controller\Dashboard\AbstractDashboardController;
use App\Service\TicketActivitiesService;
use App\Service\TicketService;
use App\Traits\FormValidationTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/ticket-link-to/li8y1z3i0t2oa7z')]
class LinkToController extends AbstractDashboardController
{
    use FormValidationTrait;

    private const string DASHBOARD_TICKETS_ROUTE = 'app_dashboard_tickets_index';
    private const string DASHBOARD_TICKET_VIEW_ROUTE = 'app_dashboard_ticket_view';

    public function __construct(
        private readonly TicketService           $ticketService,
        private readonly TicketActivitiesService $ticketActivitiesService
    ) {
    }

    #[Route('/store/{id}', name: 'app_dashboard_ticket_link_to_store', methods: 'POST')]
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

        $backTo = $this->redirectToRoute(self::DASHBOARD_TICKET_VIEW_ROUTE, [
            'id' => $ticketId,
            'pid' => $projectId
        ]);

        $user = $this->getUser();

        if ($this->validateCheckbox($request->request->get('c1z3n6t4'))) {

            $message = sprintf('removed linked-issue "T-%s".', $issue->getLinkToTicket());
            $this->ticketActivitiesService->add($issue, $user, $message, true);
            $this->ticketService->linkIssue($issue, null, null);

            $this->addFlash('success', 'Linked issue has been removed.');

            return $backTo;
        }

        $linkToTicketNo = $this->validate($request->request->get('iNnO7nMi'));

        if (empty($linkToTicketNo)) {
            $this->addFlash('warning', 'Invalid link to ticket.');
            return $backTo;
        }

        $linkToTicketNo = str_replace('T-', '', $linkToTicketNo);

        $targetIssue = $this->ticketService->getByTicketNo((int)$linkToTicketNo);

        if (!$linkToTicketNo || !$targetIssue) {
            $this->addFlash('warning', 'Target issue could not be found.');
            return $backTo;
        }

        if ($linkToTicketNo === $issue->getLinkToTicket()) {
            $this->addFlash('warning', 'Issue cannot be linked on the same issue.');
            return $backTo;
        }

        $this->ticketService->linkIssue($issue, (string)$targetIssue->getTicketNo(), $targetIssue->getId());

        $message = sprintf('linked issue to issue "%s".', $linkToTicketNo);
        $this->ticketActivitiesService->add($issue, $user, $message, true);

        $this->addFlash('success', 'Issue has been linked.');

        return $backTo;
    }
}

<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\Ticket;

use App\Controller\Dashboard\AbstractDashboardController;
use App\Service\TicketService;
use App\Service\UserService;
use App\Traits\FormValidationTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/ticket/internal-note/1297y1z3i0t2o8am')]
class InternalNoteController extends AbstractDashboardController
{
    use FormValidationTrait;
    private const string DASHBOARD_TICKET_VIEW_ROUTE = 'app_dashboard_ticket_view';

    public function __construct(
        private readonly UserService    $userService,
        private readonly TicketService  $ticketService,
    ) {
    }

    #[Route('/add/{id}', name: 'app_dashboard_ticket_store_internal_note', methods: 'POST')]
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

        $note = $this->validateTextarea($request->request->get('content'), true);

        $this->ticketService->save($issue->setInternalNote($note));

        $this->addFlash('success', 'Issue internal-note has been saved.');

        return $this->redirectToRoute(self::DASHBOARD_TICKET_VIEW_ROUTE, [
            'id' => $ticketId,
            'pid' => $projectId
        ]);
    }

    #[Route('/delete/{id}', name: 'app_dashboard_ticket_store_internal_note_clear', methods: 'POST')]
    public function delete(?string $id, Request $request): RedirectResponse
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

        $this->ticketService->save($issue->setInternalNote(null));

        $this->addFlash('success', 'Issue internal-note has been saved.');

        return $this->redirectToRoute(self::DASHBOARD_TICKET_VIEW_ROUTE, [
            'id' => $ticketId,
            'pid' => $projectId
        ]);
    }
}

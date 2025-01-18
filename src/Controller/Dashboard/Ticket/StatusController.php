<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\Ticket;

use App\Controller\Dashboard\AbstractDashboardController;
use App\Helper\AppHelper;
use App\Mails\Dashboard\IssueResolvedMail;
use App\Service\TicketActivitiesService;
use App\Service\TicketService;
use App\Service\TicketStatusService;
use App\Service\UserSettingService;
use App\Traits\FormValidationTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/ticket-status/xst7y1z3i0t2o8a1')]
class StatusController extends AbstractDashboardController
{
    use FormValidationTrait;

    private const DASHBOARD_TICKETS_ROUTE = 'app_dashboard_tickets_index';
    private const DASHBOARD_TICKET_VIEW_ROUTE = 'app_dashboard_ticket_view';

    public function __construct(
        private readonly TicketService           $ticketService,
        private readonly TicketStatusService     $ticketStatusService,
        private readonly TicketActivitiesService $ticketActivitiesService,
        private readonly UserSettingService      $userSettingService,
    ) {
    }

    #[Route('/store/{id}', name: 'app_dashboard_ticket_store_issue_status', methods: 'POST')]
    public function store(?string $id, Request $request, IssueResolvedMail $issueResolvedMail): RedirectResponse
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
        $message = sprintf('changed status of issue to "%s"', strtolower($status->getName()));
        $this->ticketActivitiesService->add(
            $issue,
            $user,
            $message
        );

        if ((AppHelper::STATUS_CLOSED === strtoupper($status->getName())
            || AppHelper::STATUS_RESOLVED === strtoupper($status->getName()))
            && $this->userSettingService->notifyCustomerOnTicketStatusClosed($issue->getCustomer())
        ) {
            $this->addFlash(
                'notice',
                sprintf(
                    'The customer "%s" is being notified that the issue is being done.',
                    $issue->getCustomer()->getName())
            );

            $username = $issue->getCustomer()->getName();
            $userEmail = $issue->getCustomer()->getEmail();
            $issueResolvedMail->send($username, $userEmail, $issue->getTicketNo(), $issue->getTitle());
        }

        $this->addFlash('success', 'Status is being updated.');

        return $this->redirectToRoute(self::DASHBOARD_TICKET_VIEW_ROUTE, [
            'id' => $ticketId,
            'pid' => $projectId
        ]);
    }
}

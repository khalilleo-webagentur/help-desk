<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\Ticket;

use App\Controller\Dashboard\AbstractDashboardController;
use App\Service\TicketAttachmentsService;
use App\Service\TicketService;
use App\Service\UserService;
use App\Traits\FormValidationTrait;
use DateTime;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/ticket/filter-attachments/att4m6e5m4y8n4ter')]
class FilterController extends AbstractDashboardController
{
    use FormValidationTrait;

    private const DASHBOARD_TICKETS_ROUTE = 'app_dashboard_tickets_index';

    public function __construct(
        private readonly UserService              $userService,
        private readonly TicketService            $ticketService,
        private readonly TicketAttachmentsService $ticketAttachmentsService,
    ) {
    }

    #[Route('/q-issues', name: 'app_dashboard_ticket_filter', methods: ['GET', 'POST'])]
    public function filterIssues(Request $request): RedirectResponse|Response
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

    #[Route('/q-attachments', name: 'app_dashboard_ticket_filter_attachments', methods: ['GET', 'POST'])]
    public function filterAttachments(Request $request): RedirectResponse|Response
    {
        $this->denyAccessUnlessGrantedRoleSuperAdmin();

        $fileName = $this->validate($request->request->get('name'));
        $userId = $this->validateNumber($request->request->get('uId'));
        $fileNo = $this->validate($request->request->get('fileNo'));
        $type = $this->validate($request->request->get('type'));
        $from = $this->validate($request->request->get('dateFrom'));
        $to = $this->validate($request->request->get('dateTo'));

        $dateFrom = DateTime::createFromFormat('Y-m-d', $from);
        $dateTo = DateTime::createFromFormat('Y-m-d', $to);

        if (false === $dateFrom || false === $dateTo) {
            $this->addFlash('warning', 'Invalid date format');
            return $this->redirectToRoute(self::DASHBOARD_TICKETS_ROUTE);
        }

        $dateFrom->modify('00:00:00');
        $dateTo->modify('23:59:59');

        $attachments = $this->ticketAttachmentsService->getAllByCriteria(
            $fileName,
            $userId,
            $fileNo,
            $type,
            $dateFrom,
            $dateTo
        );

        return $this->render('dashboard/tickets/filter-attachments.html.twig', [
            'attachments' => $attachments
        ]);
    }

    #[Route('/q-notes', name: 'app_dashboard_ticket_filter_notes', methods: ['GET', 'POST'])]
    public function filterNotes(Request $request): RedirectResponse|Response
    {
        $this->denyAccessUnlessGrantedRoleSuperAdmin();

        $companyId = $this->validateNumber($request->request->get('company'));
        $note = $this->validate($request->request->get('note'));
        $isInternalNote = $this->validateCheckbox($request->request->get('iNote'));
        $isExternalNote = $this->validateCheckbox($request->request->get('eNote'));
        $from = $this->validate($request->request->get('dateFrom'));
        $to = $this->validate($request->request->get('dateTo'));

        $dateFrom = DateTime::createFromFormat('Y-m-d', $from);
        $dateTo = DateTime::createFromFormat('Y-m-d', $to);

        if (false === $dateFrom || false === $dateTo) {
            $this->addFlash('warning', 'Invalid date format');
            return $this->redirectToRoute(self::DASHBOARD_TICKETS_ROUTE);
        }

        $dateFrom->modify('00:00:00');
        $dateTo->modify('23:59:59');

        $issues = $this->ticketService->queryAllIssueNotes(
            $companyId,
            $note,
            $isInternalNote,
            $isExternalNote,
            $dateFrom,
            $dateTo
        );

        return $this->render('dashboard/tickets/filter-notes.html.twig', [
            'issues' => $issues
        ]);
    }
}

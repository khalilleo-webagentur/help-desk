<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\Ticket;

use App\Controller\Dashboard\AbstractDashboardController;
use App\Service\TicketActivitiesService;
use App\Service\TicketService;
use App\Service\TimeTrackService;
use App\Traits\FormValidationTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/ticket/time-track/mk8y1z3i0t2o8a1')]
class TimeTrackController extends AbstractDashboardController
{
    use FormValidationTrait;

    private const string DASHBOARD_TICKETS_ROUTE = 'app_dashboard_tickets_index';
    private const string DASHBOARD_TICKET_VIEW_ROUTE = 'app_dashboard_ticket_view';

    public function __construct(
        private readonly TicketService           $ticketService,
        private readonly TimeTrackService        $timeTrackService,
        private readonly TicketActivitiesService $ticketActivitiesService
    ) {
    }

    #[Route('/new/{hash}', name: 'app_dashboard_ticket_time_track_new', methods: 'POST')]
    public function new(Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGrantedRoleSuperAdmin();

        $projectId = $this->validateNumber($request->request->get('pid'));

        if ($projectId <= 0) {
            $this->addFlash('warning', 'Project ID must be greater than zero.');
            return $this->redirectToRoute(self::DASHBOARD_TICKET_VIEW_ROUTE);
        }

        $ticketId = $this->validateNumber($request->request->get('id'));
        $issue = $this->ticketService->getById($ticketId);

        if (!$issue) {
            $this->addFlash('warning', 'Ticket not found.');
            return $this->redirectToRoute(self::DASHBOARD_TICKET_VIEW_ROUTE);
        }

        $redirectBack = $this->redirectToRoute(self::DASHBOARD_TICKET_VIEW_ROUTE, [
            'id' => $ticketId,
            'pid' => $projectId
        ]);

        $minutes = $this->validateNumber($request->request->get('iO7nMi'));

        if (!$minutes) {
            $this->addFlash('danger', 'Minutes should be greater than 0.');
            return $redirectBack;
        }

        $note = $this->validate($request->request->get('note'));

        if (!$note) {
            $this->addFlash('danger', 'The note field is required.');
            return $redirectBack;
        }

        $user = $this->getUser();
        $this->timeTrackService->add($user, $issue, $minutes, $note);

        $this->ticketService->save($issue->setTimeSpentInMinutes(
            $minutes + $issue->getTimeSpentInMinutes())
        );

        $message = sprintf('logged spent time "%s" minutes', $minutes);
        $this->ticketActivitiesService->add($issue, $user, $message);

        $this->addFlash('success', 'Time is being logged.');

        return $redirectBack;
    }

    #[Route('/store/{hash}', name: 'app_dashboard_ticket_time_track_store', methods: 'POST')]
    public function store(Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGrantedRoleSuperAdmin();

        $projectId = $this->validateNumber($request->request->get('pid'));

        if ($projectId <= 0) {
            $this->addFlash('warning', 'Project ID must be greater than zero.');
            return $this->redirectToRoute(self::DASHBOARD_TICKET_VIEW_ROUTE);
        }

        $ticketId = $this->validateNumber($request->request->get('id'));
        $issue = $this->ticketService->getById($ticketId);

        if (!$issue) {
            $this->addFlash('warning', 'Ticket not found.');
            return $this->redirectToRoute(self::DASHBOARD_TICKET_VIEW_ROUTE);
        }

        $redirectBack = $this->redirectToRoute(self::DASHBOARD_TICKET_VIEW_ROUTE, [
            'id' => $ticketId,
            'pid' => $projectId
        ]);

        $minutes = $this->validateNumber($request->request->get('minutes'));

        if (!$minutes) {
            $this->addFlash('danger', 'Minutes should be greater than 0.');
            return $redirectBack;
        }

        $note = $this->validate($request->request->get('note'));

        if (!$note) {
            $this->addFlash('danger', 'The note field is required.');
            return $redirectBack;
        }

        $timeTrack = $this->timeTrackService->getById(
            $this->validateNumber($request->request->get('ttId'))
        );

        if (!$timeTrack) {
            $this->addFlash('danger', 'Time track not found.');
            return $redirectBack;
        }

        $user = $this->getUser();
        $message = sprintf('modified logged time spent from %s to "%s" minutes', $timeTrack->getMinutes(), $minutes);
        $this->ticketActivitiesService->add($issue, $user, $message);
        $this->timeTrackService->store($timeTrack, $minutes, $note);

        $this->addFlash('success', 'Changes has been saved.');

        return $redirectBack;
    }

    #[Route('/delete/{hash}', name: 'app_dashboard_ticket_time_track_delete', methods: 'POST')]
    public function delete(Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGrantedRoleCustomer();

        $projectId = $this->validateNumber($request->request->get('pid'));

        if ($projectId <= 0) {
            $this->addFlash('warning', 'Project ID must be greater than zero.');
            return $this->redirectToRoute(self::DASHBOARD_TICKET_VIEW_ROUTE);
        }

        $issueId = $this->validateNumber($request->request->get('id'));
        $issue = $this->ticketService->getById($issueId);

        if (!$issue) {
            $this->addFlash('warning', 'Ticket not found.');
            return $this->redirectToRoute(self::DASHBOARD_TICKET_VIEW_ROUTE);
        }

        $timeTrack = $this->timeTrackService->getOneByTicketAndId(
            $issue,
            $this->validateNumber($request->request->get('tId'))
        );

        if (!$timeTrack) {
            $this->addFlash('warning', 'Time track not found.');
            return $this->redirectToRoute(self::DASHBOARD_TICKET_VIEW_ROUTE);
        }

        $user = $this->getUser();
        $message = sprintf('removed time spent "%s" minutes.', $timeTrack->getMinutes());
        $this->ticketActivitiesService->add($issue, $user, $message);

        $this->timeTrackService->delete($timeTrack, true);

        $this->addFlash('success', 'Time Track is being deleted.');

        return $this->redirectToRoute(self::DASHBOARD_TICKET_VIEW_ROUTE, [
            'id' => $issueId,
            'pid' => $projectId
        ]);
    }
}

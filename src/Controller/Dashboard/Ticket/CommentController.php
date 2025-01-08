<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\Ticket;

use App\Controller\Dashboard\AbstractDashboardController;
use App\Entity\TicketComment;
use App\Service\ProjectService;
use App\Service\TicketCommentsService;
use App\Service\TicketService;
use App\Service\UserService;
use App\Traits\FormValidationTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/ticket/comment/b3z7q5u8m3p6j5o0')]
class CommentController extends AbstractDashboardController
{
    use FormValidationTrait;

    private const DASHBOARD_TICKETS_ROUTE = 'app_dashboard_tickets_index';
    private const DASHBOARD_TICKET_SHOW_ROUTE = 'app_dashboard_ticket_view';

    public function __construct(
        private readonly UserService           $userService,
        private readonly TicketService         $ticketService,
        private readonly TicketCommentsService $ticketCommentsService,
        private readonly ProjectService        $projectService,
    ) {
    }

    #[Route('/add', name: 'app_dashboard_ticket_comment_new', methods: 'POST')]
    public function new(Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGrantedRoleCustomer();
        $user = $this->getUser();


        $project = $this->projectService->getById(
            $this->validateNumber($request->request->get('pId'))
        );

        if (!$project) {
            $this->addFlash('warning', 'Issue could not be found. E-0005');
            return $this->redirectToRoute(self::DASHBOARD_TICKETS_ROUTE);
        }

        $isAdmin = $this->userService->isAdmin($user);
        $ticketId = $this->validateNumber($request->request->get('tId'));

        $issue = $isAdmin
            ? $this->ticketService->getById($ticketId)
            : $this->ticketService->getOneByProjectAndId($project, $ticketId);

        if (!$issue) {
            $this->addFlash('warning', 'Issue could not be found.');
            return $this->redirectToRoute(self::DASHBOARD_TICKETS_ROUTE);
        }

        $backToRoute = $this->redirectToRoute(self::DASHBOARD_TICKET_SHOW_ROUTE, [
            'id' => $issue->getId(),
            'pid' => $issue->getProject()->getId()
        ]);

        $description = $this->validateTextarea($request->request->get('content'), true);

        if (empty(strip_tags($description))) {
            $this->addFlash('warning', 'Description could not be empty.');
            return $backToRoute;
        }

        $ticketComment = new TicketComment();

        $ticketComment->addCommentedBy($user);

        $this->ticketCommentsService->save(
            $ticketComment
                ->setTicket($issue)
                ->setDescription($description)
        );

        return $backToRoute;
    }
}

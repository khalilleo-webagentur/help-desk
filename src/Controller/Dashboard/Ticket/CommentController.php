<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\Ticket;

use App\Controller\Dashboard\AbstractDashboardController;
use App\Entity\TicketComment;
use App\Service\TicketCommentsService;
use App\Service\TicketService;
use App\Service\UserService;
use App\Traits\FormValidationTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/ticket/comment/7fo873g176snp7xm')]
class CommentController extends AbstractDashboardController
{
    use FormValidationTrait;

    private const DASHBOARD_TICKETS_ROUTE = 'app_dashboard_tickets_index';
    private const DASHBOARD_TICKET_SHOW_ROUTE = 'app_dashboard_ticket_view';

    public function __construct(
        private readonly UserService             $userService,
        private readonly TicketService           $ticketService,
        private readonly TicketCommentsService   $ticketCommentsService,
    ) {
    }

    #[Route('/add', name: 'app_dashboard_ticket_comment_new', methods: 'POST')]
    public function new(Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGrantedRoleCustomer();
        $user = $this->getUser();
        $isAdmin = $this->userService->isAdmin($user);
        $ticketId = $this->validateNumber($request->request->get('tId'));

        $issue = $isAdmin
            ? $this->ticketService->getById($ticketId)
            : $this->ticketService->getOneByCustomerAndId($user, $ticketId);

        if (!$issue) {
            $this->addFlash('warning', 'Issue could not be found.');
            return $this->redirectToRoute(self::DASHBOARD_TICKETS_ROUTE);
        }

        $backToRoute = $this->redirectToRoute(self::DASHBOARD_TICKET_SHOW_ROUTE, ['id' => $ticketId]);
        $description = $this->validateTextarea($request->request->get('content'), true);

        if (!$description) {
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

<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\Ticket;

use App\Controller\Dashboard\AbstractDashboardController;
use App\Entity\TicketComment;
use App\Helper\AppHelper;
use App\Mails\Dashboard\NotifyIssueCommentMail;
use App\Service\ProjectService;
use App\Service\SystemLogsService;
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

    private const string DASHBOARD_TICKETS_ROUTE = 'app_dashboard_tickets_index';
    private const string DASHBOARD_TICKET_SHOW_ROUTE = 'app_dashboard_ticket_view';

    public function __construct(
        private readonly UserService           $userService,
        private readonly TicketService         $ticketService,
        private readonly TicketCommentsService $ticketCommentsService,
        private readonly ProjectService        $projectService,
        private readonly SystemLogsService     $systemLogsService,
    ) {
    }

    #[Route('/add', name: 'app_dashboard_ticket_comment_new', methods: 'POST')]
    public function new(Request $request, NotifyIssueCommentMail $notifyIssueCommentMail): RedirectResponse
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

        $isSuperAdmin = $this->isSuperAdmin();
        $ticketId = $this->validateNumber($request->request->get('tId'));

        $issue = $isSuperAdmin
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

        if (!$description || empty(strip_tags($description))) {
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

        if ($this->validateCheckbox($request->request->get('notify'))) {

            $name = $this->getParameter('webmasterName');
            $email = $this->getParameter('webmasterEmail');

            if ($issue->getAssignee()) {
                $name = $issue->getAssignee()->getName();
                $email = $issue->getAssignee()->getEmail();
            }

            $customer = $issue->getCustomer();
            $message = strip_tags($description);

            $notifyIssueCommentMail->send(
                $isSuperAdmin ? $customer->getName() : $name,
                $isSuperAdmin ? $customer->getEmail() : $email,
                $issue->getTicketNo(),
                $issue->getTitle(),
                $message
            );

            $this->systemLogsService->create(AppHelper::SYSTEM_LOG_EVENT_TICKET_COMMENT, $message);
        }

        return $backToRoute;
    }

    #[Route('/delete', name: 'app_dashboard_ticket_comment_delete', methods: 'POST')]
    public function delete(Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGrantedRoleSuperAdmin();

        $commentId = $this->validateNumber($request->request->get('comment'));

        $user = $this->getUser();

        if ($comment = $this->ticketCommentsService->getById($commentId)) {
            $this->ticketCommentsService->delete($user, $comment);
            $this->addFlash('success', 'Comment has been deleted.');
        }

        return $this->redirectToRoute(self::DASHBOARD_TICKETS_ROUTE);
    }
}

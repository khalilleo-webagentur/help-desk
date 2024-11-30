<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\Ticket;

use App\Controller\Dashboard\AbstractDashboardController;
use App\Service\Core\FileHandlerService;
use App\Service\TicketAttachmentsService;
use App\Service\TicketService;
use App\Service\UserService;
use App\Traits\FormValidationTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/ticket/71o8i7h1v7lnp7xm')]
class HelperController extends AbstractDashboardController
{
    use FormValidationTrait;

    private const DASHBOARD_TICKETS_ROUTE = 'app_dashboard_tickets_index';

    public function __construct(
        private readonly UserService             $userService,
        private readonly TicketService           $ticketService,
        private readonly TicketAttachmentsService $ticketAttachmentsService,
    ) {
    }

    #[Route('/image/{hash}', name: 'app_dashboard_ticket_helper_show', methods: ['GET', 'POST'])]
    public function view(?string $hash, Request $request, FileHandlerService $fileHandlerService): Response
    {
        if ($request->getMethod() === 'GET') {
            $this->addFlash('notice', 'The file that you are looking for could not be found.');
            return $this->redirectToRoute('app_home');
        }

        $this->denyAccessUnlessGrantedRoleCustomer();

        $user = $this->getUser();

        $isAdmin = $this->userService->isAdmin($user);

        $id = $this->validateNumber($request->request->get('tId'));

        $issue = $isAdmin
            ? $this->ticketService->getById($id)
            : $this->ticketService->getOneByCustomerAndId($user, $id);

        if (!$issue) {
            $this->addFlash('warning', 'Issue could not be found.');
            return $this->redirectToRoute(self::DASHBOARD_TICKETS_ROUTE);
        }

        $attachment = $this->ticketAttachmentsService->getOneByTicket($issue);

        if (!$attachment) {
            $this->addFlash('warning', 'Attachment could not be found.');
            return $this->redirectToRoute(self::DASHBOARD_TICKETS_ROUTE);
        }

        $dir = $this->getParameter('attachments');

        if (!$fileHandlerService->isFileExistsInDir($dir, $attachment->getFilename())) {
            $this->addFlash('warning', 'Attachment could not be found.');
            return $this->redirectToRoute(self::DASHBOARD_TICKETS_ROUTE);
        }

        return $this->file($dir . '/' . $attachment->getFilename(), $hash, ResponseHeaderBag::DISPOSITION_INLINE);
    }
}

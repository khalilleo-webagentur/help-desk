<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\Ticket;

use App\Controller\Dashboard\AbstractDashboardController;
use App\Service\TicketService;
use App\Service\UserService;
use App\Traits\FormValidationTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/ticket/search/4fo473g146sn47xk')]
class SearchController extends AbstractDashboardController
{
    use FormValidationTrait;

    public function __construct(
        private readonly UserService   $userService,
        private readonly TicketService $ticketService,
    ) {
    }

    #[Route('/q', name: 'app_dashboard_ticket_search', methods: ['GET', 'POST'])]
    public function new(Request $request): RedirectResponse|Response
    {
        $this->denyAccessUnlessGrantedRoleCustomer();
        $user = $this->getUser();
        $isAdmin = $this->userService->isAdmin($user);

        $ticketNo = $this->validateNumber($request->request->get('keyword'));
        $backToRoute = $this->redirectToRoute($request->request->get('backTo'));

        $issue = $isAdmin
            ? $this->ticketService->getByTicketNo($ticketNo)
            : $this->ticketService->getOneByCustomerAndTicketNo($user, $ticketNo);

        if (!$issue) {
            $this->addFlash('warning', 'Issue could not be found.');
            return $backToRoute;
        }

        return $this->render('dashboard/tickets/search.html.twig', [
            'ticketNo' => $ticketNo,
            'issue' => $issue
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\Ticket;

use App\Controller\Dashboard\AbstractDashboardController;
use App\Service\ProjectService;
use App\Service\TicketService;
use App\Service\UserService;
use App\Traits\FormValidationTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/ticket/search/p8k0w6u3q8w9l2x4')]
class SearchController extends AbstractDashboardController
{
    use FormValidationTrait;

    public function __construct(
        private readonly UserService    $userService,
        private readonly TicketService  $ticketService,
        private readonly ProjectService $projectService,
    ) {
    }

    #[Route('/q', name: 'app_dashboard_ticket_search', methods: ['GET', 'POST'])]
    public function index(Request $request): RedirectResponse|Response
    {
        $this->denyAccessUnlessGrantedRoleCustomer();
        $user = $this->getUser();

        $ticketNo = $this->validateNumber($request->request->get('keyword'));
        $backToRoute = $this->redirectToRoute($request->request->get('backTo'));

        $issue = $this->ticketService->getByTicketNo($ticketNo);

        if (!$issue) {
            $this->addFlash('warning', 'Issue could not be found.');
            return $backToRoute;
        }

        $isAdmin = $this->userService->isAdmin($user);
        $canViewIssue = false;

        if (!$isAdmin) {
            foreach ($this->projectService->getAllByCompany($user->getCompany()) as $project) {
                if ($project->getId() === $issue->getProject()->getId()) {
                    $canViewIssue = true;
                    break;
                }
            }
        }

        if (false === $canViewIssue && !$isAdmin) {
            $this->addFlash('warning', 'Issue could not be found. E-0004');
            return $backToRoute;
        }

        return $this->render('dashboard/tickets/search.html.twig', [
            'ticketNo' => $ticketNo,
            'issues' => [$issue]
        ]);
    }
}

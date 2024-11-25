<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\Ticket;

use App\Controller\Dashboard\AbstractDashboardController;
use App\Entity\Ticket;
use App\Service\ProjectService;
use App\Service\TicketLabelsService;
use App\Service\TicketService;
use App\Service\TicketStatusService;
use App\Service\TicketTypesService;
use App\Service\UserService;
use App\Traits\FormValidationTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/tickets/81o8i3h1v6lnp7xa')]
class IndexController extends AbstractDashboardController
{
    use FormValidationTrait;

    private const DASHBOARD_TICKETS_ROUTE = 'app_dashboard_tickets_index';

    public function __construct(
        private readonly UserService $userService,
        private readonly TicketService $ticketService,
        private readonly TicketTypesService $ticketTypesService,
        private readonly TicketLabelsService $ticketLabelsService,
        private readonly ProjectService  $projectService,
        private readonly TicketStatusService  $ticketStatusService,
    ){
    }

    #[Route('/home', name: 'app_dashboard_tickets_index')]
    public function index(): Response
    {
        $this->denyAccessUnlessGrantedRoleCustomer();

        $user = $this->getUser();

        $isAdmin = $this->userService->isAdmin($user);

        $issues = $isAdmin
            ? $this->ticketService->getAll()
            : $this->ticketService->getAllByCustomer($user);

        $ticketTypes = $this->ticketTypesService->getAll();

        $ticketLabels = $this->ticketLabelsService->getAll();

        $users = $this->userService->getAllEmployees();

        $projects = $isAdmin
            ? $this->projectService->getAll()
            : $this->projectService->getAllByCustomer($user);

        return $this->render('dashboard/tickets/index.html.twig', [
            'issues' => $issues,
            'ticketTypes' => $ticketTypes,
            'ticketLabels' => $ticketLabels,
            'users' => $users,
            'projects' => $projects,
        ]);
    }

    #[Route('/new', name: 'app_dashboard_ticket_new', methods: 'POST')]
    public function new(Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGrantedRoleCustomer();

        $user = $this->getUser();

        $title = $this->validate($request->request->get('title'));
        $type = $this->validateNumber($request->request->get('type'));
        $description = $this->validateTextarea($request->request->get('description'), true);
        $project = $this->validateNumber($request->request->get('project'));
        $label = $this->validateNumber($request->request->get('label'));

        if (!$title || $type <= 0 || !$description || $label <= 0 || $project <= 0) {
            $this->addFlash('warning', 'Fields with star are required.');
            return $this->redirectToRoute(self::DASHBOARD_TICKETS_ROUTE);
        }

        $project = $this->userService->isAdmin($user)
            ? $this->projectService->getById($project)
            : $this->projectService->getByCustomerAndId($user, $project);

        if (!$project) {
            $this->addFlash('warning', 'Project not found.');
            return $this->redirectToRoute(self::DASHBOARD_TICKETS_ROUTE);
        }

        $ticketType = $this->ticketTypesService->getById($type);

        if (!$ticketType) {
            $this->addFlash('warning', 'Ticket-Type not found.');
            return $this->redirectToRoute(self::DASHBOARD_TICKETS_ROUTE);
        }

        $ticketLabel = $this->ticketLabelsService->getById($label);

        if (!$ticketLabel) {
            $this->addFlash('warning', 'Ticket-Label not found.');
            return $this->redirectToRoute(self::DASHBOARD_TICKETS_ROUTE);
        }

        $assignee = $this->userService->getById(
            $this->validateNumber($request->request->get('assignee'))
        );

        if ($assignee && !$this->userService->isAdmin($assignee)) {
            $this->addFlash('warning', 'Assignee not found.');
            return $this->redirectToRoute(self::DASHBOARD_TICKETS_ROUTE);
        }

        $ticket = new Ticket();

        $ticketNo = $this->ticketService->getLatTicketNo();

        $this->ticketService->save(
            $ticket
                ->setTicketNo($ticketNo)
                ->setCustomer($project->getCustomer())
                ->setAssignee($assignee)
                ->setProject($project)
                ->setType($ticketType)
                ->setLabel($ticketLabel)
                ->setTitle($title)
                ->setDescription($description)
        );

        $this->addFlash('success', 'New issue has been added.');

        return $this->redirectToRoute(self::DASHBOARD_TICKETS_ROUTE);
    }
}

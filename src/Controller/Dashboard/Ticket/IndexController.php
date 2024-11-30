<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\Ticket;

use App\Controller\Dashboard\AbstractDashboardController;
use App\Entity\Ticket;
use App\Service\Core\FileUploaderService;
use App\Service\Core\MonologService;
use App\Service\ProjectService;
use App\Service\TicketActivitiesService;
use App\Service\TicketAttachmentsService;
use App\Service\TicketLabelsService;
use App\Service\TicketService;
use App\Service\TicketStatusService;
use App\Service\TicketTypesService;
use App\Service\UserService;
use App\Traits\FormValidationTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
        private readonly UserService             $userService,
        private readonly TicketService           $ticketService,
        private readonly TicketTypesService      $ticketTypesService,
        private readonly TicketLabelsService     $ticketLabelsService,
        private readonly ProjectService          $projectService,
        private readonly TicketStatusService     $ticketStatusService,
        private readonly TicketActivitiesService $ticketActivitiesService,
        private readonly TicketAttachmentsService $ticketAttachmentsService,
        private readonly MonologService          $monologService,
    ) {
    }

    #[Route('/status/{status?}', name: 'app_dashboard_tickets_index')]
    public function index(?string $status): Response
    {
        $this->denyAccessUnlessGrantedRoleCustomer();

        $user = $this->getUser();

        $isAdmin = $this->userService->isAdmin($user);

        $status = !empty($status) ? $this->validate(str_replace('-', ' ', $status)) : 'Open';

        $ticketStatus = $this->ticketStatusService->getOneByName($status);

        $issues = $isAdmin
            ? $this->ticketService->getAllByStatus($ticketStatus)
            : $this->ticketService->getAllByCustomerAndStatus($user, $ticketStatus);

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
            'status' => $status,
        ]);
    }

    #[Route('/new', name: 'app_dashboard_ticket_new', methods: 'POST')]
    public function new(Request $request, FileUploaderService $fileUploaderService): RedirectResponse
    {
        $this->denyAccessUnlessGrantedRoleCustomer();

        $user = $this->getUser();

        $title = $this->validate($request->request->get('title'));
        $type = $this->validateNumber($request->request->get('type'));
        $description = $this->validateTextarea($request->request->get('content'), true);
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
        $status = $this->ticketStatusService->getOneByName('Open');

        $this->ticketService->save(
            $ticket
                ->setTicketNo($ticketNo)
                ->setCustomer($project->getCustomer())
                ->setAssignee($assignee)
                ->setProject($project)
                ->setType($ticketType)
                ->setLabel($ticketLabel)
                ->setStatus($status)
                ->setTitle($title)
                ->setDescription($description)
        );

        /** @var UploadedFile $attachment */
        $attachment = $request->files->get('attachment');

        if ($attachment && $fileUploaderService->isExtensionAllowed($attachment)) {
            $originalFilename = $attachment->getClientOriginalName();
            $size = $attachment->getSize();
            $extension = $attachment->getClientOriginalExtension();
            $filename = $fileUploaderService->upload($attachment);

            if ($filename && $size && $extension) {
                $this->ticketAttachmentsService->create($ticket, $originalFilename, $filename, $size, $extension);
            }
        }

        $message = sprintf('Issue T-%s added by %s', $ticket->getTicketNo(), $user->getName());
        $this->ticketActivitiesService->add($ticket, $user, $message);

        // notify admins when customer add a new issue ..

        $this->addFlash('success', 'New issue has been added.');

        return $this->redirectToRoute(self::DASHBOARD_TICKETS_ROUTE);
    }

    #[Route('/show/{id}', name: 'app_dashboard_ticket_view')]
    public function view(?string $id): Response
    {
        $this->denyAccessUnlessGrantedRoleCustomer();

        $user = $this->getUser();

        $isAdmin = $this->userService->isAdmin($user);

        $id = $this->validateNumber($id);

        $issue = $isAdmin
            ? $this->ticketService->getById($id)
            : $this->ticketService->getOneByCustomerAndId($user, $id);

        if (!$issue) {
            $this->addFlash('warning', 'Issue could not be found.');
            return $this->redirectToRoute(self::DASHBOARD_TICKETS_ROUTE);
        }

        $ticketActivities = $this->ticketActivitiesService->getAllByTicket($issue);

        $attachments = $this->ticketAttachmentsService->getAllByTicket($issue);

        return $this->render('dashboard/tickets/view.html.twig', [
            'issue' => $issue,
            'ticketActivities' => $ticketActivities,
            'dir' => $this->getParameter('attachments'),
            'attachments' => $attachments,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_dashboard_ticket_edit')]
    public function edit(?string $id): Response
    {
        $this->denyAccessUnlessGrantedRoleCustomer();

        $user = $this->getUser();

        $isAdmin = $this->userService->isAdmin($user);

        $id = $this->validateNumber($id);

        $issue = $isAdmin
            ? $this->ticketService->getById($id)
            : $this->ticketService->getOneByCustomerAndId($user, $id);

        if (!$issue) {
            $this->addFlash('warning', 'Issue could not be found.');
            return $this->redirectToRoute(self::DASHBOARD_TICKETS_ROUTE);
        }

        $users = $this->userService->getAllEmployees();

        $statuses = $this->ticketStatusService->getAll();

        return $this->render('dashboard/tickets/edit.html.twig', [
            'issue' => $issue,
            'assignees' => $users,
            'statuses' => $statuses,
        ]);
    }

    #[Route('/store/{no}', name: 'app_dashboard_ticket_store', methods: ['POST'])]
    public function store(?string $no, Request $request): Response
    {
        $this->denyAccessUnlessGrantedRoleCustomer();

        $user = $this->getUser();
        $isAdmin = $this->userService->isAdmin($user);
        $issueId = $this->validateNumber($request->request->get('id'));

        $issue = $isAdmin
            ? $this->ticketService->getById($issueId)
            : $this->ticketService->getOneByCustomerAndId($user, $issueId);

        if (!$issue) {
            $this->addFlash('warning', 'Issue could not be found.');
            return $this->redirectToRoute(self::DASHBOARD_TICKETS_ROUTE);
        }

        $title = $this->validateTextarea($request->request->get('title'), true);
        $description = $this->validateTextarea($request->request->get('content'), true);

        $assignee = $isAdmin
            ? $this->userService->getById($this->validateNumber($request->request->get('assignee')))
            : $issue->getAssignee();

        $status = $isAdmin
            ? $this->ticketStatusService->getById($this->validateNumber($request->request->get('status')))
            : $issue->getStatus();

        $this->monologService->logger->info(
            sprintf(
                'issueUpdated: T-%s by %s [title: %s, description: %s, status: %s] to [title: %s, description: %s, status: %s]',
                $issue->getTicketNo(),
                $user->getUserIdentifier(),
                $issue->getTitle(),
                $issue->getDescription(),
                $issue->getStatus() ? $issue->getStatus()->getName() : 'Open',
                $title,
                $description,
                $status ? $status->getName() : 'Open'
            )
        );

        if ($title !== $issue->getTitle()) {
            $this->ticketActivitiesService->add(
                $issue,
                $user,
                sprintf(
                    '%s updated title of issue to "%s"', $user->getName(), $issue->getTitle()
                )
            );
        }

        if ($description !== $issue->getDescription()) {
            $this->ticketActivitiesService->add(
                $issue,
                $user,
                sprintf(
                    '%s updated description of issue to "%s"', $user->getName(), $issue->getDescription()
                )
            );
        }

        if ($issue->getStatus() && ($status->getName() !== $issue->getStatus()->getName())) {
            $this->ticketActivitiesService->add(
                $issue,
                $user,
                sprintf(
                    '%s updated status of issue to "%s"', $user->getName(), $status->getName()
                )
            );
        }

        $this->ticketService->save(
            $issue
                ->setTitle($title)
                ->setDescription($description)
                ->setAssignee($assignee)
                ->setStatus($status)
        );

        $this->addFlash('success', 'Issue has been saved.');

        return $this->redirectToRoute(self::DASHBOARD_TICKETS_ROUTE);
    }
}

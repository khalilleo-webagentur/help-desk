<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\Ticket;

use App\Controller\Dashboard\AbstractDashboardController;
use App\Controller\Dashboard\Dto\Search;
use App\Entity\Ticket;
use App\Helper\AppHelper;
use App\Service\CompanyService;
use App\Service\Core\FileUploaderService;
use App\Service\Core\MonologService;
use App\Service\Helper\TicketStatusHelper;
use App\Service\ProjectService;
use App\Service\TicketActivitiesService;
use App\Service\TicketAttachmentsService;
use App\Service\TicketCommentsService;
use App\Service\TicketLabelsService;
use App\Service\TicketService;
use App\Service\TicketStatusService;
use App\Service\TicketTypesService;
use App\Service\UserService;
use App\Traits\FormValidationTrait;
use DateTime;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/tickets/d7l6j0l5u9s2n8j8')]
class IndexController extends AbstractDashboardController
{
    use FormValidationTrait;

    private const DASHBOARD_TICKETS_ROUTE = 'app_dashboard_tickets_index';
    private const SEARCH_ROUTE = 'app_dashboard_ticket_search';

    public function __construct(
        private readonly UserService              $userService,
        private readonly TicketService            $ticketService,
        private readonly TicketTypesService       $ticketTypesService,
        private readonly TicketLabelsService      $ticketLabelsService,
        private readonly ProjectService           $projectService,
        private readonly TicketStatusService      $ticketStatusService,
        private readonly TicketActivitiesService  $ticketActivitiesService,
        private readonly TicketAttachmentsService $ticketAttachmentsService,
        private readonly TicketCommentsService    $ticketCommentsService,
        private readonly CompanyService           $companyService,
        private readonly MonologService           $monologService,
    ) {
    }

    #[Route('/status/{status?}', name: 'app_dashboard_tickets_index')]
    public function index(?string $status, TicketStatusHelper $ticketStatusHelper): Response
    {
        $this->denyAccessUnlessGrantedRoleCustomer();

        $user = $this->getUser();

        $isAdmin = $this->userService->isAdmin($user) || $user->isNinja();

        $status = !empty($status) ? mb_strtoupper($this->validate($status)) : AppHelper::STATUS_OPEN;

        $ticketStatus = $this->ticketStatusService->getOneByName($status);

        $issues = $isAdmin
            ? $this->ticketService->getAllByStatus($ticketStatus)
            : $this->ticketService->getAllByCompanyAndStatus($user->getCompany(), $ticketStatus);

        $ticketTypes = $this->ticketTypesService->getAll();

        $ticketLabels = $this->ticketLabelsService->getAll();

        $projects = $isAdmin
            ? $this->projectService->getAll()
            : $this->projectService->getAllByCompany($user->getCompany());

        $companies = $isAdmin ? $this->companyService->getAll() : [];
        $statuses = $isAdmin ? $this->ticketStatusService->getAll() : [];
        $assigners = $this->userService->getAllByCompany($user->getCompany());

        $dateTime = new DateTime();

        $tabs = $ticketStatusHelper->getTabs($user);

        return $this->render('dashboard/tickets/index.html.twig', [
            'issues' => $issues,
            'ticketTypes' => $ticketTypes,
            'ticketLabels' => $ticketLabels,
            'projects' => $projects,
            'status' => $status,
            'assigners' => $assigners,
            'search' => new Search(true, self::SEARCH_ROUTE, self::DASHBOARD_TICKETS_ROUTE),
            'companies' => $companies,
            'statuses' => $statuses,
            'dateTimeFrom' => $dateTime->modify('-1 month')->format('Y-m-d'),
            'dateTimeTo' => (new DateTime())->format('Y-m-d'),
            'tabs' => $tabs,
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

        $isAdmin = $this->userService->isAdmin($user);

        $project = $isAdmin || $user->isNinja()
            ? $this->projectService->getById($project)
            : $this->projectService->getByCompanyAndId($user->getCompany(), $project);

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

        if ($assignee && !$this->userService->isAdmin($assignee) && !$user->isNinja()) {
            $this->addFlash('warning', 'Assignee not found.');
            return $this->redirectToRoute(self::DASHBOARD_TICKETS_ROUTE);
        }

        $ticket = new Ticket();

        $ticketNo = $this->ticketService->getLatTicketNo();
        $status = $this->ticketStatusService->getOneByName(AppHelper::STATUS_OPEN);

        $customer = $isAdmin
            ? $this->userService->getOneByCompany($project->getCompany())
            : $user;

        $this->ticketService->save(
            $ticket
                ->setTicketNo($ticketNo)
                ->setCustomer($customer)
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
                $this->ticketAttachmentsService->create($user, $ticket, $originalFilename, $filename, $size, $extension);
            }
        }

        $message = sprintf('Issue T-%s added by %s', $ticket->getTicketNo(), $user->getName());
        $this->ticketActivitiesService->add($ticket, $user, $message);

        // notify admins when customer add a new issue ..

        $this->addFlash('success', 'New issue has been added.');

        return $this->redirectToRoute(self::DASHBOARD_TICKETS_ROUTE);
    }

    #[Route('/show/{id}', name: 'app_dashboard_ticket_view')]
    public function view(?string $id, Request $request): Response
    {
        $this->denyAccessUnlessGrantedRoleCustomer();
        $user = $this->getUser();
        $isAdmin = $this->userService->isAdmin($user) || $user->isNinja();
        $id = $this->validateNumber($id);
        $projectId = $this->validateNumber($request->get('pid'));

        $project = $this->projectService->getById($projectId);

        if (!$project) {
            $this->addFlash('warning', 'Issue could not be found.');
            return $this->redirectToRoute(self::DASHBOARD_TICKETS_ROUTE);
        }

        $issue = $isAdmin
            ? $this->ticketService->getById($id)
            : $this->ticketService->getOneByProjectAndId($project, $id);

        if (!$issue) {
            $this->addFlash('warning', 'Issue could not be found.');
            return $this->redirectToRoute(self::DASHBOARD_TICKETS_ROUTE);
        }

        $ticketActivities = $this->ticketActivitiesService->getAllByTicket($issue);
        $attachments = $this->ticketAttachmentsService->getAllByTicket($issue);

        $comments = [];

        foreach ($issue->getTicketComments() as $comment) {
            $comments[] = [
                'id' => $comment->getId(),
                'description' => $comment->getDescription(),
                'isSeen' => $comment->isSeen(),
                'seenAt' => $comment->getSeenAt(),
                'likeCounter' => $comment->getLikeCounter(),
                'disLikeCounter' => $comment->getDisLikeCounter(),
                'updatedAt' => $comment->getUpdatedAt(),
                'createdAt' => $comment->getCreatedAt(),
                'commentedByName' => $comment->getCommentedBy()->getValues()[0]->getName(),
            ];
        }

        $statuses = $isAdmin ? $this->ticketStatusService->getAll() : [];

        return $this->render('dashboard/tickets/view.html.twig', [
            'issue' => $issue,
            'ticketActivities' => $ticketActivities,
            'dir' => $this->getParameter('attachments'),
            'statuses' => $statuses,
            'attachments' => $attachments,
            'comments' => $comments
        ]);
    }

    #[Route('/edit/{id}', name: 'app_dashboard_ticket_edit')]
    public function edit(?string $id, Request $request): Response
    {
        $this->denyAccessUnlessGrantedRoleCustomer();

        $projectId = $this->validateNumber($request->get('pid'));

        $project = $this->projectService->getById($projectId);

        if (!$project) {
            $this->addFlash('warning', 'Issue could not be found.');
            return $this->redirectToRoute(self::DASHBOARD_TICKETS_ROUTE);
        }

        $user = $this->getUser();

        $isAdmin = $this->userService->isAdmin($user);

        $id = $this->validateNumber($id);

        $issue = $isAdmin
            ? $this->ticketService->getById($id)
            : $this->ticketService->getOneByProjectAndId($project, $id);

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

        $projectId = $this->validateNumber($request->get('pid'));

        $project = $this->projectService->getById($projectId);

        if (!$project) {
            $this->addFlash('warning', 'Issue could not be found.');
            return $this->redirectToRoute(self::DASHBOARD_TICKETS_ROUTE);
        }

        $user = $this->getUser();
        $isAdmin = $this->userService->isAdmin($user);
        $issueId = $this->validateNumber($request->request->get('id'));

        $issue = $isAdmin || $user->isNinja()
            ? $this->ticketService->getById($issueId)
            : $this->ticketService->getOneByProjectAndId($project, $issueId);

        if (!$issue) {
            $this->addFlash('warning', 'Issue could not be found.');
            return $this->redirectToRoute(self::DASHBOARD_TICKETS_ROUTE);
        }

        $title = $this->validateTextarea($request->request->get('title'), true);
        $description = $this->validateTextarea($request->request->get('content'), true);

        $assignee = $isAdmin || $user->isNinja()
            ? $this->userService->getById($this->validateNumber($request->request->get('assignee')))
            : $issue->getAssignee();

        $status = $isAdmin || $user->isNinja()
            ? $this->ticketStatusService->getById($this->validateNumber($request->request->get('status')))
            : $issue->getStatus();

        $timeSpentInMinutes = $isAdmin || $user->isNinja()
            ? $this->validateNumber($request->request->get('minutes'))
            : $issue->getTimeSpentInMinutes();

        $this->monologService->logger->info(
            sprintf(
                'issueUpdated: T-%s by %s [title: %s, description: %s, status: %s] to [title: %s, description: %s, status: %s]',
                $issue->getTicketNo(),
                $user->getUserIdentifier(),
                $issue->getTitle(),
                $issue->getDescription(),
                $issue->getStatus() ? $issue->getStatus()->getName() : AppHelper::STATUS_OPEN,
                $title,
                $description,
                $status ? $status->getName() : AppHelper::STATUS_OPEN
            )
        );

        if ($timeSpentInMinutes != $issue->getTimeSpentInMinutes()) {
            $this->ticketActivitiesService->add(
                $issue,
                $user,
                sprintf(
                    '%s logged time spent of issue "%s" minutes', $user->getName(), $timeSpentInMinutes
                )
            );
        }

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
                    '%s updated description of issue form: [%s] to [%s]',
                    $user->getName(),
                    strip_tags($issue->getDescription()),
                    strip_tags($description)
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
                ->setTimeSpentInMinutes($issue->getTimeSpentInMinutes() + $timeSpentInMinutes)
        );

        $this->addFlash('success', 'Issue has been saved.');

        return $this->redirectToRoute(self::DASHBOARD_TICKETS_ROUTE);
    }
}

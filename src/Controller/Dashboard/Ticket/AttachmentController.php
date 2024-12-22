<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\Ticket;

use App\Controller\Dashboard\AbstractDashboardController;
use App\Service\Core\FileHandlerService;
use App\Service\Core\FileUploaderService;
use App\Service\ProjectService;
use App\Service\TicketActivitiesService;
use App\Service\TicketAttachmentsService;
use App\Service\TicketService;
use App\Service\UserService;
use App\Traits\FormValidationTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/ticket/attachment/e4d1r3o2h6o6g6m3')]
class AttachmentController extends AbstractDashboardController
{
    use FormValidationTrait;

    private const DASHBOARD_TICKETS_ROUTE = 'app_dashboard_tickets_index';

    public function __construct(
        private readonly UserService              $userService,
        private readonly TicketService            $ticketService,
        private readonly TicketAttachmentsService $ticketAttachmentsService,
        private readonly TicketActivitiesService  $ticketActivitiesService,
        private readonly ProjectService           $projectService,
    ) {
    }

    #[Route('/upload-new-file', name: 'app_dashboard_ticket_helper_upload', methods: 'POST')]
    public function upload(Request $request, FileUploaderService $fileUploaderService): RedirectResponse
    {
        $this->denyAccessUnlessGrantedRoleCustomer();

        $user = $this->getUser();

        $isAdmin = $this->userService->isAdmin($user);

        $id = $this->validateNumber($request->request->get('tId'));

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

        /** @var UploadedFile $attachment */
        $attachment = $request->files->get('attachment');

        if ($attachment && $fileUploaderService->isExtensionAllowed($attachment)) {
            $originalFilename = $attachment->getClientOriginalName();
            $size = $attachment->getSize();
            $extension = $attachment->getClientOriginalExtension();
            $filename = $fileUploaderService->upload($attachment);

            if ($filename && $size && $extension) {
                $this->ticketAttachmentsService->create($user, $issue, $originalFilename, $filename, $size, $extension);

                $message = sprintf('Attachment %s added by %s', $originalFilename, $user->getName());
                $this->ticketActivitiesService->add($issue, $user, $message);

                $this->addFlash('success', 'File uploaded successfully.');
            }
        }

        return $this->redirectToRoute(self::DASHBOARD_TICKETS_ROUTE);
    }

    #[Route('/file/{hash}', name: 'app_dashboard_ticket_helper_show', methods: ['GET', 'POST'])]
    public function view(?string $hash, Request $request, FileHandlerService $fileHandlerService): Response
    {
        if ($request->getMethod() === 'GET') {
            $this->addFlash('notice', 'The file that you are looking for could not be found.');
            return $this->redirectToRoute('app_home');
        }

        $this->denyAccessUnlessGrantedRoleCustomer();

        $projectId = $this->validateNumber($request->get('pid'));

        $project = $this->projectService->getById($projectId);

        if (!$project) {
            $this->addFlash('warning', 'Issue could not be found.');
            return $this->redirectToRoute(self::DASHBOARD_TICKETS_ROUTE);
        }

        $user = $this->getUser();

        $isAdmin = $this->userService->isAdmin($user);

        $id = $this->validateNumber($request->request->get('tId'));

        $issue = $isAdmin
            ? $this->ticketService->getById($id)
            : $this->ticketService->getOneByProjectAndId($project, $id);

        if (!$issue) {
            $this->addFlash('warning', 'Issue could not be found.');
            return $this->redirectToRoute(self::DASHBOARD_TICKETS_ROUTE);
        }

        $attachmentId = $this->validateNumber($request->request->get('aId'));

        $attachment = $this->ticketAttachmentsService->getOneByTicketAndId($issue, $attachmentId);

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

    #[Route('/delete/{hash}', name: 'app_dashboard_ticket_helper_delete_file', methods: 'POST')]
    public function delete(Request $request): RedirectResponse
    {
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

        $id = $this->validateNumber($request->request->get('aId'));
        $attachment = $this->ticketAttachmentsService->getOneByTicketAndId($issue, $id);

        if (!$attachment) {
            $this->addFlash('warning', 'Attachment could not be found.');
            return $this->redirectToRoute(self::DASHBOARD_TICKETS_ROUTE);
        }

        if ($user->getId() !== $attachment->getUser()->getId()) {
            $this->addFlash('warning', 'You have not enough permission to delete the Attachment from others.');
            return $this->redirectToRoute(self::DASHBOARD_TICKETS_ROUTE);
        }

        $this->ticketAttachmentsService->delete($attachment);

        $message = sprintf('Attachment %s removed by %s', $attachment->getOriginalFileName(), $user->getName());
        $this->ticketActivitiesService->add($issue, $user, $message);

        $this->addFlash('success', 'Attachment has been deleted.');

        return $this->redirectToRoute(self::DASHBOARD_TICKETS_ROUTE);
    }
}

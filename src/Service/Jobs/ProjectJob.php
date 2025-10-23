<?php

declare(strict_types=1);

namespace App\Service\Jobs;

use App\Entity\Project;
use App\Helper\AppHelper;
use App\Service\ProjectService;
use App\Service\SystemLogsService;
use App\Service\TicketActivitiesService;
use App\Service\TicketAttachmentsService;
use App\Service\TicketCommentsService;
use App\Service\TicketService;
use Doctrine\ORM\EntityManager;
use Exception;

/**
 * Delete Tickets and related Data
 *  - Comments
 *  - Attachments
 *  - Activities
 *  - Unlink other Ticket
 *
 * Delete Project
 */
final readonly class ProjectJob
{
    public function __construct(
        private TicketService            $ticketService,
        private ProjectService           $projectService,
        private TicketAttachmentsService $ticketAttachmentsService,
        private TicketActivitiesService  $ticketActivitiesService,
        private TicketCommentsService    $ticketCommentsService,
        private SystemLogsService        $systemLogsService,
    ) {
    }

    public function deleteEverythingRelatedToProject(Project $project, EntityManager $entityManager): bool
    {
        $entityManager->getConnection()->beginTransaction();

        try {
            // Delete all Tickets related to this project
            foreach ($this->ticketService->getAllByProject($project) as $ticket) {

                // Unlink other Ticket to this Ticket
                if ($targetTicktNo = $ticket->getLinkToTicket()) {
                    $this->ticketService->unLinkIssuesBySourceIssueNo((int)$targetTicktNo, false);
                }

                // Delete Attachments to this Ticket if any
                $this->ticketAttachmentsService->deleteAllByTicket($ticket, false);

                // Delete Log-activities to this Ticket if any
                $this->ticketActivitiesService->deleteAllByTicket($ticket, false);

                // Delete Comments to this Ticket if any
                $this->ticketCommentsService->deleteAllByTicket($ticket, false);

                // Delete Ticket
                $this->ticketService->delete($ticket, false);
            }

            // Delete Project itself
            $this->projectService->delete($project, false);

            // Flush and commit
            $entityManager->flush();
            $entityManager->getConnection()->commit();

            return true;

        } catch (Exception $e) {
            if ($entityManager->getConnection()->isConnected()) {
                $entityManager->getConnection()->rollBack();
                $message = sprintf(
                    'An exception was thrown while attempting to delete the project. Ex: %s',
                    $e->getMessage()
                );
                $this->systemLogsService->create(AppHelper::SYSTEM_LOG_CRITICAL, $message);
            }
        }

        return false;
    }
}
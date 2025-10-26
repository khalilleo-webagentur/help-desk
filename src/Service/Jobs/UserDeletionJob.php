<?php

declare(strict_types=1);

namespace App\Service\Jobs;

use App\Entity\User;
use App\Helper\AppHelper;
use App\Service\SystemLogsService;
use App\Service\TicketService;
use App\Service\UserService;
use App\Service\UserSettingService;
use Doctrine\ORM\EntityManager;
use Exception;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Delete user and all related Data
 * - Delete user setting
 * - Delete user
 */
final readonly class UserDeletionJob
{
    public function __construct(
        private UserService        $userService,
        private UserSettingService $userSettingService,
        private TicketService      $ticketService,
        private SystemLogsService  $systemLogsService
    ) {
    }

    public function deleteEverythingRelatedToUser(User|UserInterface $user, EntityManager $entityManager): bool
    {
        $entityManager->getConnection()->beginTransaction();
        $isDoneWithoutAnyError = false;

        try {
            // Make sure that the user has no ticket
            if ($this->ticketService->getOneByCustomer($user)) {
                $message = sprintf(
                    'User deletion job: User #%s cannot be deleted since user has created Tickets.',
                    $user->getId()
                );
                $this->systemLogsService->create(AppHelper::SYSTEM_LOG_EVENT_INFO, $message);
                $entityManager->flush();
                $entityManager->getConnection()->commit();
                return false;
            }

            // Make sure that the user has no Assigned issue
            if ($this->ticketService->getOneByAssignee($user)) {
                $message = sprintf(
                    'User deletion job: User #%s cannot be deleted since user has assigned Issues.',
                    $user->getId()
                );
                $this->systemLogsService->create(AppHelper::SYSTEM_LOG_EVENT_INFO, $message);
                $entityManager->flush();
                $entityManager->getConnection()->commit();
                return false;
            }

            // Delete user setting
            $this->userSettingService->delete($user, false);

            // Delete user
            $this->userService->delete($user, false);

            // Flush and commit
            $entityManager->flush();
            $entityManager->getConnection()->commit();

            $isDoneWithoutAnyError = true;

        } catch (Exception $e) {
            if ($entityManager->getConnection()->isTransactionActive()) {
                $entityManager->getConnection()->rollBack();
                $message = sprintf(
                    'An exception was thrown while attempting to delete the user. Ex: %s',
                    $e->getMessage()
                );
                $this->systemLogsService->create(AppHelper::SYSTEM_LOG_CRITICAL, $message);
            }
        }

        return $isDoneWithoutAnyError;
    }
}
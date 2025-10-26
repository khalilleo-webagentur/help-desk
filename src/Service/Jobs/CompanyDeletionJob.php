<?php

declare(strict_types=1);

namespace App\Service\Jobs;

use App\Entity\Company;
use App\Helper\AppHelper;
use App\Service\CompanyService;
use App\Service\ProjectService;
use App\Service\SystemLogsService;
use App\Service\UserService;
use App\Service\UserSettingService;
use Doctrine\ORM\EntityManager;
use Exception;

/**
 * Delete company and all related Data
 * - All projects
 * - All tickets associated with a project
 * - All attachments related to a ticket
 * - All user settings
 * - All users
 */
final readonly class CompanyDeletionJob
{
    public function __construct(
        private UserService        $userService,
        private UserSettingService $userSettingService,
        private CompanyService     $companyService,
        private ProjectService     $projectService,
        private SystemLogsService  $systemLogsService,
        private ProjectDeletionJob $projectDeletionJob,
    ) {
    }

    public function deleteEverythingRelatedToCompany(Company $company, EntityManager $entityManager): bool
    {
        $entityManager->getConnection()->beginTransaction();
        $isDoneWithoutAnyError = false;

        try {

            foreach ($this->projectService->getAllByCompany($company) as $project) {
                $this->projectDeletionJob->deleteEverythingRelatedToProject($project, $entityManager);
            }

            // Delete Users related to this company
            $users = $this->userService->getAllByCompany($company);
            foreach ($users as $user) {
                $this->userSettingService->delete($user, false);
                $this->userService->delete($user, false);
            }

            // Delete Company itself
            $this->companyService->delete($company, false);

            // Flush and commit
            $entityManager->flush();
            $entityManager->getConnection()->commit();

            $isDoneWithoutAnyError = true;

        } catch (Exception $e) {
            if ($entityManager->getConnection()->isConnected()) {
                $entityManager->getConnection()->rollBack();
                $message = sprintf(
                    'An exception was thrown while attempting to delete the company. Ex: %s',
                    $e->getMessage()
                );
                $this->systemLogsService->create(AppHelper::SYSTEM_LOG_CRITICAL, $message);
            }
        }

        return $isDoneWithoutAnyError;
    }
}